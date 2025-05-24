<?php

namespace App\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class SetUserTimezone
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::check()) {
            $timezone = Auth::user()->timezone;

            if ($timezone) {
                config(['app.timezone' => $timezone]);
                date_default_timezone_set($timezone);

                Carbon::setLocale($timezone);
            }
        }

        return $next($request);
    }
}
