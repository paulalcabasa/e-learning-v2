<?php

namespace App\Services;

use App\TrainorHistory;
use App\TrainingHistory;

class HistoryService
{
    public function save_training_history($params)
    {
        $query = new TrainingHistory;
        $query->module_id        = $params['module_id'];
        $query->exam_schedule_id = $params['exam_schedule_id'];
        $query->exam_detail_id   = $params['exam_detail_id'];
        $query->trainee_exam_id  = $params['trainee_exam_id'];
        $query->dealer_id        = $params['dealer_id'];
        $query->trainee_id       = $params['trainee_id'];
        $query->score            = $params['score'];
        $query->result           = $params['result'];
        $query->date_taken       = $params['date_taken'];
        $query->save();

        return $query;
    }

    public function save_trainor_history($params)
    {
        $query = new TrainorHistory;
        $query->trainor_id       = $params['trainor_id'];
        $query->module_detail_id = $params['module_detail_id'];
        $query->save();

        return $query;
    }
}
