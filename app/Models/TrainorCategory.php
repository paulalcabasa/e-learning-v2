<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class TrainorCategory extends Model
{
    protected $fillable = [
        'trainor_id',
        'category_id',
        'created_at',
        'updated_at'
    ];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = "trainor_categories";

    public function getTrainorCategories($trainor_id){
        $sql = "SELECT ct.id,
                    ct.category_name,
                    tc.id trainor_category_id
                FROM categories ct
                    LEFT JOIN trainor_categories tc
                        ON tc.category_id = ct.id
                        AND tc.trainor_id = :trainor_id
                WHERE ct.status = 'active'
                ORDER BY ct.category_name ASC";
        $query = DB::select($sql,['trainor_id' => $trainor_id]);
        return $query;
    }

    public function deleteByTrainor($trainor_id){
        $this->where('trainor_id', $trainor_id)->delete();
    }

    public function batchInsert($params){
        $this->insert($params);
    }
    
    public function getCategories($trainor_id){
        $sql = "SELECT ct.id,
                    ct.category_name,
                    tc.id trainor_category_id
                FROM categories ct
                    LEFT JOIN trainor_categories tc
                        ON tc.category_id = ct.id
                WHERE ct.status = 'active'
                AND tc.trainor_id = :trainor_id
                ORDER BY ct.category_name ASC";
        $query = DB::select($sql,['trainor_id' => $trainor_id]);
        return $query;
    }

    public function validateAccess($params){
        return $this->where([
            ['category_id', '=', $params['category_id']], 
            ['trainor_id' ,'=', $params['trainor_id']]
        ])->get();
        
    }
}
