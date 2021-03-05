<?php

namespace App\Http\Controllers;

use App\Models\SubModule;
use App\TraineeQuestion;
use App\Models\Dealer;
use App\ExamSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Exports\ExamResult;
use Excel;

class ExamResultController extends Controller
{
	public function exam_schedules()
	{
		$examSched = new ExamSchedule;
		$data = $examSched->getResults(session('employee_id')); 	
		return $data;
	}

	public function dealers($exam_schedule_id)
	{
		$query = DB::table('dealers as d')
			->select(
				'd.dealer_id', 
				'd.dealer_name', 
				'd.branch'
			)
			->leftJoin('exam_details as ed', 'ed.dealer_id', '=', 'd.dealer_id')
			->where('ed.exam_schedule_id', $exam_schedule_id)
			->latest('dealer_id')
			->get();

		return $query->toArray();
	}

	public function trainees($exam_schedule_id, $dealer_id)
	{
		$query = DB::select(
			'SELECT 
			ts.trainee_id,
			CONCAT(ts.lname,", ",ts.fname," ",COALESCE(ts.mname, "")) as trainee,
			te.created_at, 
			te.updated_at,
			es.passing_score,

			(
			SELECT COUNT(c.choice_id)
			FROM trainee_questions as tq
			LEFT JOIN trainee_exams as te
			ON tq.trainee_exam_id = te.trainee_exam_id
			LEFT JOIN choices as c
			ON c.choice_id = tq.choice_id
			WHERE te.exam_schedule_id = :param1
			AND te.trainee_id = ts.trainee_id
			AND c.is_correct = 1
			) as score,
			
			(
			SELECT SUM(qd.items)
			FROM question_details as qd
			LEFT JOIN exam_schedules as es
			ON es.exam_schedule_id = qd.exam_schedule_id
			WHERE es.exam_schedule_id = :param2
			) as items,

			(
			SELECT IF(COUNT(tq.choice_id) < items, "incomplete", "complete")
			FROM trainee_questions as tq
			LEFT JOIN trainee_exams as te
			ON tq.trainee_exam_id = te.trainee_exam_id
			WHERE te.exam_schedule_id = :param3
			AND te.trainee_id = ts.trainee_id
			AND tq.choice_id > 0
			) as status

			FROM trainees as ts

			LEFT JOIN trainee_exams as te
			ON te.trainee_id = ts.trainee_id

			LEFT JOIN exam_schedules as es
			ON es.exam_schedule_id = te.exam_schedule_id

			LEFT JOIN trainors as trs
			ON trs.trainor_id = ts.trainor_id

			WHERE te.remaining_time <= 0
			AND te.seconds <= 0
			AND te.exam_schedule_id = :param4
			AND trs.dealer_id = :param5',
			[
				'param1' => $exam_schedule_id,
				'param2' => $exam_schedule_id,
				'param3' => $exam_schedule_id,
				'param4' => $exam_schedule_id,
				'param5' => $dealer_id
			]
		);

		return response()->json($query);
	}

	public function detailed_result($exam_schedule_id, $trainee_id)
	{
		$query = TraineeQuestion::
			select(
				'trainee_questions.trainee_question_id', 
				'trainee_questions.number', 
				'trainee_questions.choice_id', 
				'trainee_questions.question_id', 
				'c.is_correct' 
			)
			->with([
				'question:question_id,question', 
				'question.choices:question_id,choice_id,choice_letter,choice,is_correct' 
			])
			->leftJoin('trainee_exams as te', 'te.trainee_exam_id', '=', 'trainee_questions.trainee_exam_id')
			->leftJoin('choices as c', 'c.choice_id', '=', 'trainee_questions.choice_id')
			->where([
					['te.exam_schedule_id', '=', $exam_schedule_id],
					['te.trainee_id', '=', $trainee_id]
				])
			->orderBy('trainee_questions.number')
			->get();

		return response()->json($query);
	}

