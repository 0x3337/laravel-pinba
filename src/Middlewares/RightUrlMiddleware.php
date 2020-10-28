<?php

namespace Chocofamilyme\LaravelPinba\Middlewares;

use Chocofamilyme\LaravelPinba\Facades\Pinba;
use Closure;
use Illuminate\Http\Request;

class RightUrlMiddleware
{
    private const UNKNOWN_SCRIPT_NAME = '<unknown>';

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $return = $next($request);

        $uri = self::UNKNOWN_SCRIPT_NAME;
        if ($route = $request->route()) {
            $uri = is_string($route) ? $route : $route->uri;
        }

        Pinba::setScriptName($uri);

        return $return;
    }
}
