<?php

namespace App\Console\Commands;

use App\ModuleDetail;
use App\ExamDetail;

use Carbon\Carbon;
use App\Email;
use App\Services\SendEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class SendDailyEmails extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'daily_email:send';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Send daily e-mails to a user';

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
		$this->modules();
		$this->exams();
	}

	public function modules()
	{
		$ahead_one_day = Carbon::now()->addDays(1)->toDateString();
		$now = Carbon::now()->toDateString();

		$module_emails = DB::table('module_details as md')
			->select(
				'trs.email',
				'md.start_date',
				'md.end_date',
				'md.is_opened'
			)
			->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'md.dealer_id')
			->where('deleted_at', NULL)
			->get();
		$bar = $this->output->createProgressBar(count($module_emails));
		foreach ($module_emails as $value) {
			$bar->setFormat('debug');
			$bar->setProgressCharacter('|');
			if ($value->start_date == $ahead_one_day) {

				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'before',
						'subject'	     => 'Upcoming Schedule: Module/PDF',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => 'paul-alcabasa@isuzuphil.com',
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'An upcoming <span style="color: #5caad2;">Schedule!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you about upcoming module viewing schedule tomorrow. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}

			if ($value->start_date == $now) {
				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'during',
						'subject'	     => 'Today\'s Reminder: Module/PDF',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => 'paul-alcabasa@isuzuphil.com',
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'Today\'s <span style="color: #5caad2;">Reminder Schedule!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you about your module viewing schedule today. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}

			if (Carbon::parse($value->end_date)->addDays(1)->toDateString() == $now && $value->is_opened == 0) { 
				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'after',
						'subject'	     => 'Expired: Module/PDF',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => NULL,
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'Sorry, Module/PDF viewing is <span style="color: #5caad2;">Expired!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you that you\'ve missed to view the module/pdf. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}
			$bar->advance();
		}
		$bar->finish();
	}

	public function exams()
	{
		$ahead_one_day = Carbon::now()->addDays(1)->toDateString();
		$now = Carbon::now()->toDateString();

		$exam_emails = DB::table('exam_details as ed')
			->select(
				'trs.email',
				'ed.start_date',
				'ed.end_date',
				'ed.is_opened'
			)
			->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'ed.dealer_id')
			->where('deleted_at', NULL)
			->get();

		$bar = $this->output->createProgressBar(count($exam_emails));
		foreach ($exam_emails as $value) {
			$bar->setFormat('debug');
			$bar->setProgressCharacter('|');

			if ($value->start_date == $ahead_one_day) {
				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'before',
						'subject'	     => 'Upcoming Schedule: Examination',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => NULL,
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'An upcoming <span style="color: #5caad2;">Schedule!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you about upcoming examination schedule tomorrow. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}

			if ($value->start_date == $now) {
				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'during',
						'subject'	     => 'Today\'s Reminder: Examination',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => NULL,
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'Today\'s <span style="color: #5caad2;">Reminder Schedule!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you about your examination schedule today. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}

			if (Carbon::parse($value->end_date)->addDays(1)->toDateString() == $now && $value->is_opened == 0) { 
				if (isset($value->email)) {
					$this->mail->send([
						'email_category' => 'after',
						'subject'	     => 'Expired: Examination',
						'sender'	     => config('mail.from.address'),
						'recipient'	     => $value->email,
						'cc'	         => NULL,
						'attachment'	 => NULL,
						'content'        => [
							'title'	   => 'Sorry, Examination is <span style="color: #5caad2;">Expired!</span>',
							'message'  => 'Greetings! <strong>IPC Administration</strong> is reminding you that you\'ve missed to take the examination. <br>
							Please click the button to navigate directly to our system.'
						]
					]);
				}
			}
			$bar->advance();
		}
		$bar->finish();
	}
}
