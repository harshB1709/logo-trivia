<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class GameInProgress
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
        if($request->session()->missing('words')) {
            return $request->isMethod('post') ? abort(400) : redirect()->route('home');
        }

        return $next($request);
    }
}
