<?php

namespace App\Services;

use App\Trainor;
use App\Trainee;
use App\User;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;

class RemoveUser
{
    public function trainee($trainee_id)
    {
        try {
            DB::beginTransaction();

            $trainee = Trainee::findOrFail($trainee_id);
            $trainee->delete();

            if ($trainee) {
                $app_user_id = 'trainee_' . $trainee->trainee_id;
                User::where('app_user_id', $app_user_id)->delete();
                $user = DB::table('users')->where('app_user_id', $app_user_id)->delete();
            }

            DB::commit();
            return $trainee;
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }
}