	public function header($exam_schedule_id)
	{
		$query = DB::table('exam_schedules as es')
			->select(
				'm.module', 
				'm.description', 
				'es.timer'
			)
			->leftJoin('modules as m', 'm.module_id', '=', 'es.module_id')
			->where('es.exam_schedule_id', $exam_schedule_id)
			->first();

		return response()->json($query);
	}

	public function exam_status($exam_schedule_id, $dealer_id, $trainee_id)
	{
		$query = DB::select(
			'SELECT 
			ts.trainee_id, 
			CONCAT(ts.lname,", ",ts.fname," ",COALESCE(ts.mname, "")) as trainee,
			te.created_at, 
			te.updated_at,

			(
			SELECT COUNT(c.choice_id)
			FROM trainee_questions as tq
			LEFT JOIN trainee_exams as te
			ON tq.trainee_exam_id = te.trainee_exam_id
			LEFT JOIN choices as c
			ON c.choice_id = tq.choice_id
			WHERE te.exam_schedule_id = :param1
			AND te.trainee_id = ts.trainee_id
			AND c.is_correct = 1
			) as score,
			
			(
			SELECT SUM(qd.items)
			FROM question_details as qd
			LEFT JOIN exam_schedules as es
			ON es.exam_schedule_id = qd.exam_schedule_id
			WHERE es.exam_schedule_id = :param2
			) as items,

			(
			SELECT IF(COUNT(tq.choice_id) < items, "incomplete", "complete")
			FROM trainee_questions as tq
			LEFT JOIN trainee_exams as te
			ON tq.trainee_exam_id = te.trainee_exam_id
			WHERE te.exam_schedule_id = :param3
			AND te.trainee_id = ts.trainee_id
			AND tq.choice_id > 0
			) as status

			FROM trainees as ts

			LEFT JOIN trainee_exams as te
			ON te.trainee_id = ts.trainee_id

			LEFT JOIN trainors as trs
			ON trs.trainor_id = ts.trainor_id

			WHERE te.remaining_time <= 0
			AND te.seconds <= 0
			AND te.exam_schedule_id = :param4
			AND trs.dealer_id = :param5
			AND ts.trainee_id = :param6',
			[
				'param1' => $exam_schedule_id,
				'param2' => $exam_schedule_id,
				'param3' => $exam_schedule_id,
				'param4' => $exam_schedule_id,
				'param5' => $dealer_id,
				'param6' => $trainee_id
			]
		);

		return response()->json($query[0]); // This is intended because trainee_id & exam_schedule_id
	}

	public function summary(Request $params)
	{
		$query = DB::table('sub_modules as sm')
			->select(
				'ts.trainee_id', 
				DB::raw('CONCAT(ts.lname,", ",ts.fname," ",COALESCE(ts.mname, "")) as trainee'), 
				'sm.sub_module_id', 
				'sm.sub_module', 
				DB::raw('SUM(qd.items) as items')
			)
			->leftJoin('question_details as qd', 'qd.sub_module_id', '=', 'sm.sub_module_id')
			->leftJoin('trainee_exams as te', 'te.exam_schedule_id', '=', 'qd.exam_schedule_id')
			->leftJoin('trainees as ts', 'ts.trainee_id', '=', 'te.trainee_id')
			->where([
				['te.trainee_id', $params->trainee_id],
				['te.exam_schedule_id', $params->exam_schedule_id]
			])
			->groupBy('sm.sub_module_id', 'te.trainee_id')
			->get();

		$data = [];
		foreach ($query as $key => $value) {
			$data[$key] = $value;
			$data[$key]->score = $this->user_answer($params->exam_schedule_id, $params->trainee_id, $value->sub_module_id)->score;
		}

		return response()->json($data);
	}

