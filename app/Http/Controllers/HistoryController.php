<?php

namespace App\Http\Controllers;

use App\Trainee;
use App\TrainingHistory;
use App\Services\HistoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\ExamDetail;

class HistoryController extends Controller
{
    public function get_trainee($trainee_id)
    {
        $query = DB::table('trainees as ts')
            ->select(
                'ts.trainee_id',
                'ts.fname',
                'ts.mname',
                'ts.lname',
                'ts.email',
                'd.dealer_name',
                'd.branch',
                'ts.created_at',
                'ts.updated_at'
            )
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'ts.dealer_id')
            ->where('ts.trainee_id', $trainee_id)
            ->first();

        return response()->json($query);
    }
    
    public function get_trainee_history($trainee_id)
    {
        return DB::table('training_histories as th')
            ->select(
                'th.training_history_id',
                DB::raw('CONCAT(ts.lname,", ",ts.fname," ",COALESCE(ts.mname, "")) as trainee'),
                'ts.email',
                'm.module',
                'm.description',
                'd.dealer_name',
                'd.branch',
                'th.result',
                'th.score',
                'th.date_taken',
                'th.updated_at as date_finished' 
            )
            ->leftJoin('modules as m', 'm.module_id', '=', 'th.module_id')
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'th.dealer_id')
            ->leftJoin('trainees as ts','ts.trainee_id', '=', 'th.trainee_id')
            ->leftJoin('trainee_exams as te','te.trainee_id', '=', 'ts.trainee_id')

            ->groupBy([
                'th.training_history_id'
            ])
            ->where([
                ['ts.trainee_id', '=', $trainee_id]
            ])
            ->get();
    }

    public function get_trainor_history($trainor_id)
    {
        return DB::table('training_histories as th')
            ->select(
                'th.training_history_id',
                't.trainor_id',
                DB::raw('CONCAT(t.lname,", ",t.fname) as trainor'),
                'm.module',
                'ed.start_date',
                'ed.end_date'
            )
            ->leftJoin('modules as m','m.module_id', '=', 'th.module_id')
            ->leftJoin('trainors as t','t.dealer_id', '=', 'th.dealer_id')
            ->leftJoin('trainee_exams as te', 'te.trainee_exam_id', '=', 'th.trainee_exam_id')
            ->leftJoin('exam_schedules as es', 'es.exam_schedule_id', '=', 'te.exam_schedule_id')
            ->leftJoin('exam_details as ed', 'ed.exam_schedule_id', '=', 'es.exam_schedule_id')
            ->where('t.trainor_id', $trainor_id)
            ->get();
    }
}
