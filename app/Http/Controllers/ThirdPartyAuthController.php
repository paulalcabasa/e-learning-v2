<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ThirdPartyAuthController extends Controller
{
    public function check_logging(Request $request)
    {
        return response()->json(DB::table('users')->where('email', $request->email)->first());
    }

    public function logout()
    {
        $query = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 0]);
        
        return response()->json($query);
    }

    public function check_user(Request $request)
    {
        return response()->json(DB::table('users')->where('email', $request->email)->first());
    }
}
