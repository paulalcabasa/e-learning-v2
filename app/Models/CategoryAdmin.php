<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class CategoryAdmin extends Model
{
    protected $fillable = [
        'category_id',
        'administrator_id',
        'created_at'
    ];
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $table = "category_administrators";

    public function getAllByCategory($category_id){
        $sql = "SELECT CONCAT(pit.first_name,' ',pit.last_name) admin_name,
                        utt.user_type,
                        ct.category_name,
                        ca.id category_admin_id,	
                        pit.employee_id,
                        ct.id category_id,
                        CASE WHEN ca.id IS NOT NULL THEN 1 ELSE 0 END admin_flag
                FROM ipc_central.user_access_tab uct
                    LEFT JOIN ipc_central.personal_information_tab pit
                        ON pit.employee_id = uct.employee_id
                    LEFT JOIN ipc_central.user_type_tab utt
                        ON utt.id = uct.user_type_id
                    LEFT JOIN e_learning.category_administrators ca
                        ON ca.employee_id = pit.employee_id 
                        AND ca.category_id = :category_id 
                    LEFT JOIN e_learning.categories ct
                        ON ct.id = ca.category_id
                        
                WHERE 1 = 1
                    AND system_id = 47
                    AND utt.user_type = 'Administrator'
                ORDER BY pit.last_name, pit.first_name";
        $query = DB::select($sql, ['category_id' => $category_id]);
        return $query;
    }

    public function batchInsert($params){
        $this->insert($params);
    }

    public function deleteByCategory($category_id){
        $this->where('category_id',$category_id)->delete();
    }
}
