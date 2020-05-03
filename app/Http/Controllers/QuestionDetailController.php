<?php

namespace App\Http\Controllers;

use App\QuestionDetail;
use App\Http\Requests\QuestionDetailValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;

class QuestionDetailController extends Controller
{
    //--> /admin/question_details/get/{params?} :with optional parameters
    public function index()
    {
        $question_details = QuestionDetail::with('sub_module')->get();

        return response()->json(['question_details' => $question_details->toArray()]);
    }

    //--> /admin/question_detail/get/{question_detail_id}
    public function show($question_detail_id)
    {
        return response()->json(QuestionDetail::findOrFail($question_detail_id));
    }

    //--> /admin/question_detail/post/
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            foreach ($request->all() as $value) {
                if (isset($value['question_detail_id'])) {
                    if ($value['isSelected'] == FALSE  || $value['items'] == 0 || !isset($value['items'])) 
                        QuestionDetail::destroy($value['question_detail_id']);
                    
                    DB::table('question_details')
                        ->where('question_detail_id', $value['question_detail_id'])
                        ->update([
                            'items' => $value['items']
                        ]);
                }
                if (!isset($value['question_detail_id']) && isset($value['isSelected'])) {
                    DB::table('question_details')
                        ->insert([
                            'exam_schedule_id' => $value['exam_schedule_id'],
                            'sub_module_id'    => $value['sub_module_id'],
                            'items'            => $value['items']
                        ]);
                }
            }

            DB::commit();
            return response('Created', 201);
        }
        catch(Exception $ex) {
            DB::rollBack();
            return response('Bad Request', 400);
        }
    }

    //--> /admin/question_detail/put/{question_detail_id}
    public function update(Request $request, $question_detail_id)
    {
        
    }

    //--> /admin/question_detail/delete/{question_detail_id}
    public function destroy($question_detail_id)
    {
        $question_detail = QuestionDetail::findOrFail($question_detail_id);
        $question_detail->delete();

        return response()->json($question_detail);
    }
}
