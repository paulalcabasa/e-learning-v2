<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ResultDetail extends Model
{
    protected $fillable = [
        'exam_detail_id',
        'trainee_id',
        'time_initiated',
        'time_ended',
        'score'
    ];

    protected $primaryKey = 'result_detail_id';
    public $timestamps = false;

    public function exam_detail_id()
    {
        return $this->belongsTo('App\ExamDetail', 'exam_detail_id', 'exam_detail_id');
    }

    public function trainee()
    {
        return $this->belongsTo('App\Trainee', 'trainee_id', 'trainee_id');
    }
}
