<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Choice extends Model
{
    protected $fillable = [
        'question_id', 'choice_letter', 'choice', 'is_correct'
    ];

    protected $primaryKey = 'choice_id';

    public function question()
    {
    	return $this->belongsTo('App\Models\Question', 'question_id', 'question_id');
    }

    public function trainee_exams()
    {
    	return $this->hasMany('App\TraineeExam', 'choice_id', 'choice_id');
    }
}
