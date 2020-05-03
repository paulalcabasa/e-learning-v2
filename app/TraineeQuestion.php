<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraineeQuestion extends Model
{
    protected $fillable = [
        'number',
        'question_id',
        'choice_id'
    ];

    protected $primaryKey = 'trainee_question_id';
    public $timestamps = false;

    public function question()
    {
        return $this->belongsTo('App\Models\Question', 'question_id', 'question_id');
    }

    public function choice()
    {
        return $this->belongsTo('App\Models\Choice', 'choice_id', 'choice_id');
    }
}
