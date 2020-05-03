<?php

namespace App\Http\Controllers;

use App\Trainor;
use App\ExamSchedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrainorSchedulesController extends Controller
{
	public function schedules($trainor_id)
    {
		$trainor = Trainor::findOrFail($trainor_id)->first();
		$dealer_id = $trainor->dealer_id;
		$exam_schedules = ExamSchedule::
			with(['exam_details' => function($query) use($dealer_id) {
				$query->where([
					['exam_details.dealer_id', '=', $dealer_id]
				]);
			}])
			->with('module')
			->with('question_details')
			->oldest('created_at')
			->get();

		return response()->json($exam_schedules->toArray());
	}
	
	public function trainees($exam_schedule_id, $trainor_id)
	{
		$trainor = Trainor::whereTrainorId($trainor_id)->first();
		$dealer_id = $trainor->dealer_id;

		$query = DB::select(
			'SELECT 
			ts.trainee_id, 
			ts.trainor_id,
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

			LEFT JOIN dealers as dlrs
			ON dlrs.dealer_id = ts.dealer_id

			WHERE 1 = 1
			AND te.remaining_time <= 0
			AND te.seconds <= 0
			AND te.exam_schedule_id = :param4
			AND dlrs.dealer_id = :param5',
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
}
