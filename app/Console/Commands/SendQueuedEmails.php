<?php

namespace App\Console\Commands;

use App\Email;

use App\Services\SendEmail;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendQueuedEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queued_email:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Queued Email from Database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    protected $mail;

	public function __construct(SendEmail $mail)
	{
		parent::__construct();
		
		$this->mail = $mail;
	}

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $pending_emails = Email::where('sent_at', NULL)->get();

        $bar = $this->output->createProgressBar(count($pending_emails));
		if ($pending_emails) {
            foreach ($pending_emails as $value) {

                $bar->setFormat('debug');
                $bar->setProgressCharacter('|');
				$mail = $this->mail->send([
					'email_category' => $value['email_category'],
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

                $query = Email::findOrFail($value['email_id']);
                $query->sent_at = NOW();
                $query->save();

                $bar->advance();
            }
            $bar->finish();
            return $this->info('All Emails Successfully Sent!');
        }
    }
}
