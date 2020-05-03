<?php

namespace App\Http\Controllers;

use Auth;
use Carbon\Carbon;
use App\Trainor;
use App\Dealer;
use App\ExamDetail;
use App\ModuleDetail;
use Illuminate\Http\Request;

class AdminCalendarController extends Controller
{
    public function get_events()
	{
		$events = [];

		if (isset($_GET['filter'])) {
			$events = $this->filtered_events($_GET['filter']);
		}
		else {
			$filter = NULL;
			$events = $this->filtered_events($filter);
		}

        $calendar = \Calendar::addEvents($events)
            ->setOptions([
                'navLinks' => true,
                // 'editable' => true,
            ]);

		return view('contents.calendar.calendar', compact('calendar'));
	}
	
	public function filtered_events($filter)
	{
		$events = [];
		if ($filter == 'pdf' || $filter == 'all' || $filter == NULL) {
			$modules = $this->getModuleSchedules();
			foreach ($modules as $key => $value) {
				$end = Carbon::parse($value['end_date'])->addDays(1);
				$now = Carbon::now();
	
				$events[] = \Calendar::event(
					'PDF: ' . $value['module_schedule']['module']['module'] . ' | ' . $value['dealer']['dealer_name'] . ' - ' . $value['dealer']['branch'], //event title
					true, //full day event?
					Carbon::parse($value['start_date'])->format('y-m-d'), //start time (you can also use Carbon instead of DateTime)
					Carbon::parse($value['end_date'])->addDays(1)->format('y-m-d'), //end time (you can also use Carbon instead of DateTime)
					$key, //optionally, you can specify an event ID
					[
						'color' => $end > $now ? '#039BE5' : '#E53935'
					]
				);
			}
		}
		
		if ($filter == 'exam' || $filter == 'all' || $filter == NULL) {
			$exams = $this->getExamSchedules();
			foreach ($exams as $key => $value) {
				$end = Carbon::parse($value['end_date'])->addDays(1);
				$now = Carbon::now();
	
				$events[] = \Calendar::event(
					'EXAM: ' . $value['exam_schedule']['module']['module'] . ' | ' . $value['dealer']['dealer_name'] . ' - ' . $value['dealer']['branch'], //event title
					true, //full day event?
					Carbon::parse($value['start_date'])->format('y-m-d'), //start time (you can also use Carbon instead of DateTime)
					Carbon::parse($value['end_date'])->addDays(1)->format('y-m-d'), //end time (you can also use Carbon instead of DateTime)
					$key, //optionally, you can specify an event ID
					[
						'color' => $end > $now ? '#7CB342' : '#E53935'
					]
				);
			}
		}

		return $events;
	}

	public function exam_events() 
	{
		$events = [];

		$exams = $this->getExamSchedules();
		foreach ($exams as $key => $value) {
			$end = Carbon::parse($value['end_date'])->addDays(1);
			$now = Carbon::now();

			$events[] = \Calendar::event(
				'EXAM: ' . $value['exam_schedule']['module']['module'] . ' | ' . $value['dealer']['dealer_name'] . ' - ' . $value['dealer']['branch'], //event title
				true, //full day event?
				Carbon::parse($value['start_date'])->format('y-m-d'), //start time (you can also use Carbon instead of DateTime)
				Carbon::parse($value['end_date'])->addDays(1)->format('y-m-d'), //end time (you can also use Carbon instead of DateTime)
				$key, //optionally, you can specify an event ID
				[
					'color' => $end > $now ? '#7CB342' : '#E53935'
				]
			);
		}

		return $events;
	}

    public function getModuleSchedules()
	{
		return ModuleDetail::with([
            'dealer',
            'module_schedule.module'
        ])->get();
	}

	public function getExamSchedules()
	{
		return ExamDetail::with([
            'dealer',
            'exam_schedule.module'
        ])->get();
	}
}
