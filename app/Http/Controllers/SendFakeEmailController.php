<?php

namespace App\Http\Controllers;

use App\Services\SendEmail;
use App\Mail\ModuleCreated;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;

class SendFakeEmailController extends Controller
{
    public function send(SendEmail $mail)
    {
        $res = $mail->send([
            'email_category' => 'creation',
            'subject'	     => 'New Module Schedule',
            'sender'	     => config('mail.from.address'),
            'recipient'	     => 'princeivankentmtiburcio@gmail.com',
            'cc'	         => NULL,
            'attachment'	 => NULL,
            'content'	     => [
                'title' => 'You have new <span style="color: #5caad2;">Schedule!</span>',
                'message' => 
                    'Hi Mam/Sir, <strong>IPC Administration</strong> has been created a new schedule for a module viewing. <br>
                    Please click the button to navigate directly to your system.'
            ]
        ]);

        return 200;
    }
}
