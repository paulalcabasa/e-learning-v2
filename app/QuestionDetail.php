<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class QuestionDetail extends Model
{
    protected $fillable = [
        'exam_schedule_id',
        'sub_module_id',
        'items'
    ];

    protected $primaryKey = 'question_detail_id';
    public $timestamps = false;

    public function exam_schedule_id()
    {
        return $this->belongsTo('App\ExamSchedule', 'exam_schedule_id', 'exam_schedule_id');
    }

    public function sub_module()
    {
        return $this->belongsTo('App\Models\SubModule', 'sub_module_id', 'sub_module_id');
    }
}
