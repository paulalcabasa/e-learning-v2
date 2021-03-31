<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
     
        $emailExists = DB::table('users')->where(['email' => $credentials['email']])->exists();
       
        if ($emailExists) {
            $currentlyActive = DB::table('users')->where([
                'email'     => $credentials['email'],
                'is_active' => 1,
                'user_type' => 'trainee'
            ])->exists();

            if ($currentlyActive) 
                return redirect()
                    ->route('login')
                    ->with([
                        'status' => 'You are currently have session on other device, please logout them first or contact IPC\'s Administrator'
                    ]);

            $isApproved = DB::table('users')->where([
                'email'       => $credentials['email'],
                'is_approved' => 1
            ])->exists();

            if (!$isApproved) 
                return redirect()
                    ->route('login')
                    ->with([
                        'status' => 'Please wait for your account\'s approval or you may contact IPC\'s Administrator. Thank you!'
                    ]);
        }

        if (Auth::attempt(
            [
                'email'       => $credentials['email'], 
                'password'    => $credentials['password'], 
                'is_approved' => 1,
            ])) {
           
            // Authentication passed...
            $setUserActive = DB::table('users')->where('app_user_id', Auth::user()->app_user_id)->update(['is_active' => 1]);
            if ($setUserActive) 
                return $this->redirectUser();
        }
        else {
            return back()
                ->withInput()
                ->with([
                    'status' => 'Incorrect User Credentials'
                ]);
        }
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    public function redirectUser()
    {
        Log::channel('users')->info('Showing user profile for user: ', [
            'app_user_id' => Auth::user()->app_user_id,
            'name'        => Auth::user()->name,
            'email'       => Auth::user()->email,
        ]);

        $user_type = Auth::user()->user_type;
        if ($user_type == 'trainor') 
            return redirect()->route('trainor');
        else if ($user_type == 'trainee') 
            return redirect()->route('trainee'); 
    }
}
