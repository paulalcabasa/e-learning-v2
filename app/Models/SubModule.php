<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubModule extends Model
{
    protected $fillable = [
        'module_id', 'sub_module'
    ];
    
    protected $primaryKey = 'sub_module_id';

    public function questions()
    {
        return $this->hasMany('App\Models\Question', 'sub_module_id', 'sub_module_id');
    }

    public function question_detail()
    {
        return $this->hasMany('App\QuestionDetail', 'sub_module_id', 'sub_module_id');
    }

    public function module()
    {
    	return $this->belongsTo('App\Models\Module', 'module_id', 'module_id');
    }
}
