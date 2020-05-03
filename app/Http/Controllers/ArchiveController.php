<?php

namespace App\Http\Controllers;

use App\User;
use App\Trainor;
use Illuminate\Http\Request;

class ArchiveController extends Controller
{
    public function archive_trainors()
    {
        return Trainor::select('trainors.*', 'd.dealer_name', 'd.branch')
            ->leftJoin('dealers as d', 'd.dealer_id', '=', 'trainors.dealer_id')
            ->onlyTrashed()
            ->orderBy('trainors.created_at', 'ASC')
            ->get();
    }

    public function retrieve_trainor($trainor_id)
    {
        $query = Trainor::where('trainor_id', $trainor_id)->restore();

        if ($query) User::where('app_user_id', 'trainor_'.$trainor_id)->restore();

        return $query;
    }

    public function delete_trainor($trainor_id)
    {
        $query = Trainor::withTrashed()->findOrFail($trainor_id);
        $query->forceDelete();

        return response()->json($query);
    }
}