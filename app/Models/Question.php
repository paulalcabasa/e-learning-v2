<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    protected $fillable = [
        'sub_module_id', 'question'
    ];

    protected $primaryKey = 'question_id';

    public function choices()
    {
    	return $this->hasMany('App\Models\Choice', 'question_id', 'question_id');
    }

    public function submodule()
    {
    	return $this->belongsTo('App\Models\SubModule', 'sub_module_id', 'sub_module_id');
    }

    public function trainee_exams()
    {
    	return $this->hasMany('App\TraineeExam', 'question_id', 'question_id');
    }
}
