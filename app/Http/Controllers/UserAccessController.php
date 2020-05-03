<?php

namespace App\Http\Controllers;

use App\UserAccess;
use Illuminate\Http\Request;

class UserAccessController extends Controller
{
    public function index($employee_id)
    {
        $query = UserAccess::where([
            ['employee_id', '=', $employee_id],
            ['system_id', '=', config('constants.SYSTEM_ID')],
        ])->exists();

        return response()->json($query);
    }
}
