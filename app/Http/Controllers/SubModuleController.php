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

class SubModuleController extends Controller
{
    public function modules($params = '')
    {
        /* $modules = DB::table('modules')
            ->select(
                'module_id',
                'module',
                'description',
                'file_name',
                'is_active',
                'created_at',
                'updated_at'
            )
            ->where('is_active', 1)
            ->where('module', 'like', '%' . $params . '%')
            ->orWhere('description', 'like', '%' . $params . '%')
            ->orWhere('file_name', 'like', '%' . $params . '%')
            ->oldest('created_at')
            ->get(); */
        $module = new Module;
        $modules = $module->getModules(session('employee_id'));
        return $modules;
    } 

    public function index($id = '')
    {
        if ($id == '') {
            return view('contents.sub-modules.sub_modules', [
                'modules' => $this->modules()
            ]);
        }
        else {
            $module = Module::findOrFail($id);

            $submodules = $module->submodules()
                ->where('is_active', 1)
                ->get();

            return view('contents.sub-modules.sub_modules', [
                'submodules' => $submodules,
                'modules' => $this->modules(),
                'single_module' => $module
            ]);
        }
    }

    // AJAX Request
    public function submodules($module_id)
    {
        $submodules = DB::table('sub_modules as s')
            ->select(
                's.*',
                DB::raw('COUNT(q.question_id) as questions')
            )
            ->leftJoin('questions as q', 'q.sub_module_id', '=', 's.sub_module_id')
            ->where([
                's.module_id' => $module_id,
                's.is_active' => '1'
            ])
            ->groupBy('s.sub_module_id')
            ->get();

        return response()->json([
            'submodules' => $submodules,
            'modules' => $this->modules()
        ]);
    }

    public function store(SubModuleValidation $request)
    {
        return SubModule::create($request->all());
    }

    public function update(Request $request, $submodule_id)
    {
        $submodule = SubModule::findOrFail($submodule_id);
        $submodule->sub_module = $request->sub_module;
        $submodule->save();

        return $submodule;
    }

    public function destroy($id)
    {
        $submodule = SubModule::findOrFail($id);
        $submodule->delete();

        return $submodule;
    }

    // ---------------------------------
    public function submodule($submodule_id)
    {
        return SubModule::findOrFail($submodule_id);
    }

    public function upload_pdf(Request $request, $id)
    {
        $submodule = SubModule::findOrFail($id);

        if ($request->hasFile('file_name')) {
            $request->file('file_name')->store('public');
            $file_name = $request->file('file_name')->hashName();

            $submodule->update(['file_name' => $file_name]);
        }

        return $submodule;
    }

    public function mod_sub_details($submodule_id)
    {
        $submodule = SubModule::findOrFail($submodule_id);
        $module = $submodule->module;
        $questions = $submodule->question;
        
        $choices = [];
        foreach ($questions as $key => $question) {
            $choices[$question->question_id] = $question->choice;
        }

        return view('contents.questions.questions',
            [
                'submodule' => $submodule,
                'module' => $module,
                'questions' => $questions 
            ]);
    }

    public function create_qa(QuestionAnswerValidation $request)
    {
        DB::transaction(function () use($request) {
            $question = new Question();
            $question->sub_module_id = $request->sub_module_id;
            $question->question = $request->question;
            $question->save();

            $choice = DB::table('choices')
                ->insert([
                    [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_a,
                        'choice'        => $request->choice_a
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_b,
                        'choice'        => $request->choice_b
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_c,
                        'choice'        => $request->choice_c
                    ], [
                        'question_id'   => $question->question_id,
                        'choice_letter' => $request->default_d,
                        'choice'        => $request->choice_d
                    ],
                ]);

            return $choice;
        });
    }
}
