<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

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

    public function getByTrainor($trainor_id){
        $sql = "SELECT cc.id classification_id,
                    ca.category_name,
                    cc.classification
                FROM category_classifications cc
                    INNER JOIN categories ca
                        ON ca.id = cc.category_id
                    INNER JOIN trainor_categories tc
                        ON tc.category_id = ca.id
                WHERE 1 = 1
                    AND tc.trainor_id = :trainor_id
                ORDER BY ca.category_name, cc.classification";
        $query = DB::select($sql, ['trainor_id' => $trainor_id]);
        return $query;
    }
}
