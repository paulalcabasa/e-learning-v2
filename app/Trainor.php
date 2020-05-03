<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainor extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'dealer_id', 
        'fname',
        'mname',
        'lname',
        'email'
    ];

    protected $primaryKey = 'trainor_id';
    public $timestamps = false;
    protected $dates = ['deleted_at'];

    public function trainees()
    {
    	return $this->hasMany('App\Trainee', 'trainor_id', 'trainor_id');
    }

    public function module_details()
    {
    	return $this->hasMany('App\ModuleDetail', 'dealer_id', 'dealer_id');
    }

    public function dealer()
    {
        return $this->belongsTo('App\Dealer', 'dealer_id', 'dealer_id');
    }
}
