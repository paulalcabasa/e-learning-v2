<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Email extends Model
{
    protected $fillable = [
        'email_category',
        'subject',
        'sender',
        'recipient',
        'title',
        'message',
        'cc',
        'attachment',
        'sent_at'
    ];

    protected $primaryKey = 'email_id';
    public $timestamps = false;

    public function email_category()
    {
        return $this->belongsTo('App\EmailCategory', 'email_category_id', 'email_category_id');
    }
}
