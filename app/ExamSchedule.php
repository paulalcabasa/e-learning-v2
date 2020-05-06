<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class ExamSchedule extends Model
{
    protected $fillable = [
        'module_id',
        'status',
        'timer',
        'passing_score',
        'created_by'
    ];

    protected $primaryKey = 'exam_schedule_id';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'date:M d, Y | H:i:s A',
        'updated_at' => 'date:M d, Y | H:i:s A'
    ];

    public function exam_details()
    {
        return $this->hasMany('App\ExamDetail', 'exam_schedule_id', 'exam_schedule_id');
    }

    public function question_details()
    {
        return $this->hasMany('App\QuestionDetail', 'exam_schedule_id', 'exam_schedule_id');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module_id', 'module_id');
    }

    public function sub_modules()
    {
        return $this->hasManyThrough(
            'App\Models\SubModule', 
            'App\QuestionDetail',
            'exam_schedule_id', 
            'sub_module_id',
            'exam_schedule_id', 
            'sub_module_id'
        );
    }

    public function trainee_exams()
    {
        return $this->hasMany('App\TraineeExam', 'exam_schedule_id', 'exam_schedule_id');
    }

    public function getSchedule($employee_id){
        /* /* $exam_schedules = ExamSchedule::
			with('exam_details')
			->with('module')
			->with('question_details')
			->get(); */
		
        $sql = "SELECT es.exam_schedule_id,
                        es.created_by,
                        date_format(es.created_at,'%M %d, %Y %h:%i %p') created_at,
                        es.status,
                        md.module,
                        ct.category_name
                FROM exam_schedules es
                    LEFT JOIN modules md
                        ON md.module_id = es.module_id
                    LEFT JOIN exam_details ed
                        ON ed.exam_schedule_id = es.exam_schedule_id
                    LEFT JOIN categories ct
                        ON ct.id = md.category_id
                    LEFT JOIN category_administrators ca
                        ON ca.category_id = ct.id
                WHERE ca.employee_id = :employee_id";
        $query = DB::select($sql, ['employee_id' => $employee_id]);
        return $query;
    }

    public function getResults($employee_id){
        /* 	$query = DB::table('exam_schedules as es')
			->select(
				'es.exam_schedule_id', 
				'es.created_by', 
				'es.created_at', 
				'es.timer', 
				'es.status', 
				'm.module'
			)
			->leftJoin('modules as m', 'm.module_id', '=', 'es.module_id')
			->leftJoin('exam_details as ed', 'ed.exam_schedule_id', '=', 'es.exam_schedule_id')
			->where('ed.is_opened', 1)
			->groupBy([
				'es.exam_schedule_id'
			])
			->get(); */
        $sql = "SELECT es.exam_schedule_id, 
                                es.created_by, 
                                es.created_at, 
                                es.timer, 
                                es.status, 
                                m.module,
                                ct.category_name
                FROM exam_schedules es
                    LEFT JOIN modules m
                        ON m.module_id = es.module_id
                    LEFT JOIN exam_details ed
                        ON ed.exam_schedule_id = es.exam_schedule_id
                    LEFT JOIN category_administrators ca
                        ON ca.category_id = m.category_id
                    LEFT JOIN categories ct
                        ON ct.id = ca.category_id
                WHERE ed.is_opened = 1
                    AND ca.employee_id = :employee_id
                GROUP BY es.exam_schedule_id";
        $query = DB::select($sql, ['employee_id' => $employee_id]);
        return $query;
    }
}
