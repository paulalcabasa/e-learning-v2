<?php

namespace App\Http\Controllers;

use App\UserAccess;
use App\Services\EmailService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function send_notification(Request $request, EmailService $batch_email)
    {
        $user_access = UserAccess::select('et.email')
            ->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
            ->where([
                'system_id'    => config('app.system_id'),
                'user_type_id' => 2
            ])
            ->get();

        foreach ($user_access as $value) {
            $batch_email->batch_incoming_emails([
                'email_category' => 'others',
                'subject'        => $request->subject,
                'sender'         => config('mail.from.address'),
                'recipient'      => $value['email'],
                'title'          => '<span style="color: #5caad2;">'. $request->subject .'</span>',
                'message'        =>  '
                    Name: <strong>'.$request->fullname.'</strong></br>
                    Email: <strong>'.$request->email.'</strong></br>
                    Mobile: <strong>'.$request->mobile.'</strong></br>
                    Message: <strong>'.$request->message.'</strong></br>
                ',
                'cc'             => null,
                'attachment'     => null
            ]);
        }

        return 200;
    }
}
