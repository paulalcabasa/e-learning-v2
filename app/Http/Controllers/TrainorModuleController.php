<?php

namespace App\Http\Controllers;

use Auth;
use App\Trainor;
use App\Models\Module;
use App\UserAccess;
use App\Services\LearningStatusService;
use App\Services\UpdateStatus;
use App\Services\EmailService;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class TrainorModuleController extends Controller
{
    protected $user_id;
    protected $dealer;

    public function __construct(UpdateStatus $status)
    {
        $this->middleware(function ($request, $next) {
			$this->user_id = str_replace_last(Auth::user()->user_type . '_', '', Auth::user()->app_user_id);
			
			$this->dealer = DB::table('dealers as d')
				->select('d.*')
				->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'd.dealer_id')
				->where([
					'trs.trainor_id' => $this->user_id
				])
				->first();

            return $next($request);
        });
        
        $status->update_module_detail_status();
    }
    
    public function trainor_modules($trainor_id)
    {
        $trainor = Trainor::findOrFail($trainor_id);
        $dealer_id = $trainor->dealer->dealer_id;

        $modules = Module::with([
            'module_details' => function($query) use($dealer_id) {
                $query->where('dealer_id', $dealer_id);
            }
        ])->get();

        return response()->json(['modules' => $modules->toArray()]);
    }

    public function done_reading_pdf($module_detail_id, EmailService $batch_email, LearningStatusService $status)
    {
        $query = DB::table('module_details')
            ->where('module_detail_id', $module_detail_id)
            ->update([
                'is_finished' => 1
            ]);

        if ($query) {
            $status->module_status($module_detail_id); //--> This will update module_schedules table
            
            $user_access = UserAccess::select('et.email')
                ->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
                ->where([
                    'system_id'    => config('app.system_id'),
                    'user_type_id' => 2
                ])
                ->get();

            foreach ($user_access as $value) {
                $batch_email->batch_incoming_emails([
                    'email_category' => 'finish',
                    'subject'        => 'Finished Module/PDF Viewing',
                    'sender'         => config('mail.from.address'),
                    'recipient'      => $value['email'],
                    'title'          => 'Module/PDF <span style="color: #5caad2;">Finished!</span>',
                    'message'        => $this->dealer->dealer_name . ' of <strong>'.$this->dealer->branch.'</strong> has been initiated the examination. <br>
                    Please click the button to navigate directly to our system.',
                    'cc'             => null,
                    'attachment'     => null
                ]);
            }
        }

        return response()->json($query);
    }
}
