<?php

namespace App\Http\Controllers\Auth;

use Session;
use App\UserAccess;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthSessionController extends Controller
{
    protected $host;

    public function __construct()
    { 
        $this->host = $_SERVER['HTTP_HOST'];
    }

    public function login($employee_id, $employee_no, $full_name, $section)
    {
        $credentials = [
            'employee_id' => base64_decode(urldecode($employee_id)),
            'employee_no' => base64_decode(urldecode($employee_no)),
            'full_name'   => base64_decode(urldecode($full_name)),
            'section'     => base64_decode(urldecode($section))
        ];

        return $this->check_access($credentials);
    }

    public function check_access($credentials)
    {
        $user_access = UserAccess::select('et.*')
            ->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
            ->where([
                'system_id'      => config('app.system_id'),
                'user_type_id'   => 2,
                'et.employee_id' => $credentials["employee_id"]
            ])
            ->exists();

        if (!$user_access) return redirect()->away('http://'.$this->host.'/ipc_central/main_home.php');

        session($credentials);

        if (Session()->has(
            [
                'section', 
                'full_name', 
                'employee_no', 
                'employee_id'
            ]
        )) {
            Session()->regenerate();
            return redirect()->route('admin');
        }
        else {
            return redirect()->away('http://'.$this->host.'/ipc_central/main_home.php');
        }
    }
}