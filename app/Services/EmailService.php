<?php

namespace App\Services;

use App\Email;

class EmailService
{
    public function batch_incoming_emails($params)
    {
        $query = new Email;
        $query->email_category = $params['email_category'];
        $query->subject        = $params['subject'];
        $query->sender         = $params['sender'];
        $query->recipient      = $params['recipient'];
        $query->title          = $params['title'];
        $query->message        = $params['message'];
        $query->cc             = $params['cc'];
        $query->attachment     = $params['attachment'];
        $query->save();

        return 200;
    }
}