	public function user_answer($exam_schedule_id, $trainee_id, $submodule_id)
	{
		$user_answers = DB::table('trainee_questions as tq')
			->select('tq.choice_id')
			->leftJoin('trainee_exams as te', 'te.trainee_exam_id', '=', 'tq.trainee_exam_id')
			->leftJoin('trainees as ts', 'ts.trainee_id', '=', 'te.trainee_id')
			->where([
				['ts.trainee_id', $trainee_id],
				['te.exam_schedule_id', $exam_schedule_id],
			])
			->get();

		$user_answers = array_pluck($user_answers, 'choice_id'); //--> RETRIEVE all array values

		$query = DB::table('choices as c')
			->select(
				DB::raw('COUNT(c.is_correct) as score')
			)
			->leftJoin('questions as q', 'q.question_id', '=', 'c.question_id')
			->leftJoin('sub_modules as sm', 'sm.sub_module_id', '=', 'q.sub_module_id')
			->whereIn('c.choice_id', $user_answers)
			->where([
				['sm.sub_module_id', $submodule_id],
				['c.is_correct', 1]
			])
			->first();

		return $query;
	}

	/**
	 * Parameters:
	 * [exam_schedule_id]
	 */
	public function dealer_average($exam_schedule_id)
	{
		$query = DB::select(
			"SELECT 
			d.dealer_id,
			d.dealer_name,
			d.branch
			-- (SELECT 
			-- 	SUM(items) 
			-- FROM
			-- 	question_details 
			-- WHERE exam_schedule_id = es.exam_schedule_id) AS total_items,
			-- (SELECT ROUND
			-- 	(
			-- 	COUNT(c.choice_id) / 
			-- 	(SELECT 
			-- 		COUNT(te2.trainee_id) 
			-- 	FROM
			-- 		trainee_exams te2 
			-- 	WHERE te2.exam_schedule_id = te2.exam_schedule_id)
			-- 	) 
			-- FROM
			-- 	choices c 
			-- 	LEFT JOIN trainee_questions tq 
			-- 	ON tq.choice_id = c.choice_id 
			-- 	LEFT JOIN trainee_exams te 
			-- 	ON te.trainee_exam_id = tq.trainee_exam_id 
			-- WHERE te.trainee_exam_id = te.trainee_exam_id 
			-- 	AND te.exam_schedule_id = te.exam_schedule_id 
			-- 	AND c.is_correct = 1) AS average_score 
			FROM
			dealers d 
			LEFT JOIN trainors trs 
				ON trs.dealer_id = d.dealer_id 
			LEFT JOIN trainees ts 
				ON ts.trainor_id = trs.trainor_id 
			LEFT JOIN trainee_exams te 
				ON te.trainee_id = ts.trainee_id 
			LEFT JOIN trainee_questions tq 
				ON tq.trainee_exam_id = te.trainee_exam_id 
			LEFT JOIN exam_schedules es 
				ON es.exam_schedule_id = te.exam_schedule_id 

			WHERE es.exam_schedule_id = $exam_schedule_id
			AND te.remaining_time = 0 
			AND te.seconds = 0 
			GROUP BY d.dealer_id"
		);

		return response()->json($query);
	}

	public function dealer_summary($dealer_id, $exam_schedule_id)
	{
		$participants = DB::table('trainee_exams as te')
			->leftJoin('trainees as ts', 'ts.trainee_id', '=', 'te.trainee_id')
			->where([
				'te.exam_schedule_id' => $exam_schedule_id,
				'ts.dealer_id'        => $dealer_id,
				'te.remaining_time'   => 0,
				'te.seconds'          => 0
			])
			->count();

		$query = DB::select(
			'SELECT sm.sub_module_id, sm.sub_module, 
			ROUND(COUNT(CASE WHEN tc.is_correct = 1 AND tc.is_answered = 1 THEN 1 ELSE NULL END)/?) score,
			qd.items
			FROM exam_schedules es
			LEFT JOIN trainee_exams te
			ON es.exam_schedule_id = te.exam_schedule_id
			LEFT JOIN trainee_questions tq
			ON te.trainee_exam_id = tq.trainee_exam_id
			LEFT JOIN trainee_choices tc
			ON tq.trainee_question_id = tc.trainee_question_id
			LEFT JOIN questions q
			ON tq.question_id = q.question_id
			LEFT JOIN sub_modules sm
			ON q.sub_module_id = sm.sub_module_id
			LEFT JOIN question_details qd
			ON sm.sub_module_id = qd.sub_module_id
			LEFT JOIN trainees as ts
			ON ts.trainee_id = te.trainee_id
			AND es.exam_schedule_id  = qd.exam_schedule_id
			WHERE 1 = 1
			AND es.exam_schedule_id = ?
			AND ts.dealer_id = ?
			GROUP BY sm.sub_module_id, qd.items', [$participants,$exam_schedule_id,$dealer_id]
		);

		return response()->json($query);
	}

