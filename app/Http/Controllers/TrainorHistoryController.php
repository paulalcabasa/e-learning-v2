<?php

namespace App\Http\Controllers;

use App\TrainorHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrainorHistoryController extends Controller
{
    public function get_history($trainor_id)
    {
        return DB::table('trainor_histories as th')
            ->select(
                'th.trainor_history_id',
                DB::raw('CONCAT(trs.lname,", ",trs.fname," ",COALESCE(trs.mname, "")) as trainor'),
                'm.module',
                'm.description',
                'md.start_date',
                'md.end_date'
            )
            ->leftJoin('trainors as trs', 'trs.trainor_id', '=', 'th.trainor_id')
            ->leftJoin('module_details as md', 'md.module_detail_id', '=', 'th.module_detail_id')
            ->leftJoin('module_schedules as ms', 'ms.module_schedule_id', '=', 'md.module_schedule_id')
            ->leftJoin('modules as m', 'm.module_id', '=', 'ms.module_id')
            ->where('th.trainor_id', $trainor_id)
            ->get();
    }
}
