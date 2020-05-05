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

    public function store(Request $request){
        $category = new Category;
      
        $category->category_name = $request->category['category_name'];
        $category->save();
        return [
            'message' => 'Category has been created'
        ];
    }

    public function update(Request $request){
        $category = Category::find($request->category['category_id']);
        $category->category_name = $request->category['category_name'];
        $category->status = $request->category['status'];
        $category->save();
        return [
            'message' => 'Category has been updated.'
        ];
    }
}
