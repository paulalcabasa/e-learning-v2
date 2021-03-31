<?php

namespace App\Http\Controllers;

use Auth;
use App\Trainee;
use App\Trainor;
use App\User;
use App\Services\SendEmail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UserProfileController extends Controller
{
	protected $mail;

	public function __construct(SendEmail $mail)
	{
		$this->mail = $mail;
	}
	
	public function trainor_profile($trainee_id)
	{
		return response()->json(Trainor::findOrFail($trainee_id));
	}

	public function trainee_profile($trainee_id)
	{
		return response()->json(Trainee::findOrFail($trainee_id));
	}

	public function update_profile(Request $request, $app_user_id)
	{
		$this->validate($request, [
			'fname' => 'required|string|max:255',
			'mname' => 'nullable|string|max:255',
			'lname' => 'required|string|max:255',
			'password' => 'nullable|string|min:6'
		]);

		$params = $request->all();

		$app_user_id = 'trainor_' . $app_user_id;
		$user = Trainor::where('trainor_id', $params['trainor_id'])->first();
		$user->fname = $params['fname'];
		$user->mname = $params['mname'];
		$user->lname = $params['lname'];
		
		$email_updated = 0;
		if ($user->email == $request->email) {}
		else {
			$request->validate([
				'email' => 'required|string|email|max:255|unique:trainors'
			]);
			$user->email = $params['email'];
			$email_updated = 1;
		}

		$user->save();

		if ($user) {
			$update_user = User::where('app_user_id', $app_user_id)->update([
				'name' => $user->lname .', '. $user->fname .' '. $user->mname,
				'email'=> $user->email
			]);

			if ($email_updated) {
				$logout = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 0]);
				Auth::logout();
				return response()->json(['status' => 'logout']);
			}

			if (isset($params['password'])) {
				$query = User::where('app_user_id', $app_user_id)->update(['password' => bcrypt($params['password'])]);
				if ($query) {
					$user = User::where('app_user_id', $app_user_id)->first();
					$logout = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 0]);
					$destroy_session = DB::table('sessions')->where('user_id', $user->user_id)->delete();
					return response()->json(['status' => 'logout']);
				}
			}

			return 200;
		}
	}

	public function trainee_update_profile(Request $request, $app_user_id)
	{
		$this->validate($request, [
			'fname' => 'required|string|max:255',
			'mname' => 'nullable|string|max:255',
			'lname' => 'required|string|max:255',
			'password' => 'nullable|string|min:6'
		]);

		$params = $request->all();

		$app_user_id = 'trainee_' . $app_user_id;
		$user = Trainee::where('trainee_id', $params['trainee_id'])->first();
		$user->fname = $params['fname'];
		$user->mname = $params['mname'];
		$user->lname = $params['lname'];
		
		$email_updated = 0;
		if ($user->email == $request->email) {}
		else {
			$request->validate([
				'email' => 'required|string|email|max:255|unique:trainees'
			]);
			$user->email = $params['email'];
			$email_updated = 1;
		}

		$user->save();

		if ($user) {
			$update_user = User::where('app_user_id', $app_user_id)->update([
				'name' => $user->lname .', '. $user->fname .' '. $user->mname,
				'email'=> $user->email
			]);

			if ($email_updated) {
				$logout = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 0]);
				Auth::logout();
				return response()->json(['status' => 'logout']);
			}

			if (isset($params['password'])) {
				$query = User::where('app_user_id', $app_user_id)->update(['password' => bcrypt($params['password'])]);
				if ($query) {
					$user = User::where('app_user_id', $app_user_id)->first();
					$logout = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 0]);
					$destroy_session = DB::table('sessions')->where('user_id', $user->user_id)->delete();
					return response()->json(['status' => 'logout']);
				}
			}

			return 200;
		}
	}

	public function reset_password($id, $user_type)
	{
		$append = $user_type == 'trainor' ? 'trainor_' : 'trainee_';
		$app_user_id = $append . $id;

		// Get user lastname
		$table = $user_type == 'trainor' ? 'trainors' : 'trainees';
		$user = DB::table($table)->where($user_type.'_id', $id)->first();
		//$lname = bcrypt(snake_case($user->lname));
		$lname = bcrypt(strtolower($user->lname));
	
		// Update
		$query = User::where('app_user_id', $app_user_id)->update(['password' => $lname]);

		if ($query)
			$this->mail->send([
				'email_category' => 'basic',
				'subject'	     => 'Password Reset',
				'sender'	     => config('mail.from.address'),
				'recipient'	     => $user->email,
				'cc'	         => NULL,
				'attachment'	 => NULL,
				'content'        => [
					'title'	   => 'Password <span style="color: #5caad2;">Reset!</span>',
					'message'  => 'Good Day! Your <strong>account password</strong> has been <strong>reset</strong>. </br>
					Your default password is your <span style="color: red;">lastname (all lower case)</span>. </br>
					Thank you!'
				]
			]);
	
		return response()->json($query);
	}
}
