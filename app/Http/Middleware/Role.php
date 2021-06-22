<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Role
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if(\Auth::user()->hasAnyRole($roles)) {
            return $next($request);
        }
        
        abort(403);

        return redirect(RouteServiceProvider::HOME);
    }
}
