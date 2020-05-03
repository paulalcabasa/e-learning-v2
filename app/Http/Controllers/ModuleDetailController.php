<?php

namespace App\Http\Controllers;

use Auth;
use App\UserAccess;
use App\Dealer;
use App\ModuleSchedule;
use App\ModuleDetail;
use App\Http\Requests\ModuleDetailValidation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Services\HistoryService;
use App\Services\UpdateStatus;
use App\Services\EmailService;

class ModuleDetailController extends Controller
{
    protected $user_id;

    public function __construct(UpdateStatus $status)
    {
        $this->middleware(function ($request, $next) {
            $this->user_id = str_replace_last(Auth::user()['user_type'] . '_', '', Auth::user()['app_user_id']);

            return $next($request);
        });

        $status->update_module_detail_status();
    }

    public function index()
    {
        $module_details = DB::table('module_details as md')
            ->select(
                'md.*', 
                'm.module',
                'dr.dealer_name', 
                'dr.branch'
            )
            ->leftJoin('modules as m', 'm.module_id', '=', 'md.module_id')
            ->leftJoin('dealers as dr', 'dr.dealer_id', '=', 'md.dealer_id')
            ->orderBy('md.created_at', 'ASC')
            ->get();

        // return response()->json(['module_details' => $module_details]);
    }

    public function dealers_schedule($module_schedule_id)
    {
        $dealers_schedule = DB::table('dealers as dr')
            ->select(
                'dr.dealer_id', 
                'dr.dealer_name', 
                'dr.branch', 
                'md.module_detail_id', 
                'md.module_schedule_id', 
                'md.start_date', 
                'md.end_date', 
                'md.is_opened', 
                'md.status', 
                'md.is_enabled', 
                'md.is_finished', 
                'm.module_id', 
                'm.module' 
            )
            ->leftJoin('module_details as md', 'md.dealer_id', '=', 'dr.dealer_id')
            ->leftJoin('module_schedules as ms', 'ms.module_schedule_id', '=', 'md.module_schedule_id')
            ->leftJoin('modules as m', 'm.module_id', '=', 'ms.module_id')
            ->where('md.module_schedule_id', $module_schedule_id)
            ->orderBy('md.created_at', 'DESC')
            ->get();

        return response()->json(['dealers_schedule' => $dealers_schedule]);
    }

    public function show($module_detail_id)
    {
        $module_detail = DB::table('module_details as md')
            ->select(
                'md.*', 
                'm.module',
                'dr.dealer_name', 
                'dr.branch'
            )
            ->leftJoin('modules as m', 'm.module_id', '=', 'md.module_id')
            ->leftJoin('dealers as dr', 'dr.dealer_id', '=', 'md.dealer_id')
            ->where('module_detail_id', $module_detail_id)
            ->first();

        return response()->json($module_detail);
    }

    /** ModuleDetailValidation */
    public function store(Request $request, EmailService $batch_email)
    {
        if (!$request->module_schedule) { // Update
            foreach ($request->dealer_schedules as $value) {
                DB::table('module_details')
                    ->where('module_detail_id', $value['module_detail_id'])
                    ->update([
                        'start_date'         => $value['start_date'],
                        'end_date'           => $value['end_date'],
                        'is_enabled'         => $value['is_enabled']
                    ]);
            }
            return;
        }

        try {
            DB::beginTransaction();

            $module_schedule = new ModuleSchedule;
            $module_schedule->module_id = $request->module_schedule['module_id'];
            $module_schedule->created_by = $request->module_schedule['created_by'];
            $module_schedule->save();

            if ($module_schedule->module_schedule_id) {
                $dealer_ids = [];
                foreach ($request->dealer_schedules as $value) {
                    $dealer_ids[] = $value['dealer_id'];

                    DB::table('module_details')->insert([
                        'module_schedule_id' => $module_schedule->module_schedule_id,
                        'dealer_id'          => $value['dealer_id'],
                        'start_date'         => $value['start_date'],
                        'end_date'           => $value['end_date']
                    ]);
                }
            }

            $emails = DB::table('trainors')->select('email')->whereIn('dealer_id', $dealer_ids)->get();
            foreach ($emails as $value) {
                $batch_email->batch_incoming_emails([
                    'email_category' => 'creation',
                    'subject'        => 'New Module Schedule',
                    'sender'         => config('mail.from.address'),
                    'recipient'      => $value->email, // should be trainor
                    'title'          => 'You have new <span style="color: #5caad2;">Module Schedule!</span>',
                    'message'        => 'Hi Mam/Sir, <strong>IPC Administration</strong> has been created a new schedule for a module viewing. <br>
                    Please click the button to navigate directly to your system.',
                    'cc'             => null,
                    'attachment'     => null
                ]);
            }

            DB::commit();
        }
        catch(Exception $ex) {
            DB::rollBack();

            return response()->json([
                'error' => 'Request to create failed, please repeat again or ask for a help.'
            ]);
        }
    }

    public function update(ModuleDetailValidation $request, $module_detail_id)
    {
        $module_detail = ModuleDetail::findOrFail();
        $module_detail->dealer_id = $value['dealer_id'];
        $module_detail->start_date = $value['start_date'];
        $module_detail->end_date = $value['end_date'];
        $module_detail->is_enabled = $value['is_enabled'];
        $module_detail->save();

        return response()->json($module_detail);
    }

    public function destroy($module_detail_id)
    {
        $module_detail = ModuleDetail::findOrFail($module_detail_id);
        $module_detail->delete();

        return response()->json($module_detail);
    }

    // -------------------------------------------------------------------
    public function trigger_module($module_detail_id, $user_id, EmailService $batch_email, HistoryService $history)
    {
        $trainor_id = $user_id;

        try {
            $check_already_open = ModuleDetail::where([
                    'module_detail_id' => $module_detail_id,
                    'trainor_id'       => $trainor_id,
                    'is_opened'        => 1
                ])
                ->exists();

            if (!$check_already_open) {
                $query = ModuleDetail::find($module_detail_id)
                    ->update([
                        'is_opened'  => 1,
                        'trainor_id' => $trainor_id
                    ]);

                if ($query) {
                    // SAVE HISTORY
                    $history->save_trainor_history([
                        'trainor_id'       => $trainor_id,
                        'module_detail_id' => $module_detail_id
                    ]);

                    $dealer = Dealer::select('dealers.*')
                        ->leftJoin('trainors as trs', 'trs.dealer_id', '=', 'dealers.dealer_id')
                        ->where([
                            'trs.trainor_id' => $this->user_id
                        ])
                        ->first();
        
                    $user_access = UserAccess::select('et.email')
                        ->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
                        ->where([
                            'system_id'    => config('app.system_id'),
                            'user_type_id' => 2
                        ])
                        ->get();
        
                    foreach ($user_access as $value) {
                        $batch_email->batch_incoming_emails([
                            'email_category' => 'opened',
                            'subject'        => 'Initialized Module',
                            'sender'         => config('mail.from.address'),
                            'recipient'      => $value['email'],
                            'title'          => 'Module <span style="color: #5caad2;">Initiated!</span>',
                            'message'        => $dealer->dealer_name . ' of <strong>'.$dealer->branch.'</strong> has been initiated the module viewing. <br>
                            Please click the button to navigate directly to our system.',
                            'cc'             => null,
                            'attachment'     => null
                        ]);
                    }
                }
            }
            return response()->json(200);
        } catch (Exception $e) {
            report($e);
        }
    }

    public function disabling_module($module_detail_id)
    {
        ModuleDetail::findOrFail($module_detail_id)->update(['is_enabled' => 0]);
    }
}
