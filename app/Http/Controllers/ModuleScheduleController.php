<?php

namespace App\Http\Controllers;

use App\Services\UpdateStatus;
use App\ModuleDetail;
use App\ModuleSchedule;
use App\Http\Requests\ModuleScheduleValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ModuleScheduleController extends Controller
{
    public function __construct(UpdateStatus $status)
    {
        $status->update_module_detail_status();
    }

    public function index()
    {
        $module_schedules = DB::table('module_schedules as ms')
            ->select(
                'ms.*', // module_schedule
                'm.module'
            )
            ->leftJoin('modules as m', 'm.module_id', '=', 'ms.module_id')
            ->orderBy('ms.created_at', 'DESC')
            ->get();
        
        return response()->json(['module_schedules' => $module_schedules]);
    }

    public function show($module_schedule_id)
    {
        return response()->json(ModuleSchedule::findOrFail($module_schedule_id));
    }

    public function store(ModuleScheduleValidation $request)
    {
        return response()->json(ModuleSchedule::create($request->all()));
    }

    public function update(ModuleScheduleValidation $request, $module_schedule_id)
    {
        $module_schedule = ModuleSchedule::findOrFail($module_schedule_id);
        $module_schedule->status = $request->status;
        $module_schedule->save();

        return response()->json($module_schedule);
    }

    public function destroy($module_schedule_id)
    {
        $module_schedule = ModuleSchedule::findOrFail($module_schedule_id);
        $module_schedule->delete();

        return response()->json($module_schedule);
    }
}
