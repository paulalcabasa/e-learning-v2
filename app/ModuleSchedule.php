<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
}
