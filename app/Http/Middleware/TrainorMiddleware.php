<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class TrainorMiddleware
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
        if (Auth::user()->user_type != 'trainor') {
            return redirect()->route('page_blocked');
        }

        return $next($request);
    }
}
