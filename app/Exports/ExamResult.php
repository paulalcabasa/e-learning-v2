<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use DB;


class ExamResult implements FromView
{

    protected $params;

    public function __construct($params){
        $this->params = $params;
    } 
    
    public function view(): View {
        $query = DB::select(
			'SELECT 
                ts.trainee_id,
                CONCAT(ts.lname,", ",ts.fname," ",COALESCE(ts.mname, "")) as trainee_name,
                date_format(te.created_at,"%m/%d/%Y %h:%i %p") start_time, 
                date_format(te.updated_at,"%m/%d/%Y %h:%i %p") completion_time, 
                TIMEDIFF(te.updated_at,te.created_at) consumed_time,
                es.passing_score,
                (
                SELECT COUNT(c.choice_id)
                FROM trainee_questions as tq
                LEFT JOIN trainee_exams as te
                ON tq.trainee_exam_id = te.trainee_exam_id
                LEFT JOIN choices as c
                ON c.choice_id = tq.choice_id
                WHERE te.exam_schedule_id = :exam_schedule_id1
                AND te.trainee_id = ts.trainee_id
                AND c.is_correct = 1
                ) as score,
			
                (
                SELECT SUM(qd.items)
                FROM question_details as qd
                LEFT JOIN exam_schedules as es
                ON es.exam_schedule_id = qd.exam_schedule_id
                WHERE es.exam_schedule_id = :exam_schedule_id2
                ) as items,

                (
                SELECT IF(COUNT(tq.choice_id) < items, "incomplete", "complete")
                FROM trainee_questions as tq
                LEFT JOIN trainee_exams as te
                ON tq.trainee_exam_id = te.trainee_exam_id
                WHERE te.exam_schedule_id = :exam_schedule_id3
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
                AND te.exam_schedule_id = :exam_schedule_id4
                AND trs.dealer_id = :dealer_id',
			[
				'exam_schedule_id1' => $this->params['exam_schedule_id'],
				'exam_schedule_id2' => $this->params['exam_schedule_id'],
				'exam_schedule_id3' => $this->params['exam_schedule_id'],
				'exam_schedule_id4' => $this->params['exam_schedule_id'],
				'dealer_id' => $this->params['dealer_id']
			]
		);

	
        return view('exports.exam_result', [
            'result' => $query
        ]);
    }
}
