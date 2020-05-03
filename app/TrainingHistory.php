<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainingHistory extends Model
{
    protected $fillable = [
        'module_id', 
        'exam_schedule_id', 
        'exam_detail_id', 
        'trainee_exam_id', 
        'dealer_id',
        'trainor_id',
        'trainee_id',
        'score',
        'result',
        'date_taken'
    ];

    protected $primaryKey = 'training_history_id';

    public function module()
    {
    	return $this->belongsTo('App\Models\Module', 'module_id', 'module_id');
    }

    public function trainee_exam()
    {
    	return $this->belongsTo('App\TraineeExam', 'trainee_exam_id', 'trainee_exam_id');
    }

    public function trainee()
    {
    	return $this->belongsTo('App\Trainee', 'trainee_id', 'trainee_id');
    }

    public function dealer_id()
    {
    	return $this->belongsTo('App\Dealer', 'dealer_id', 'dealer_id');
    }
}
