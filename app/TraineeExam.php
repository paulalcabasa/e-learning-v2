<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraineeExam extends Model
{
    protected $fillable = [
        'exam_schedule_id', 
        'trainee_id',
        'date_started',
        'date_ended',
        'remaining_time',
        'is_triggered'
    ];

    protected $primaryKey = 'trainee_exam_id';
    public $timestamps = false;

    public function exam_schedule()
    {
        return $this->belongsTo('App\ExamSchedule', 'exam_schedule_id', 'exam_schedule_id');
    }

    public function trainee()
    {
        return $this->belongsTo('App\Trainee', 'trainee_id', 'trainee_id');
    }

    public function choices()
    {
        return $this->hasManyThrough(
            'App\Models\Choice',
            'App\Models\Question',
            'question_id',
            'question_id',
            'question_id',
            'question_id'
        );
    }

    public function trainee_questions()
    {
        return $this->belongsTo('App\TraineeQuestion', 'trainee_exam_id', 'trainee_exam_id');
    }
}
