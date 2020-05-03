<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
            $user_type = Auth::user()->user_type;
            switch ($user_type) {
                case 'trainor':
                        return  redirect()->route('trainor');    
                    break;

                case 'trainee':
                        return  redirect()->route('trainee'); 
                    break;

                default:
                    # code...
                    // return  redirect()->route('user');  
                    break;
            }
        }

        return $next($request);
    }
}
