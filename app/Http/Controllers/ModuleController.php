<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\ModuleValidation;
use App\Http\Requests\UploadValidation;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sections = DB::select('SELECT id, section_name FROM sections');

        $data = [
            'sections' => $sections
        ];
        return view('contents.modules.modules',$data);
    }

    public function get() 
    {
        $data = DB::select('
            SELECT m.*,
                    st.section_name,
            (SELECT COUNT(sm.module_id) FROM sub_modules sm WHERE sm.module_id = m.module_id) as count_total
            FROM modules m LEFT JOIN sections st
            ON st.id = m.section_id
        ');

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
        $data->section_id = $request->section_id;
        
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
        return view('contents.modules.module', $module);
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
        return response()->json(['modules' => Module::all()]);
    }
}
