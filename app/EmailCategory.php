<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailCategory extends Model
{
    protected $fillable = ['email_category'];

    protected $primaryKey = 'email_category_id';
    public $timestamps = false;

    public function email_categories()
    {
        return $this->hasMany('App\Email', 'email_category_id', 'email_category_id');
    }
}
