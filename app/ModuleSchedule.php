<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ModuleSchedule extends Model
{
    protected $fillable = [
        'module_id',
        'status',
        'created_by'
    ];

    protected $primaryKey = 'module_schedule_id';
    public $timestamps = false;

    protected $casts = [
        'created_at' => 'date:M d, Y | H:i:s A',
        'updated_at' => 'date:M d, Y | H:i:s A'
    ];

    public function module_detail()
    {
        return $this->hasMany('App\ModuleDetail', 'module_schedule_id', 'module_schedule_id');
    }

    public function module()
    {
        return $this->belongsTo('App\Models\Module', 'module_id', 'module_id');
    }

    public function getModuleSchedule($employee_id){
       /*   $module_schedules = DB::table('module_schedules as ms')
            ->select(
                'ms.*', // module_schedule
                'm.module'
            )
            ->leftJoin('modules as m', 'm.module_id', '=', 'ms.module_id')
            ->orderBy('ms.created_at', 'DESC')
            ->get(); */
        
        $sql = "SELECT ms.module_schedule_id,
                        ms.module_id,
                        ms.created_at,
                        ms.created_by,
                        ms.status,
                        ct.category_name,
                        md.module
                FROM module_schedules ms
                    LEFT JOIN modules md
                        ON md.module_id = ms.module_id
                    LEFT JOIN category_administrators ca
                        ON ca.category_id = md.category_id
                    LEFT JOIN categories ct
                        ON ct.id = md.category_id
                WHERE ca.employee_id  = :employee_id";
        $query = DB::select($sql, ['employee_id' => $employee_id]);
        return $query;
    }
}
