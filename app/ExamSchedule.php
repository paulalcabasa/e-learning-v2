<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
