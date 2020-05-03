<?php

namespace App\Http\Controllers;

use App\Email;
use App\Services\SendEmail;
use Illuminate\Http\Request;

class SendEmailController extends Controller
{
    public function send_bulk_emails(SendEmail $mail)
    {
        $pending_emails = Email::where('sent_at', NULL)->get();

        if ($pending_emails) {
            foreach ($pending_emails as $value) {
                $mail->send([
                    'subject'	     => $value['subject'],
                    'sender'	     => $value['sender'],
                    'recipient'	     => $value['recipient'],
                    'cc'	         => $value['cc'],
                    'attachment'	 => $value['attachment'],
                    'content'        => [
                        'title'	   => $value['title'],
                        'message'  => $value['message']
                    ]
                ]);

                if ($mail) $this->update_batch_email($value['email_id']);
            }
        }
    }

    public function update_batch_email($email_id)
    {
        $query = Email::findOrFail($email_id);
        $query->sent_at = NOW();
        $query->save();

        return true;
    }
}
