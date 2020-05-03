<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActiveUserController extends Controller
{
	public function users()
	{
		$query = DB::table('users as u')
			->select(
				'u.*',
				's.ip_address',
				's.last_activity',
				's.user_agent'
			)
			->leftJoin('sessions as s', 's.user_id', '=', 'u.user_id')
			->oldest('u.created_at')
			->get();

		return response()->json($query);
	}

	public function logout(Request $request)
	{
		$user_id = $request->user_id;
		try {
			$query = DB::table('users')
				->where('user_id', $user_id)
				->update(['is_active' => 0]);

			//--> Delete user session on session's table
			if ($query) $query = DB::table('sessions')->where('user_id', $user_id)->delete(); 
			
			return response()->json($query);
		}
		catch(Exception $e) {
			report($e);

			return false;
		}
	}
}
