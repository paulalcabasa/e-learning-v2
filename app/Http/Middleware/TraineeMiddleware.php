<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class TraineeMiddleware
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
        if (Auth::user()->user_type != 'trainee') {
            return redirect()->route('page_blocked');
        }

        return $next($request);
    }
}
