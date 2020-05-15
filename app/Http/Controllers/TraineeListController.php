<?php

namespace App\Http\Controllers;

use App\Trainor;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TraineeListController extends Controller
{
    public function trainee_list($trainor_id)
    {
        $trainor = Trainor::findOrFail($trainor_id);

        return DB::table('trainees as ts')
            ->select(
                'ts.*',
                'trs.trainor_id',
                'cc.classification',
                DB::raw('CONCAT(trs.lname,", ",trs.fname," ",COALESCE(trs.mname, "")) as trainor')
            )
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'ts.dealer_id')
            ->leftJoin('trainors as trs', 'trs.trainor_id', '=', 'ts.trainor_id')
            ->leftJoin('category_classifications as cc', 'cc.id','=','ts.classification_id')
            ->where('ts.dealer_id', $trainor->dealer_id)
            ->get();
    }
}