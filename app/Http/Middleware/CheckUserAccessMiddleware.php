<?php

namespace App\Http\Middleware;

use App\UserAccess;
use Closure;

class CheckUserAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $query = UserAccess::where([
            ['employee_id', '=', session('employee_id')],
            ['system_id', '=', config('constants.SYSTEM_ID')],
            ['user_type_id', '=', config('constants.ADMIN_ID')],
        ])->exists();

        if (!$query) return redirect()->route('administration_guard');

        return $next($request);
    }
}
