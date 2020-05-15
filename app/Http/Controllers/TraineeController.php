<?php

namespace App\Http\Controllers;

use App\User;
use Validator;
use App\Trainee;
use App\Trainor;
use App\UserAccess;
use Illuminate\Http\Request;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\TraineeValidation;

class TraineeController extends Controller
{
	public function index()
	{
		$query = DB::select(
			'SELECT
				ts.trainee_id, 
				ts.fname, 
				ts.mname, 
				ts.lname, 
				ts.email,
				ts.created_at,
				d.dealer_name, 
				d.branch,
				u.is_approved,
				cc.classification,
				CONCAT(trs.lname,", ",trs.fname," ",COALESCE(trs.mname, "")) as trainor

			FROM trainees ts
				LEFT JOIN trainors trs
					ON trs.trainor_id = ts.trainor_id
				LEFT JOIN dealers d
					ON d.dealer_id = trs.dealer_id
				LEFT JOIN users u
					ON u.app_user_id = CONCAT("trainee_",ts.trainee_id)
				LEFT JOIN category_classifications cc
					ON cc.id = ts.classification_id
			ORDER BY 
				u.is_approved ASC,
				ts.created_at ASC'
		);

		return response()->json(['trainees' => $query]);
	}

	public function show($trainee_id)
	{
		$trainees = DB::table('trainees as ts')
			->select(
				'ts.*', 
				DB::raw('CONCAT(trs.lname,", ",trs.fname," ",COALESCE(trs.mname, "")) as trainor_name'),
				'dr.dealer_name', 
				'dr.branch'
			)
			->leftJoin('trainors as trs', 'trs.trainor_id', '=', 'ts.trainor_id')
			->leftJoin('dealers as dr', 'dr.dealer_id', '=', 'trs.dealer_id')
			->where('ts.trainee_id', $trainee_id)
			->first();

		return response()->json($trainees);
	}

	public function store(TraineeValidation $request, EmailService $batch_email)
	{
		$trainor = Trainor::with('dealer')->findOrFail($request->trainor_id);

		try {
			DB::beginTransaction();

			$trainee = new Trainee;
			$trainee->trainor_id = $request->trainor_id;
			$trainee->dealer_id = $trainor->dealer_id;
			$trainee->fname     = $request->fname;
			$trainee->mname     = $request->mname;
			$trainee->lname     = $request->lname;
			$trainee->email     = $request->email;
			$trainee->classification_id     = $request->classification_id;
			$trainee->save();

			if ($trainee) {
				$app_user_id = 'trainee_' . $trainee->trainee_id;
				$name = $trainee->lname . ', ' . $trainee->fname . ' ' . $trainee->mname ?? NULL;
				$email = $trainee->email;
				$password = bcrypt(snake_case($trainee->lname));

				$user = new User;
				$user->app_user_id = $app_user_id;
				$user->name = $name;
				$user->email = $email;
				$user->password = $password;
				$user->user_type = 'trainee';
				$user->save();

				$user_access = UserAccess::select('et.email')
					->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
					->where([
						'system_id'    => config('app.system_id'),
						'user_type_id' => 2
					])
					->get();

				foreach ($user_access as $value) {
					$batch_email->batch_incoming_emails([
						'email_category' => 'basic',
						'subject'        => 'Trainee Created',
						'sender'         => config('mail.from.address'),
						'recipient'      => $value['email'], 
						'title'          => 'New Trainee <span style="color: #5caad2;">Created!</span>',
						'message'        => 'Trainee Name: <strong>' . $trainee->fname . ' ' . $trainee->lname . '</strong></br>' .
											' has registered by trainor <strong>' . $trainor->fname . ' ' . $trainor->lname . '</strong>' .
											' of <strong>' . $trainor->dealer->dealer_name .' | '. $trainor->dealer->branch.'.</strong>',
						'cc'             => null,
						'attachment'     => null
					]);
				}
			}

			DB::commit();
			return $trainee;
		}
		catch(Exception $ex) {
			DB::rollBack();
			return response('Bad Request', 400);
		}
	}

	public function update(Request $request, $trainee_id)
	{
		$trainor = Trainor::findOrFail($request->trainor_id);

		try {
			DB::beginTransaction();

			$trainee = Trainee::findOrFail($trainee_id);
			$trainee->trainor_id = $request->trainor_id;
			$trainee->dealer_id = $trainor->dealer_id;
			$trainee->fname = $request->fname;
			$trainee->mname = $request->mname;
			$trainee->lname = $request->lname;
			$trainee->classification_id = $request->classification_id;
	
			if ($trainee->email == $request->email) {}
			else {
				$request->validate([
					'email' => 'required|string|email|max:255|unique:users|unique:trainees'
				]);
				$trainee->email = $request->email;
			}
	
			$trainee->save();
	
			if ($trainee) {
				$app_user_id = 'trainee_' . $trainee->trainee_id;
				$email = $trainee->email;

				$user = User::where('app_user_id', $app_user_id)->first();
				$user->email = $email;
				$user->save();
			}

			DB::commit();
			return $trainee;
		}
		catch(Exception $ex) {
			DB::rollBack();
			return response('Bad Request', 400);
		}
	}

	public function destroy($trainee_id)
	{
		try {
			DB::beginTransaction();

			$trainee = Trainee::findOrFail($trainee_id);
			$trainee->delete();

			if ($trainee) {
				$app_user_id = 'trainee_' . $trainee->trainee_id;
				User::where('app_user_id', $app_user_id)->forceDelete();
			}

			DB::commit();
			return $trainee;
		}
		catch(Exception $ex) {
			DB::rollBack();
			return response('Bad Request', 400);
		}
	}

	public function approve_registration($trainee_id)
	{
		$trainee = Trainee::findOrFail($trainee_id);
		if ($trainee) {
			$app_user_id = 'trainee_' . $trainee->trainee_id;
			$query = User::where('app_user_id', $app_user_id)->update([
				'is_approved' => 1
			]);

			return response()->json($query);
		}
	}
}
