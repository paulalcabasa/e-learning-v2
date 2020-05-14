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

    public function getAdministrators(){
            $sql = "SELECT CONCAT(pit.first_name,' ',pit.last_name) admin_name,
                        utt.user_type,
                        pit.employee_id
                FROM ipc_central.user_access_tab uct
                    LEFT JOIN ipc_central.personal_information_tab pit
                        ON pit.employee_id = uct.employee_id
                    LEFT JOIN ipc_central.user_type_tab utt
                        ON utt.id = uct.user_type_id
                WHERE 1 = 1
                    AND system_id = 47
                    AND utt.user_type = 'Administrator'
            
                ORDER BY pit.last_name, pit.first_name"; 
        $query = DB::connection('ipc_central')->select($sql);
        return $query;
    }

    public function getByUser($employee_id, $category_id){
        $sql = "SELECT ca.category_id,
                        ca.employee_id,
                        ca.id
                FROM e_learning.category_administrators ca
                    LEFT JOIN e_learning.categories ct
                        ON ct.id = ca.category_id
                WHERE 1 = 1
                    AND ca.category_id = :category_id
                    AND ca.employee_id = :employee_id";
        $params = [
            'category_id' => $category_id,
            'employee_id' => $employee_id
        ];
        $query = DB::select($sql, $params);
        return count($query) > 0 ? $query[0] : $query;
    }

    public function batchInsert($params){
        $this->insert($params);
    }

    public function deleteByCategory($category_id){
        $this->where('category_id',$category_id)->delete();
    }
}
