<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ExamDetail extends Model
{
    protected $fillable = [
        'exam_schedule_id',
        'detail_id',
        'start_date',
        'end_date_date',
        'is_opened',
        'is_enabled', 
        'status'
    ];

    protected $primaryKey = 'exam_detail_id';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'date:M d, Y | H:i:s A',
        'updated_at' => 'date:M d, Y | H:i:s A'
    ];

    public function result_detail()
    {
        return $this->hasMany('App\ResultDetail', 'exam_detail_id', 'exam_detail_id');
    }

    public function dealer()
    {
        return $this->belongsTo('App\Dealer', 'dealer_id', 'dealer_id');
    }

    public function exam_schedule()
    {
        return $this->belongsTo('App\ExamSchedule', 'exam_schedule_id', 'exam_schedule_id');
    }

    // Exams
    public function question_details()
    {
        return $this->hasManyThrough(
            'App\QuestionDetail',
            'App\ExamSchedule',
            'exam_schedule_id',
            'exam_schedule_id',
            'exam_schedule_id',
            'exam_schedule_id'
        );
    }

    public function sub_module()
    {
        return $this->belongsTo('App\Models\SubModule', 'sub_module_id', 'sub_module_id');
    }

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'sub_module_id', 'sub_module_id');
    }
}
