<?php

namespace Chocofamilyme\LaravelPinba\Middlewares;

use Chocofamilyme\LaravelPinba\Facades\Pinba;
use Closure;

class RightUrlMiddleware
{
    const UNKNOWN_SCRIPT_NAME = '<unknown>';
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $return = $next($request);
        Pinba::setScriptName($request->route()->uri);
        return $return;
    }
}