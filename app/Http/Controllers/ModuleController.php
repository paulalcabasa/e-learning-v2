<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ModuleValidation;
use App\Http\Requests\UploadValidation;
use App\Category;
use Auth;
class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = DB::select('SELECT id, category_name FROM categories');

        $data = [
            'categories' => $categories
        ];
        return view('contents.modules.modules',$data);
    }

    public function get() 
    {
        $module = new Module;
        $data = $module->getModules(session('employee_id'));
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ModuleValidation $request)
    {
        $data = new Module;
        $data->module = $request->module;
        $data->description = $request->description;
        $data->category_id = $request->category_id;
        
        if ($request->hasFile('file_name')) {
            $filename = $request->file('file_name')->getClientOriginalName();
            $request->file_name->move(public_path('storage'), $filename);

            $data->file_name = $filename;
        }
        
        $data->save();

        return $data;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $module = Module::findOrFail($id);
     
        $categories = Category::where('status','active')->get();
        $data = [
            'module' => $module,
            'categories' => $categories
        ];
        return view('contents.modules.module', $data);
    }

    public function get_module($id)
    {
        return response()->json(Module::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ModuleValidation $request, $id)
    {
        $module = Module::findOrFail($id);
        $module->module = $request->module;
        $module->description = $request->description;
        $module->category_id = $request->category_id;
        if ($request->hasFile('file_name')) {
            $filename = $request->file('file_name')->getClientOriginalName();
            $request->file_name->move(public_path('storage'), $filename); // move file to /public/storage

            $module->file_name = $filename;
        }

        $module->save();

       return response()->json($module);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        $module->delete();
        
        return $module;
    }

    // ------- My Controllers
    public function module($id) 
    {
        $module = $this->show($id);
        $modules = DB::table('modules as m')
            ->leftJoin('sub_modules as sm', 'm.module_id', '=', 'sm.module_id')
            ->where('m.module_id', $id)
            ->get();

        return view('contents.sub-modules.sub_modules', [
            'module'  => $module,
            'modules' => $modules
        ]);
    }

    public function display_pdf($module_id)
    {
        return Module::findOrFail($module_id);
    }

    public function sub_modules($module_id) 
    {
        $sub_modules = DB::select('
            SELECT sm.*,
            (SELECT COUNT(q.sub_module_id) FROM questions q WHERE q.sub_module_id = sm.sub_module_id) as count_total
            FROM sub_modules sm
            LEFT JOIN modules m
            ON m.module_id = sm.module_id
            WHERE m.module_id = ?
        ', [$module_id]);

        return response()->json($sub_modules);
    }

    public function upload_pdf(UploadValidation $request, $id) 
    {
        if ($request->hasFile('file_name')) {
            $filename = $request->file('file_name')->getClientOriginalName();
            $request->file_name->move(public_path('storage'), $filename);
            
            $pdf = Module::findOrFail($id);
            $pdf->file_name = $filename;
            $pdf->save();

            return $pdf;
        }
    }

    public function modules()
    {
        $module = new Module;
        $data = $module->getModules(session('employee_id'));

        return response()->json(['modules' => $data]);
    }
}
