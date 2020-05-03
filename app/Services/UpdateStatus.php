<?php

namespace App\Services;

use App\ModuleDetail;
use App\ExamDetail;
use App\ModuleSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UpdateStatus
{
    protected $current_date;

    public function __construct()
    {
        // Carbon::parse(now())->format('Y-m-d')
        $this->current_date = Carbon::now()->toDateString();
    }

    public function update_module_detail_status()
    {
        $module_details = ModuleDetail::all();
        foreach ($module_details as $value) {
            if 
            ($value->start_date <= $this->current_date && $value->end_date >= $this->current_date && $value->is_opened == '0') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'on_progress']);
            }
            else if 
            ($value->start_date <= $this->current_date && $value->end_date >= $this->current_date && $value->is_opened == '1') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'on_progress']);
            }
            else if 
            ($value->start_date > $this->current_date && $value->end_date > $this->current_date && $value->is_opened == '0') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'waiting']);
            }
            else if 
            ($value->start_date > $this->current_date && $value->end_date > $this->current_date && $value->is_opened == '1') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'waiting']);
            }
            else if 
            ($value->start_date < $this->current_date || $value->end_date < $this->current_date && $value->is_opened == '0') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'ended']);
            }
            else if 
            ($value->start_date < $this->current_date || $value->end_date < $this->current_date && $value->is_opened == '1') {
                DB::table('module_details')->where('module_detail_id', $value->module_detail_id)->update(['status' => 'ended']);
            }
        }
    }

    public function update_exam_detail_status()
    {
        $exam_details = ExamDetail::all();
        foreach ($exam_details as $value) {
            if 
            ($value->start_date <= $this->current_date && $value->end_date >= $this->current_date && $value->is_opened == '0') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'on_progress']);
            }
            else if 
            ($value->start_date <= $this->current_date && $value->end_date >= $this->current_date && $value->is_opened == '1') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'on_progress']);
            }
            else if 
            ($value->start_date > $this->current_date && $value->end_date > $this->current_date && $value->is_opened == '0') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'waiting']);
            }
            else if 
            ($value->start_date > $this->current_date && $value->end_date > $this->current_date && $value->is_opened == '1') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'waiting']);
            }
            else if 
            ($value->start_date < $this->current_date || $value->end_date < $this->current_date && $value->is_opened == '0') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'ended']);
            }
            else if 
            ($value->start_date < $this->current_date || $value->end_date < $this->current_date && $value->is_opened == '1') {
                DB::table('exam_details')->where('exam_detail_id', $value->exam_detail_id)->update(['status' => 'ended']);
            }
        }
    }
}