<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrainorHistory extends Model
{
    protected $fillable = [
        'trainor_id', 
        'module_detail_id'
    ];

    protected $primaryKey = 'trainor_history_id';
}
