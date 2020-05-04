<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TrainorCategory;

class TrainorCategoryController extends Controller
{
    public function get(Request $request){
        $trainorCategory = new TrainorCategory;
        $allowedCategories = $trainorCategory->getTrainorCategories($request->trainor_id);
        $trainorCategories = [];
        foreach($allowedCategories as $row){
            array_push($trainorCategories,[
                'category_id' => $row->id,
                'category_name' => $row->category_name,
                'trainor_category_id' => $row->trainor_category_id,
                'is_checked' => $row->trainor_category_id !== null ? true : false
            ]);
        }
        return $trainorCategories;
    }

    public function store(Request $request){
        $trainor_id = $request->trainorId;
        $trainorCategory = new TrainorCategory;
        $trainorCategory->deleteByTrainor($trainor_id);

        $allowedCategories = $request->trainorCategories;

        $params = [];


        foreach($allowedCategories as $row){
            if($row['is_checked']){
                array_push($params,[
                    'trainor_id'  => $trainor_id,
                    'category_id' => $row['category_id']
                ]);
            }
        }

        $trainorCategory->batchInsert($params);

        return [
            'message' => 'Allowed categories has been updated.'
        ];
    }
}
