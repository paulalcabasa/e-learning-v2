<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Trainee extends Model
{
    protected $fillable = [
        'dealer_id', 
        'trainor_id', 
        'fname',
        'mname',
        'lname',
        'email'
    ];

    protected $primaryKey = 'trainee_id';
    public $timestamps = false;

    public function result_detail()
    {
    	return $this->hasMany('App\ResultDetail', 'trainee_id', 'trainee_id');
    }

    public function trainor()
    {
        return $this->belongsTo('App\Trainor', 'trainor_id', 'trainor_id');
    }
    
    public function dealer()
    {
        return $this->belongsTo('App\Dealer', 'dealer_id', 'dealer_id');
    }

    public function exam_details()
    {
        return $this->hasManyThrough(
            'App\ExamDetail', 
            'App\Trainor',
            'dealer_id',
            'dealer_id',
            'trainor_id',
            'trainor_id'
        );
    }

    public function trainee_exams()
    {
    	return $this->hasMany('App\Trainee_exam', 'trainee_id', 'trainee_id');
    }
}
