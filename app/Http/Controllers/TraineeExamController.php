<?php

namespace App\Http\Controllers;

use Auth;
use App\Dealer;
use App\QuestionDetail;
use App\TraineeQuestion;
use App\TraineeChoice;
use App\TraineeExam;
use App\Models\Question;
use App\ExamDetail;
use App\Trainee;
use App\UserAccess;

use App\Services\Exam;
use App\Services\EmailService;
use App\Services\HistoryService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraineeExamController extends Controller
{
	protected $user_id;
	protected $dealer;
	protected $exam;

	public function __construct(Exam $exam)
	{
		/**
		 * I've to put Auth details in "middleware"
		 * because sessions and authenticated users in controller's constructor
		 * isn't running yet.
		 */
		$this->middleware(function ($request, $next) {
			$this->user_id = str_replace_last(Auth::user()->user_type . '_', '', Auth::user()->app_user_id);

			$this->dealer = DB::table('dealers as d')
				->select('d.*')
				->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'd.dealer_id')
				->leftJoin('trainees as ts', 'ts.trainor_id', '=', 'trs.trainor_id')
				->where([
					'ts.trainee_id' => $this->user_id
				])
				->first();

            return $next($request);
		});

		$this->exam = $exam;
	}

	public function list_of_exams($trainee_id)
	{
		$current_date = Carbon::now()->toDateString();

		$exams = DB::table('exam_details as ed')
			->select(
				'ed.*',
				'm.module',
				'm.description',
				DB::raw('SUM(qd.items) as items'),
				'es.timer'
			)
			->leftJoin('trainees as trainee', 'trainee.dealer_id', '=', 'ed.dealer_id')
			->leftJoin('exam_schedules as es', 'es.exam_schedule_id', '=', 'ed.exam_schedule_id')

			->leftJoin('modules as m', 'm.module_id', '=', 'es.module_id')
			->leftJoin('question_details as qd', 'qd.exam_schedule_id', '=', 'es.exam_schedule_id')

			->where([
				'trainee.trainee_id' => $trainee_id,
				'ed.is_enabled'       => 1,
				'ed.status'           => 'on_progress'
			])
			->where('ed.end_date', '>=', $current_date)
			->where('ed.start_date', '<=', $current_date)
			->groupBy('exam_detail_id')
			->orderBy('es.created_at', 'ASC')
			->get();

		/**
		 * Exclude exam if 0 remaining time.
		 * Better to use it on small data arrays.
		 */

		$data = [];
		foreach ($exams as $key => $value) {
			$data[$key] = $value;

			$query = DB::table('trainee_exams')
				->where([
					'exam_schedule_id' => $value->exam_schedule_id,
					'trainee_id'       => $trainee_id,
					'remaining_time'   => 0,
					'seconds'          => 0
				])->exists();

			if ($query) unset($data[$key]);
		}

		return response()->json(array_values($data));
	}

	public function exam_content($exam_detail_id)
	{
		try {
			DB::beginTransaction();

			$header = $this->exam->details($exam_detail_id, $this->user_id);

			$exists = DB::table('trainee_exams')
				->where([
					'exam_schedule_id' => $header->exam_schedule_id,
					'trainee_id'       => $this->user_id,
				])->exists();

			if (!$exists) {
				$question_details = DB::table('question_details')
					->where('exam_schedule_id', $header->exam_schedule_id)
					->get();

				$randomized_questions = $this->exam->get_questions($question_details); //--> Get questions in random orders

				$created = $this->exam->fill_trainee_exam([
					'exam_schedule_id' => $header->exam_schedule_id,
					'trainee_id'       => $this->user_id,
					'remaining_time'   => $header->timer
				]);

				$item_no = 1;
				foreach ($randomized_questions as $key => $value) {
					$tq                  = new TraineeQuestion;
					$tq->trainee_exam_id = $created->trainee_exam_id;
					$tq->number          = $item_no;
					$tq->question_id     = $value['question_id'];
					$tq->choice_id       = 0;
					$tq->save();

					$this->exam->create_choices($tq->trainee_question_id, $tq->question_id);

					$item_no++;
				}
			}

			DB::commit();
			return response()->json($header);
		}
		catch(Exception $ex) {
			report($ex);

			DB::rollBack();
		}
	}

	public function trigger_exam($exam_detail_id, EmailService $batch_email, HistoryService $history)
	{
		$query = DB::table('exam_details')
			->where('exam_detail_id', $exam_detail_id)
			->update([
				'is_opened' => 1
			]);

		if ($query) {
			$dealer = Dealer::select('dealers.*')
				->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'dealers.dealer_id')
				->leftJoin('trainees as ts', 'ts.trainor_id', '=', 'trs.trainor_id')
				->where([
					'ts.trainee_id' => $this->user_id
				])
				->first();

			$user_access = UserAccess::select('et.email')
				->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
				->where([
					'system_id'    => config('app.system_id'),
					'user_type_id' => 2
				])
				->get();

			foreach ($user_access as $value) {
				$batch_email->batch_incoming_emails([
					'email_category' => 'opened',
					'subject'        => 'Initialized Examination',
					'sender'         => config('mail.from.address'),
					'recipient'      => $value['email'],
					'title'          => 'Examination <span style="color: #5caad2;">Initiated!</span>',
					'message'        => $this->dealer->dealer_name . ' of <strong>'.$this->dealer->branch.'</strong> has been initiated the examination. <br>
					Please click the button to navigate directly to our system.',
					'cc'             => null,
					'attachment'     => null
				]);
			}
		}

		return $query;
	}

	// Examination
	public function question(Request $request)
	{
		$question = TraineeExam::select(
			'trainee_exams.trainee_exam_id',
			'trainee_exams.exam_schedule_id',
			'trainee_exams.created_at',
			'trainee_exams.updated_at',
			'trainee_exams.remaining_time',
			'trainee_exams.seconds',
			'tq.trainee_question_id',
			'tq.number',
			'q.question',
			'tq.choice_id',
			'tq.question_id'
		)
		->leftJoin('trainees as t', 't.trainee_id', '=', 'trainee_exams.trainee_id')
		->leftJoin('trainee_questions as tq', 'tq.trainee_exam_id', '=', 'trainee_exams.trainee_exam_id')
		->leftJoin('questions as q', 'q.question_id', '=', 'tq.question_id')
		->where([
			'trainee_exams.exam_schedule_id' => $request->exam_schedule_id,
			'trainee_exams.trainee_id'       => $request->trainee_id,
			'tq.number'                      => $request->number,
		])
		->first();

		$choices = TraineeChoice::where('trainee_question_id', $question->trainee_question_id)->get();

		return response()->json([
			'questions' => $question,
			'choices'   => $choices
		]);
	}

	public function answer(Request $request, $trainee_question_id)
	{
		$answer = TraineeQuestion::where('trainee_question_id', $trainee_question_id)
			->update([
				'choice_id' => $request->choice_id
			]);

		return $answer;
	}

	public function timers_up(
		Request $request,
		$trainee_id,
		EmailService $batch_email,
		HistoryService $history,
		Exam $exam)
	{

		/**
		 * Update minutes and seconds on trainee_exams
		 */
		$query = TraineeExam::with('trainee.trainor')
			->where([
				['trainee_id', '=', $trainee_id],
				['exam_schedule_id', '=', $request->exam_schedule_id]
			])->first();

		$query->remaining_time = 0;
		$query->seconds = 0;
		$query->save();

		if ($query) {
			$exam_details = $this->exam->details($request->exam_detail_id, $this->user_id); // exam_detail_id
			$saved_history = $history->save_training_history([
				'module_id'        => $exam_details->module_id,
				'exam_schedule_id' => $exam_details->exam_schedule_id,
				'exam_detail_id'   => $exam_details->exam_detail_id,
				'trainee_exam_id'  => $query->trainee_exam_id,
				'dealer_id'        => $exam_details->dealer_id,
				'trainee_id'       => $exam_details->trainee_id,
				'score'            => $exam->final_score($query->trainee_exam_id)->score,
				'result'           => $exam->final_score($query->trainee_exam_id)->score >= $exam_details->passing_score ? 'passed' : 'failed',   //--> not sure
				'date_taken'       => $query->created_at
			]);

			if ($saved_history)
				return response()->json($this->update_exam_schedule_status($request->exam_schedule_id, $batch_email));
		}	
	}

	public function save_time(Request $request)
	{
		$query = DB::table('trainee_exams')
			->where([
				'trainee_id'        => $this->user_id,
				'exam_schedule_id'  => $request->exam_schedule_id
			])
			->update([
				'remaining_time' => $request->minutes,
				'seconds'        => $request->seconds
			]);

		return response()->json($query);
	}

	/**
	 * @param ["exam_schedule_id", "trainee_id"]
	 * Return boolean
	 */
	public function has_blank_answer(Request $request)
	{
		$query = DB::table('trainee_questions as tq')
			->leftJoin('trainee_exams as te', 'te.trainee_exam_id', '=', 'tq.trainee_exam_id')
			->where([
				'te.exam_schedule_id' => $request->exam_schedule_id,
				'te.trainee_id' 	  => $request->trainee_id,
				'tq.choice_id'		  => 0
			])->exists();

		return response()->json($query);
	}

	public function update_exam_schedule_status($exam_schedule_id, $batch_email)
	{
		$updated_exam_status = DB::table('trainee_exams as te')
			->where([
				['te.remaining_time', '<=', 0],
				['te.seconds', '<=', 0],
				['te.exam_schedule_id', '=', $exam_schedule_id]
			])
			->count();
		
		$total_trainees = $this->total_trainees($exam_schedule_id);

		if ($updated_exam_status == $total_trainees) {
			/**
			 * Completed
			 * Update exam_schedules->status
			 * save batch email to admin
			 */
			$query = DB::table('exam_schedules')
				->where('exam_schedule_id', $exam_schedule_id)
				->update(['status' => 'completed']);

			if ($query) {
				$user_access = UserAccess::select('et.email')
					->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
					->where([
						'system_id'    => config('app.system_id'),
						'user_type_id' => 2
					])
					->get();

				foreach ($user_access as $value) {
					$batch_email->batch_incoming_emails([
						'email_category' => 'finish',
						'subject'        => 'Finished Examination',
						'sender'         => config('mail.from.address'),
						'recipient'      => $value['email'],
						'title'          => 'Examination <span style="color: #5caad2;">Done!</span>',
						'message'        => 'All Trainess of <strong>'.$this->dealer->dealer_name.' | '.$this->dealer->branch.'</strong> has been successfully finished the examination. <br>
						Please click the button to navigate directly to your system.',
						'cc'             => null,
						'attachment'     => null
					]);
				}
			}
		}
	}

	public function total_trainees($exam_schedule_id)
	{
		return DB::table('exam_details as ed')
			->leftJoin('trainees as ts', 'ts.dealer_id', '=', 'ed.dealer_id')
			->where('ed.exam_schedule_id', $exam_schedule_id)
			->count();
	}

	public function update_choice_answered(Request $params)
	{
		try {
			DB::beginTransaction();

			$query1 = DB::table('trainee_choices')
				->where([
					'trainee_question_id' => $params->trainee_question_id
				])
				->update(['is_answered' => 0]);

			$query2 = DB::table('trainee_choices')
				->where([
					'trainee_question_id' => $params->trainee_question_id,
					'choice_id'           => $params->choice_id
				])
				->update(['is_answered' => 1]);

			DB::commit();
			return response()->json($query2);
		} catch (Exception $ex) {
			throw new Exception("Error Processing Request", $ex);

			DB::rollBack();
		}
	}
}
