<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classification extends Model
{
    protected $fillable = [
        'classification',
        'created_by',
        'date_created',
        'date_updated',
        'date_deleted',
        'deleted_by',
        'category_id'
    ];
    protected $primaryKey = 'id';
    const CREATED_AT = 'date_created';
    const UPDATED_AT = 'date_updated';
    protected $table = "category_classifications";

    
}