	public function schedule_summary($exam_schedule_id)
	{
		$participants = DB::table('trainee_exams as te')
			->leftJoin('trainees as ts', 'ts.trainee_id', '=', 'te.trainee_id')
			->where([
				'te.exam_schedule_id' => $exam_schedule_id,
				'te.remaining_time'   => 0,
				'te.seconds'          => 0
			])
			->count();

		$query = DB::select(
			'SELECT sm.sub_module_id, sm.sub_module, 
			ROUND(COUNT(CASE WHEN tc.is_correct = 1 AND tc.is_answered = 1 THEN 1 ELSE NULL END)/?) score,
			qd.items
			FROM exam_schedules es
			LEFT JOIN trainee_exams te
			ON es.exam_schedule_id = te.exam_schedule_id
			LEFT JOIN trainee_questions tq
			ON te.trainee_exam_id = tq.trainee_exam_id
			LEFT JOIN trainee_choices tc
			ON tq.trainee_question_id = tc.trainee_question_id
			LEFT JOIN questions q
			ON tq.question_id = q.question_id
			LEFT JOIN sub_modules sm
			ON q.sub_module_id = sm.sub_module_id
			LEFT JOIN question_details qd
			ON sm.sub_module_id = qd.sub_module_id
			AND es.exam_schedule_id  = qd.exam_schedule_id
			WHERE 1 = 1
			AND es.exam_schedule_id = ?
			GROUP BY sm.sub_module_id, qd.items', [$participants,$exam_schedule_id]
		);

		return response()->json($query);
	}

	// --> Test Zone
	public function scoring_summary($exam_schedule_id)
	{
		$participants = DB::table('trainee_exams as te')
			->leftJoin('trainees as ts', 'ts.trainee_id', '=', 'te.trainee_id')
			->where([
				'te.exam_schedule_id' => $exam_schedule_id,
				'ts.dealer_id'        => 39,
				'te.remaining_time'   => 0,
				'te.seconds'          => 0
			])
			->count();

		$query = DB::select(
			'SELECT sm.sub_module_id, sm.sub_module, 
			ROUND(COUNT(CASE WHEN tc.is_correct = 1 AND tc.is_answered = 1 THEN 1 ELSE NULL END)/?) score,
			qd.items
			FROM exam_schedules es
			LEFT JOIN trainee_exams te
			ON es.exam_schedule_id = te.exam_schedule_id
			LEFT JOIN trainee_questions tq
			ON te.trainee_exam_id = tq.trainee_exam_id
			LEFT JOIN trainee_choices tc
			ON tq.trainee_question_id = tc.trainee_question_id
			LEFT JOIN questions q
			ON tq.question_id = q.question_id
			LEFT JOIN sub_modules sm
			ON q.sub_module_id = sm.sub_module_id
			LEFT JOIN question_details qd
			ON sm.sub_module_id = qd.sub_module_id
			LEFT JOIN trainees as ts
			ON ts.trainee_id = te.trainee_id
			AND es.exam_schedule_id  = qd.exam_schedule_id
			WHERE 1 = 1
			AND es.exam_schedule_id = ?
			AND ts.dealer_id = 39
			GROUP BY sm.sub_module_id, qd.items', [$participants,$exam_schedule_id]
		);

		return response()->json($query);
	}

	public function export_exam_result(Request $request)
	{
        $params = [
            'exam_schedule_id' => $request->exam_schedule_id,
            'dealer_id' => $request->dealer_id
        ];
		
        return Excel::download(new ExamResult($params), 'exam_result.xlsx');
    }

	
}
