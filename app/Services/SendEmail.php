<?php

namespace App\Services;

use App\Mail\ModuleCreated;
use Illuminate\Support\Facades\Mail;

class SendEmail
{
    public function send($params)
    {
        $data = [
            'email_category' => $params['email_category'],
            'subject'	     => $params['subject'],
            'sender'	     => $params['sender'],
            'recipient'	     => $params['recipient'],
            'cc'	         => $params['cc'],
            'attachment'	 => $params['attachment'],
            'content'	     => $params['content']
        ];

        $template = 'email_template';
        if ($data['email_category'] == 'opened') $template = 'admin_template';
        else if ($data['email_category'] == 'others') $template = 'notification_template';
        else if ($data['email_category'] == 'basic') $template = 'basic_template';

        return Mail::send('emails.' . $template, ['content' => $data['content']], function ($mail) use ($data) {
            $mail->from($data['sender'], 'IPC E-learning System');
            $mail->to($data['recipient'])->subject($data['subject']);
            
            if (isset($data['cc'])) $mail->cc($data['cc']);
            if (isset($data['attachment'])) $mail->attach($data['attachment']);
        });
    }
}