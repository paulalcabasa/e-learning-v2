<?php

namespace App\Services;

use App\ModuleDetail;
use Illuminate\Support\Facades\DB;

class LearningStatusService
{
    public function module_status($module_detail_id)
    {
        $module_detail = ModuleDetail::findOrFail($module_detail_id);

        $query = DB::table('module_schedules as ms')
            ->leftJoin('module_details as md', 'md.module_schedule_id', '=', 'ms.module_schedule_id')
            ->where([
                ['md.is_finished', '=', 0],
                ['ms.module_schedule_id', '=', $module_detail->module_schedule_id]
            ])
            ->exists();

        if (!$query) {
            $update = DB::table('module_schedules as ms')
                ->leftJoin('module_details as md', 'md.module_schedule_id', '=', 'ms.module_schedule_id')
                ->where('md.module_detail_id', $module_detail_id)
                ->update([
                    'ms.status' => 'completed'
                ]);

            return $update;
        }
    }

    // public function exam_status($exam_schedule_id)
    // {
    //     $query = DB::table('exam_schedules as es')
    //         ->leftJoin('exam_details as ed', 'ed.exam_schedule_id', '=', 'es.exam_schedule_id')
    //         ->where('ed.is_finished', 0)
    //         ->exists();

    //     if (!$query)
    //         $update = DB::table('exam_schedules')
    //             ->where('module_schedule_id', $module_schedule_id)
    //             ->update([
    //                 'status' => 'completed'
    //             ]);

    //         return $update;
    // }
}