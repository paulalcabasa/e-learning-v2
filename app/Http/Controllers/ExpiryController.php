<?php

namespace App\Http\Controllers;

use App\ModuleDetail;
use App\ExamDetail;
use Illuminate\Http\Request;

class ExpiryController extends Controller
{
    public function update_module_status(Request $request, $module_detail_id)
    {
        $module_detail = ModuleDetail::findOrFail($module_detail_id);
        $module_detail->status = $request->status;
        $module_detail->save();

        return response()->json($module_detail);
    }

    public function update_exam_status(Request $request, $exam_detail_id)
    {
        $exam_detail = ExamDetail::findOrFail($exam_detail_id);
        $exam_detail->status = $request->status;
        $exam_detail->save();

        return response()->json($exam_detail);
    }
}