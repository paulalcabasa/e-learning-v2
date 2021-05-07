<?php

namespace App\Services;

use App\Models\Question;
use App\TraineeExam;
use App\TraineeChoice;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Exam
{
	/**
	 * This will get questions in random set
	 */
	public function get_questions($question_details)
	{
		$questions = [];
		foreach ($question_details as $key => $value) {
			$picked_questions = Question::with('choices')
				->leftJoin('sub_modules as sm', 'sm.sub_module_id', '=', 'questions.sub_module_id')
				->where([
					['questions.sub_module_id','=',$value->sub_module_id]
				])
				->take($value->items)
				->inRandomOrder() 
				->get();

			foreach ($picked_questions as $key => $value) {
				array_push($questions, [
					'question_id'   => $value['question_id'],
					'sub_module_id' => $value['sub_module_id'],
					'module_id'     => $value['module_id'],
					'question'      => $value['question'],
					'sub_module'    => $value['sub_module'],
					'choices'       => $value['choices']
				]);
			}
		}

		return $questions;
	}

    /**
	 * params: exam_detail_id
	 */
	public function details($exam_detail_id, $user_id)
	{
		$current_date = Carbon::now()->toDateString();

		$exams = DB::table('exam_details as ed')
			->select(
				'ed.*',
				'm.module_id',
				'm.module',
				'm.description',
				DB::raw('SUM(qd.items) as items'),
				'es.timer',
				'es.passing_score',
				'trainee.trainee_id',
				't_exams.remaining_time',
				't_exams.seconds'
			)
			->leftJoin('trainors as trainor', 'trainor.dealer_id', '=', 'ed.dealer_id')
			->leftJoin('trainees as trainee', 'trainee.trainor_id', '=', 'trainor.trainor_id')
			->leftJoin('exam_schedules as es', 'es.exam_schedule_id', '=', 'ed.exam_schedule_id')
			->leftJoin('modules as m', 'm.module_id', '=', 'es.module_id')
			->leftJoin('question_details as qd', 'qd.exam_schedule_id', '=', 'es.exam_schedule_id')
			->leftJoin('trainee_exams as t_exams', function($join) {
				global $user_id;
				$join->on('t_exams.exam_schedule_id', '=', 'es.exam_schedule_id');	
				$join->on('t_exams.trainee_id', '=', 'trainee.trainee_id');	
			})
			
			->where([
				'ed.exam_detail_id'  => $exam_detail_id,
				'trainee.trainee_id' => $user_id,
				'ed.is_enabled'      => 1
			])
			->where('ed.end_date', '>=', $current_date)
			->where('ed.start_date', '<=', $current_date)
			->groupBy(
				'exam_detail_id',
				'trainee.trainee_id',
				'qd.exam_schedule_id',
				't_exams.remaining_time',
				't_exams.seconds'
			)
			->first();

		return $exams;
	}

    public function fill_trainee_exam($params)
	{
		$model = new TraineeExam();
		$model->exam_schedule_id = $params['exam_schedule_id'];
		$model->trainee_id       = $params['trainee_id'];
		$model->remaining_time   = $params['remaining_time'];
		$model->save();

		return $model;
	}

    public function final_score($trainee_exam_id)
    {
        $query = DB::table('trainee_questions as tq')
            ->select(
                DB::raw('COUNT(c.is_correct) as score')
            )
            ->leftJoin('choices as c', 'c.choice_id', '=', 'tq.choice_id')
            ->where([
                ['tq.trainee_exam_id', '=', $trainee_exam_id],
                ['c.is_correct', '=', 1]
            ])
            ->first();

        return $query;
    }

	public function create_choices($trainee_question_id, $question_id)
	{
		$choices = DB::table('choices')
			->where('question_id', $question_id)
			->inRandomOrder()
			->get();

		$i = 0;
		$data = [];
		$choice_letters = ['a','b','c','d']; 
		foreach ($choices as $key => $value) {
			$data[] = [
				'trainee_question_id' => $trainee_question_id,
				'choice_id'           => $value->choice_id,
				'choice_letter'       => $choice_letters[$i],
				'choice'              => $value->choice,
				'is_correct'          => $value->is_correct
			];

			$i == 3 ? $i = 0 : $i++;
		}

		$create_choice = DB::table('trainee_choices')->insert($data);
		return;
	}
}
