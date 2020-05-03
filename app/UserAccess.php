<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserAccess extends Model
{
    protected $connection = 'ipc_central';
    protected $table = 'user_access_tab';
    protected $fillable = ['employee_id','system_id','user_type_id','last_user_id'];
    protected $primaryKey = false;
    public $timestamps = false;
}