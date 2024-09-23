<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        
        if (Auth()->user()->id!=1) {
            return $next($request);
        }
        else{
            return back();
        }
    }
}
