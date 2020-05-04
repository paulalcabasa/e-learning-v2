<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Category;
use Auth;
use App\Models\TrainorCategory;

class CategoryController extends Controller
{
    public function index(){
      
        $categories = Category::all();
        return response()->json($categories,200);
    }

    public function trainorCategories(){
        $trainorCategory = new TrainorCategory;
        $trainor_id = str_replace("trainor_" , "", Auth::user()->app_user_id);
        $allowedCategories = $trainorCategory->getCategories($trainor_id);
        $data = [
            'trainorCategories' => $allowedCategories
        ];

        return view('trainor.categories', $data);
    }
}
