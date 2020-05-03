<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\SubModuleValidation;
use App\Http\Requests\QuestionAnswerValidation;
use App\Models\Module;
use App\Models\SubModule;
use App\Models\Question;
use App\Models\Choice;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($submodule_id)
    {
        $submodule = SubModule::findOrFail($submodule_id);
        $module = $submodule->module;
        $questions = $submodule->questions;

        $choices = [];
        foreach ($questions as $question) {
            $choices[$question->question_id] = $question->choice;
        }

        return view('contents.questions.questions',
            [
                'submodule' => $submodule,
                'module' => $module,
                'questions' => $questions 
            ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($submodule_id)
    {
        $submodule = SubModule::findOrFail($submodule_id);
        $module = $submodule->module;

        return view('contents.questions.create_question', [
            'submodule' => $submodule,
            'module' => $module,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(QuestionAnswerValidation $request)
    {

        try {
            DB::beginTransaction();
            $question = new Question();
            $question->sub_module_id = $request->sub_module_id;
            $question->question = $request->question;
            $question->save();
    
            $choice = DB::table('choices')
                ->insert([
                    [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_a,
                        'choice'        => $request->choice_a,
                        'is_correct'    => $request->correct_answer == 'a' ? 1 : 0
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_b,
                        'choice'        => $request->choice_b,
                        'is_correct'    => $request->correct_answer == 'b' ? 1 : 0
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_c,
                        'choice'        => $request->choice_c,
                        'is_correct'    => $request->correct_answer == 'c' ? 1 : 0
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_d,
                        'choice'        => $request->choice_d,
                        'is_correct'    => $request->correct_answer == 'd' ? 1 : 0
                    ],
                ]);
            
            $notification = array(
                'title' => 'Success!',
                'message' => 'Successfully created.', 
                'alert-type' => 'success'
            );
    
            DB::commit();
            return back()->with($notification);
        }
        catch (Exception $e) {
            DB::rollBack();

            $err_notification = array(
                'title' => 'Didn\'t save!',
                'message' => 'Something went wrong.', 
                'alert-type' => 'danger'
            );
            return back()->with($err_notification);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($question_id)
    {
        $question = Question::findOrFail($question_id);
        // $choices = $question->choice;

        return response()->json($question);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($submodule_id, $question_id)
    {
        $submodule = SubModule::findOrFail($submodule_id);
        $module = $submodule->module;

        $question = $this->show($question_id)->original;

        return view('contents.questions.edit_question', [
            'submodule' => $submodule,
            'module' => $module,
            'question' => $question
        ]);

        return $question->choices[0]->choice;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(QuestionAnswerValidation $request, $submodule_id, $question_id)
    {
        try {
            DB::beginTransaction();
            $question = Question::findOrFail($question_id);
            $question->question = $request->question;
            $question->save();
            
            /** Quick API for determining choice_letters, choice_value along its choice_id */
            $letters = ['a', 'b', 'c', 'd'];
            $choice_letter = 'choice_';
            $choice_id = 'choice_id_';

            foreach ($letters as $letter) {
                $choice_ids = $choice_id . $letter; // choice_id_{a} etc..
                $choice_value = $choice_letter . $letter; // choice_{a} etc..

                DB::table('choices')
                    ->where([
                        'question_id' => $question_id,
                        'choice_id'   => $request->$choice_ids
                    ])
                    ->update([
                        'choice' => $request->$choice_value,
                        'is_correct'    => $request->correct_answer == $letter ? 1 : 0
                    ]);
            }
            /** API Logic end */
            
            $notification = array(
                'title' => 'Success!',
                'message' => 'Successfully updated.', 
                'alert-type' => 'success'
            );
    
            DB::commit();
            return redirect()
                ->action('QuestionController@index', ['submodule_id' => $submodule_id])
                ->with($notification);
        }
        catch (Exception $e) {
            DB::rollBack();

            $err_notification = array(
                'title' => 'Didn\'t save!',
                'message' => 'Something went wrong.', 
                'alert-type' => 'danger'
            );
            return back()->with($err_notification);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($submodule_id, $question_id)
    {
        $question = Question::findOrFail($question_id);
        $question->delete();

        return $question;
    }
}
