<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = [
        'module', 'description', 'file_name'
    ];

    protected $primaryKey = 'module_id';

    public function submodules()
    {
    	return $this->hasMany('App\Models\SubModule', 'module_id', 'module_id');
    }

    public function module_details()
    {
        return $this->hasManyThrough(
            'App\ModuleDetail', 
            'App\ModuleSchedule', 
            'module_id',            // Foreign key on module_schedules
            'module_schedule_id',   // Foreign key on module_details
            'module_id',            // Local key on modules
            'module_schedule_id'    // Local key on module_schedules
        );
    }
    
   
}
