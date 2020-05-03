<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'fullname', 
        'email',
        'mobile',
        'subject',
        'message'
    ];

    protected $primaryKey = 'notification_id';
    public $timestamps = false;
}
