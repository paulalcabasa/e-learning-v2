<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public function getModules($employee_id){
        //   (SELECT COUNT(sm.module_id) FROM sub_modules sm WHERE sm.module_id = m.module_id) as count_total
        $sql = "SELECT m.module_id,
                        m.module,
                        m.description,
                        m.file_name,
                        m.is_active,
                        ct.category_name
                FROM modules m 
                        LEFT JOIN categories ct
                                ON ct.id = m.category_id
                        LEFT JOIN category_administrators ca
                            ON ca.category_id = ct.id
                WHERE ca.employee_id = :employee_id";
        $query = DB::select($sql,['employee_id' => $employee_id]);
        return $query;
    }
    
   
}
