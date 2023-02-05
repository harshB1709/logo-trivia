<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\AppSetting as AppSettingModel;

class AppSetting
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $setting_key)
    {
        $app_setting = AppSettingModel::where([
            'key' => $setting_key
        ])->first();

        $app_setting = $app_setting?->value ?? true;

        $user = $request->user();

        if($app_setting || $user)
            return $next($request);

        return abort(503);
    }
}
