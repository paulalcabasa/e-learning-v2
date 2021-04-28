<?php

namespace App\Http\Middleware;

use App\UserAccess;
use Closure;

class CheckAdminSessionMiddleware
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
        $user_access = UserAccess::select('et.*')
            ->leftJoin('email_tab as et', 'et.employee_id', '=', 'user_access_tab.employee_id')
            ->where([
                'system_id'      => config('app.system_id'),
                'user_type_id'   => 2,
                'et.employee_id' => $request->session()->get('employee_id')
            ])
            ->exists();

        if (!$user_access) return redirect()->away('http://portal.isuzuphil.com/ipc_central/main_home.php');
        
        return $next($request);
    }
}