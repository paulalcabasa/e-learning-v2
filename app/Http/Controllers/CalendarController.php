<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Trainor;
use App\Dealer;
use App\ExamDetail;
use App\ModuleDetail;
use Illuminate\Http\Request;
use DB;

class CalendarController extends Controller
{
	public function get_events()
	{
		// $data = $this->getModuleSchedules($this->getDealerId());
		$events = [];
		// foreach ($data as $key => $value) {
		// 	$end = Carbon::parse($value['end_date'])->addDays(1);
		// 	$now = Carbon::now();

		// 	$events[] = \Calendar::event(
		// 		'PDF: ' . $value['module_schedule']['module']['module'], //event title
		// 		true, //full day event?
		// 		Carbon::parse($value['start_date'])->format('y-m-d'), //start time (you can also use Carbon instead of DateTime)
		// 		Carbon::parse($value['end_date'])->addDays(1)->format('y-m-d'), //end time (you can also use Carbon instead of DateTime)
		// 		$key, //optionally, you can specify an event ID
		// 		[
		// 			'color' => $end > $now ? '#7CB342' : '#E53935'
		// 		]
		// 	);
		// }

		$exam_schedules = $this->getExamSchedules($this->getDealerId());
	/* 	echo "<pre>";
		print_r($exam_schedules);
		die; */
		foreach ($exam_schedules as $key => $value) {
			$end = Carbon::parse($value->end_date)->addDays(1);
			$now = Carbon::now();

			$events[] = \Calendar::event(
				'EXAM: ' . $value->module, //event title
				true, //full day event?
				Carbon::parse($value->start_date)->format('y-m-d'), //start time (you can also use Carbon instead of DateTime)
				Carbon::parse($value->end_date)->addDays(1)->format('y-m-d'), //end time (you can also use Carbon instead of DateTime)
				$key, //optionally, you can specify an event ID
				[
					'color' => $end > $now ? '#29B6F6' : '#E53935'
				]
			);
		}

		$calendar = \Calendar::addEvents($events)
			->setOptions([
				'navLinks' => true,
				// 'editable' => true,
			]);

		return view('trainor.calendar', compact('calendar'));
	}

	public function getDealerId()
	{
		$trainor_id = str_replace_last('trainor_', '', Auth::user()->app_user_id);
		$trainor_details = Trainor::findOrFail($trainor_id);

		return $trainor_details->dealer_id;
	}

	public function getModuleSchedules($dealer_id)
	{
		$module_details_schedules = ModuleDetail::with('module_schedule.module')
			->where('dealer_id', $dealer_id)
			->get();

		return $module_details_schedules;
	}

	public function getExamSchedules($dealer_id)
	{
	/* 	$module_details_schedules = ExamDetail::with('exam_schedule.module')
			->where('dealer_id', $dealer_id)
			->get(); */
		$trainor_id = str_replace_last('trainor_', '', Auth::user()->app_user_id);
		$sql = "SELECT es.exam_schedule_id, 
						es.created_by, 
						es.created_at, 
						es.timer, 
						es.status , 
						m.module,
						ct.category_name,
						ed.is_opened,
						ed.is_enabled,
						ed.end_date,
						ed.start_date
			FROM exam_schedules es
				LEFT JOIN modules m
					ON m.module_id = es.module_id
				LEFT JOIN exam_details ed
					ON ed.exam_schedule_id = es.exam_schedule_id
				LEFT JOIN trainor_categories ca
					ON ca.category_id = m.category_id
				LEFT JOIN categories ct
					ON ct.id = ca.category_id
			WHERE ed.is_opened = 1
				AND ca.trainor_id = :trainor_id
				AND ed.dealer_id = :dealer_id
			";
		
		$module_details_schedules = DB::select($sql, [
			'trainor_id' => $trainor_id,
			'dealer_id' => $dealer_id
		]);

		return $module_details_schedules;
	}
}
