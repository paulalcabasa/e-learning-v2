<?php

namespace App\Http\Controllers;

use App\Services\EmailService;
use App\ExamSchedule;
use App\Services\UpdateStatus;
use App\Http\Requests\ExamScheduleValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ExamScheduleController extends Controller
{
	public function __construct(UpdateStatus $status)
    {
        $status->update_exam_detail_status();
	}
	
	public function index()
	{
		$exam_schedule = new ExamSchedule;
		$exam_schedules = $exam_schedule->getSchedule(session('employee_id'));
		return response()->json(['exam_schedules' => $exam_schedules]);
	}

	public function show($exam_schedule_id)
	{
		$exam_schedule = ExamSchedule::findOrFail($exam_schedule_id)
			->with('exam_details')
			->with('module')
			->with('question_details')
			->where('exam_schedule_id', $exam_schedule_id)
			->first();
		
		return response()->json($exam_schedule);
	}

	public function store(Request $request, EmailService $batch_email)
	{
		$es_data = $request->exam_schedule;
		$ed_data = $request->exam_details;
		$qd_data = $request->question_details;

		try {
			DB::beginTransaction();
			$exam_schedule                = new ExamSchedule;
			$exam_schedule->module_id     = $es_data['module_id'];
			$exam_schedule->status        = $es_data['status'];
			$exam_schedule->timer         = $es_data['timer'];
			$exam_schedule->passing_score = $es_data['passing_score'];
			$exam_schedule->created_by    = $es_data['created_by'];
			$exam_schedule->save();

			$exam_schedule_id = $exam_schedule['exam_schedule_id'];
			
			if ($exam_schedule) {
				foreach ($qd_data as $value) {
					if (isset($value['isSelected'])) {
						DB::table('question_details')
							->insert([
								'exam_schedule_id' => $exam_schedule_id,
								'sub_module_id'    => $value['sub_module_id'],
								'items'            => $value['items']
							]);
					}
				}

				$dealer_ids = [];
				foreach ($ed_data as $value) {
					$dealer_ids[] = $value['dealer_id'];
					DB::table('exam_details')
						->insert([
							'exam_schedule_id' => $exam_schedule_id,
							'dealer_id'        => $value['dealer_id'],
							'start_date'       => $value['start_date'],
							'end_date'         => $value['end_date']
						]);
				}

				$emails = DB::table('trainors')->select('email')->whereIn('dealer_id', $dealer_ids)->get();
				foreach ($emails as $value) {
					$batch_email->batch_incoming_emails([
						'email_category' => 'creation',
						'subject'        => 'New Exam Schedule',
						'sender'         => config('mail.from.address'),
						'recipient'      => $value->email, // should be trainor
						'title'          => 'You have new <span style="color: #5caad2;">Exam Schedule!</span>',
						'message'        => 'Good Day! <strong>IPC Administration</strong> has been created a new schedule for a examination. <br>
						Please click the button to navigate directly to your system.',
						'cc'             => null,
						'attachment'     => null
					]);
				}
			}

			DB::commit();
			return response('Created', 201);
		}
		catch(Exception $ex) {
			DB::rollBack();
			return response('Bad Request', 400);
		}
	}

	public function update(ExamScheduleValidation $request, $exam_schedule_id)
	{
		$exam_schedule = ExamSchedule::findOrFail($exam_schedule_id);
		$exam_schedule->module_id = $request->module_id;
		$exam_schedule->status = $request->status;
		$exam_schedule->created_by = $request->created_by;
		$exam_schedule->save();

		return response()->json($exam_schedule);
	}

	public function update_timer(Request $request, $exam_schedule_id)
	{
		$exam_schedule = ExamSchedule::findOrFail($exam_schedule_id);
		$exam_schedule->timer = $request->timer;
		$exam_schedule->save();

		return response()->json($exam_schedule);
	}

	public function update_passing_score(Request $request, $exam_schedule_id)
	{
		$exam_schedule = ExamSchedule::findOrFail($exam_schedule_id);
		$exam_schedule->passing_score = $request->passing_score;
		$exam_schedule->save();

		return response()->json($exam_schedule);
	}

	public function destroy($exam_schedule_id)
	{
		$exam_schedule = ExamSchedule::findOrFail($exam_schedule_id);
		$exam_schedule->delete();

		return response()->json($exam_schedule);
	}
}
