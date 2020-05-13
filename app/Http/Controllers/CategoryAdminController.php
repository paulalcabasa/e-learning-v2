<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoryAdmin;
use DB;

class CategoryAdminController extends Controller
{
    public function index(Request $request){
        $categoryAdmin = new CategoryAdmin;
        $admin = $categoryAdmin->getAdministrators();
        $category_id = $request->category_id;
        
        $admins = [];
        foreach($admin as $row){

            $adminFlag = $categoryAdmin->getByUser($row->employee_id, $category_id);
            
       

            array_push($admins,[
                'admin_name' => $row->admin_name,
                'employee_id' => $row->employee_id,
                'category_id' => $category_id,
               // 'adminFlag' => $adminFlag,
                'orig_admin_flag' => (!empty($adminFlag) ? true : false),
                'admin_flag' => (!empty($adminFlag) ? true : false)
            ]);
        }
        return $admins;
    }

    public function store(Request $request){
        $category_id = $request->category_id;
        $params = [];
        $categoryAdmins = $request->categoryAdmins;
        $categoryAdmin = new CategoryAdmin;
        DB::beginTransaction();

        try{

            $categoryAdmin->deleteByCategory($category_id);

            foreach($categoryAdmins as $row){
                if($row['admin_flag']){
                    array_push($params,[
                        'category_id' => $category_id,
                        'employee_id' => $row['employee_id']
                    ]);
                }
            }

            $categoryAdmin->batchInsert($params);

            DB::commit();

            return [
                'message' => 'Category admnistrators has been updated.'
            ];

        } catch(\Exception $e){
            DB::rollback();
             return [
                'message' => 'error : ' . $e
            ];
        }  
    }
}
