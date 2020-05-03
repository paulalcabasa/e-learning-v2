<?php

namespace App\Http\Controllers;

use App\QuestionDetail;
use App\ExamDetail;
use App\Services\UpdateStatus;
use App\Http\Requests\ExamDetailValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ExamDetailController extends Controller
{
    public function __construct(UpdateStatus $status)
    {
        $status->update_exam_detail_status();
    }

    public function index()
    {
        $exam_details = DB::table('exam_details as ed')
            ->select(
                'ed.*', 
                'es.module_id',
                'dr.dealer_name', 
                'dr.branch'
            )
            ->leftJoin('exam_schedules as es', 'es.exam_schedule_id', '=', 'ed.exam_schedule_id')
            ->leftJoin('dealers as dr', 'dr.dealer_id', '=', 'ed.dealer_id')
            ->orderBy('ed.created_at', 'ASC')
            ->get();

        return response()->json(['exam_details' => $exam_details]);
    }

    public function dealers_exam_schedule($exam_schedule_id)
    {
        $dealers_schedule = DB::table('dealers as dr')
            ->select(
                DB::raw('COUNT(te.trainee_exam_id) as completed_exam'),
                DB::raw('COUNT(ts.trainee_id) as trainees'),
                'dr.dealer_id', 
                'dr.dealer_name', 
                'dr.branch', 

                'ed.exam_detail_id', 
                'ed.exam_schedule_id', 
                'ed.start_date', 
                'ed.end_date', 
                'ed.is_opened', 
                'ed.status', 
                'ed.is_enabled', 

                'm.module_id', 
                'm.module' 
            )
            ->leftJoin('exam_details as ed', 'ed.dealer_id', '=', 'dr.dealer_id')
            ->leftJoin('exam_schedules as es', 'es.exam_schedule_id', '=', 'ed.exam_schedule_id')
            ->leftJoin('modules as m', 'm.module_id', '=', 'es.module_id')

            ->leftJoin('trainees as ts', 'ts.dealer_id', '=', 'dr.dealer_id')
            ->leftJoin('trainee_exams as te', 'te.trainee_id', '=', 'ts.trainee_id')

            ->where([
                ['ed.exam_schedule_id', '=', $exam_schedule_id]
            ])
            ->orderBy('ed.created_at', 'DESC')
            ->groupBy('dr.dealer_id','ed.exam_detail_id')
            ->get();

        return response()->json(['dealers_schedule' => $dealers_schedule]);
    }

    public function show($exam_detail_id)
    {
        $exam_detail = DB::table('exam_details as ed')
            ->select(
                'ed.*', 
                'mds.start_date', 
                'mds.end_date', 
                'mds.module_id', 
                'md.module',
                'd.dealer_name',
                'd.branch'
            )
            ->leftJoin('module_details as mds', 'mds.module_detail_id', '=', 'ed.module_detail_id')
            ->leftJoin('modules as md', 'md.module_id', '=', 'mds.module_id')
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'mds.dealer_id')
            ->where('ed.exam_detail_id', $exam_detail_id)
            ->first();

            return response()->json($exam_detail);
    }

    public function store(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach ($request->all() as $key => $value) {
                DB::table('exam_details')
                    ->where('exam_detail_id', $value['exam_detail_id'])
                    ->update([
                        'start_date' => $value['start_date'],
                        'end_date'   => $value['end_date'],
                        'is_enabled' => $value['is_enabled']
                    ]);
            }

            DB::commit();
            return response('Created', 201);
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }

    public function update(ExamDetailValidation $request, $exam_detail_id)
    {
        $exam_detail = ExamDetail::findOrFail($exam_detail_id);
        $exam_detail->module_detail_id = $request->module_detail_id;
        $exam_detail->date_available = $request->date_available;
        $exam_detail->timer = $request->timer;
        $exam_detail->item_quantity = $request->item_quantity;
        $exam_detail->save();

        return response()->json($exam_detail);
    }

    public function destroy($exam_detail_id)
    {
        $exam_detail = ExamDetail::findOrFail($exam_detail_id);
        $exam_detail->delete();

        return response()->json($exam_detail);
    }
}
