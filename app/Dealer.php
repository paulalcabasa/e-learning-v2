<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $fillable = [
        'dealer_name', 
        'branch',
    ];

    protected $primaryKey = 'dealer_id';
    public $timestamps = false;

    public function trainors()
    {
    	return $this->hasMany('App\Trainor', 'dealer_id', 'dealer_id');
    }
}
