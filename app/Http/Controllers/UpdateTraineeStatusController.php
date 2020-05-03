<?php

namespace App\Http\Controllers;

use App\User;
use App\Trainee;
use App\Trainor;
use App\Services\RemoveUser;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class UpdateTraineeStatusController extends Controller
{
    /**
     * status = [1,2] "Approve or Disapprove"
     */
    public function update_trainee($trainee_id, $status, RemoveUser $remove, EmailService $batch_email)
    {
        $trainee = Trainee::with('trainor')->findOrFail($trainee_id);

        if ($status == 2) {
            $query = $remove->trainee($trainee_id);

            if ($query) {
                $send = $batch_email->batch_incoming_emails([
                    'email_category' => 'basic',
                    'subject'        => 'Status Update',
                    'sender'         => config('mail.from.address'),
                    'recipient'      => $trainee->trainor->email, 
                    'title'          => 'Status <span style="color: #5caad2;">Update!</span>',
                    'message'        => 'Sorry, your trainee <strong>' . $trainee->fname . ' ' . $trainee->lname . '</strong> was <strong>disapproved</strong> by an IPC Administrator. 
                                        Because he/she is not yet registered on our Database. Registration not succeeded.',
                    'cc'             => null,
                    'attachment'     => null
                ]);

                return response()->json($send);
            }
        }
        else {
            $app_user_id = 'trainee_' . $trainee_id;
            $query = User::where('app_user_id', $app_user_id)->first();
            $query->is_approved = 1;
            $query->save();

            if ($query) {
                $send = $batch_email->batch_incoming_emails([
                    'email_category' => 'basic',
                    'subject'        => 'Status Update',
                    'sender'         => config('mail.from.address'),
                    'recipient'      => $trainee->trainor->email, 
                    'title'          => 'Status <span style="color: #5caad2;">Update!</span>',
                    'message'        => 'Your trainee <strong>' . $trainee->fname . ' ' . $trainee->lname . '</strong> was successfully <strong>approved</strong> by an IPC Administrator. 
                                        He/she can now login to IPC E-Learning System.',
                    'cc'             => null,
                    'attachment'     => null
                ]);

                return response()->json($send);
            }
        }
    }
}
