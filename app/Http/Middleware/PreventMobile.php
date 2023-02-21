<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Phattarachai\LaravelMobileDetect\Agent;

class PreventMobile
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
        $agent = new Agent();
        if($agent->isPhone() && !$request->user())
            return abort(400, "This link is only accessible with a desktop device.");
        return $next($request);
    }
}
