<?php

namespace App\Http\Controllers;

use App\User;
use App\Trainor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use App\Http\Requests\TrainorValidation;

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
}