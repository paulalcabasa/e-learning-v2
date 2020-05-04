<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'category_name'
    ];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = "categories";
    
    
}
