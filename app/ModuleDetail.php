<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ModuleDetail extends Model
{
    protected $fillable = [
        'module_schedule_id', 
        'dealer_id',
        'trainor_id',
        'start_date',
        'end_date',
        'is_opened',
        'is_enabled',
        'status'
    ];

    protected $primaryKey = 'module_detail_id';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'date:M d, Y | H:i:s A',
        'updated_at' => 'date:M d, Y | H:i:s A'
    ];

    public function exam_detail()
    {
    	return $this->hasMany('App\ExamDetail', 'module_detail_id', 'module_detail_id');
    }

    public function dealer()
    {
    	return $this->belongsTo('App\Dealer', 'dealer_id', 'dealer_id');
    }

    public function module()
    {
    	return $this->belongsTo('App\Module', 'module_id', 'module_id');
    }

    public function module_schedule()
    {
    	return $this->belongsTo('App\ModuleSchedule', 'module_schedule_id', 'module_schedule_id');
    }
}
