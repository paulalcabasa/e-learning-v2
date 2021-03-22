<?php

namespace App\Http\Controllers;

use App\User;
use App\Trainor;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use App\Http\Requests\TrainorValidation;
use App\Category;
use App\Models\TrainorCategory;

class TrainorController extends Controller
{
    public function index()
    {
        $trainors = Trainor::select('trainors.*', 'd.dealer_name', 'd.branch')
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'trainors.dealer_id')
            ->orderBy('trainors.created_at', 'ASC')
            ->withTrashed()
            ->get();

        return response()->json(['trainors' => $trainors]);
    }

    public function show($trainor_id)
    {
        $trainor = Trainor::select('trainors.*', 'd.dealer_name', 'd.branch')
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'trainors.dealer_id')
            ->where('trainors.trainor_id', $trainor_id)
            ->first();

        return response()->json($trainor);
    }

    public function store(TrainorValidation $request)
    {
       try {
            DB::beginTransaction();

            $trainor = new Trainor;
            $trainor->dealer_id = $request->dealer_id;
            $trainor->fname = $request->fname;
            $trainor->mname = $request->mname;
            $trainor->lname = $request->lname;
            $trainor->email = $request->email;
            $trainor->save();

            if ($trainor) {
                $app_user_id = 'trainor_' . $trainor->trainor_id;
                $name = $trainor->lname . ', ' . $trainor->fname . ' ' . $trainor->mname ?? NULL;
                $email = $trainor->email;
                $password = bcrypt(str_replace(["-", "–"], '', snake_case($trainor->lname)));

                $user = new User;
                $user->app_user_id = $app_user_id;
                $user->name = $name;
                $user->email = $email;
                $user->password = $password;
                $user->user_type = 'trainor';
                $user->is_approved = 1;
                $user->save();
            }

            DB::commit();
            return $trainor;
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }

    public function update(Request $request, $trainor_id)
    {
        try {
            DB::beginTransaction();

            $trainor = Trainor::findOrFail($trainor_id);
            $trainor->dealer_id = $request->dealer_id;
            $trainor->fname = $request->fname;
            $trainor->mname = $request->mname;
            $trainor->lname = $request->lname;

            if ($trainor->email == $request->email) {}
            else {
                $request->validate([
                    'email' => 'required|string|email|max:255|unique:trainors'
                ]);
                $trainor->email = $request->email;
            }

            $trainor->save();
    
            if ($trainor) {
                $app_user_id = 'trainor_' . $trainor->trainor_id;
                $email = $trainor->email;

                $user = User::where('app_user_id', $app_user_id)->first();
                $user->email = $email;
                $user->save();
            }

            DB::commit();
            return $trainor;
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }

    public function destroy($trainor_id)
    {
        try {
            DB::beginTransaction();

            $trainor = Trainor::findOrFail($trainor_id);
            $trainor->delete();

            if ($trainor) {
                $app_user_id = 'trainor_' . $trainor->trainor_id;
                User::where('app_user_id', $app_user_id)->delete();
            }

            DB::commit();
            return $trainor;
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }

    public function modules(Request $request){
        $category_id = $request->category_id;
        $trainor_id =  str_replace("trainor_", "", Auth::user()->app_user_id);
        
        $category = Category::findOrFail($category_id);
       // dd($category->category_name);
        $files = [];
        $dir = 'C:\\wamp64\e-learning\\public\\storage\\ftp-media\\' . strtolower($category->category_name);
        $path    = 'C:\\wamp64\www\\e-learning\\public\\storage\\ftp-media\\' . strtolower($category->category_name);
        if(is_dir($path)){
            $files = array_diff(scandir($path), array('.', '..'));
        }

        $trainor = Trainor::findOrFail($trainor_id);
        $trainorCategory = new TrainorCategory;

        $access = $trainorCategory->validateAccess([
            'category_id' => $category_id,
            'trainor_id' => $trainor_id
        ]);
        
        if(count($access) <= 0){
            abort(404);
        }
        $dealer_id = $trainor->dealer->dealer_id;

        $modules = Module::with([
            'module_details' => function($query) use($dealer_id) {
                $query->where('dealer_id', $dealer_id);
            }
        ])
        ->where('category_id', $category_id)
        ->get(); 
   
        $data = [
            'trainor_modules' => $modules->toArray(),
            'category' => $category,
            'category_id' => $category_id,
            'files' => $files,
            'path' => url('/') . '/public/storage/ftp-media/' . strtolower($category->category_name) . '/'
        ];
        return view('trainor.modules',$data);
    }

    
}