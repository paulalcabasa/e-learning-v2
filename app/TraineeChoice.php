<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TraineeChoice extends Model
{
    protected $fillable = [
        'trainee_question_id', 'choice_id', 'choice_letter', 'choice', 'is_correct', 'is_answered'
    ];

    public $primaryKey = 'trainee_choice_id';
}
