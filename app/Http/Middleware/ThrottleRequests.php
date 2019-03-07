<?php

namespace App\Http\Middleware;

use Illuminate\Routing\Middleware\ThrottleRequests as Middleware;
use RuntimeException;

class ThrottleRequests extends Middleware
{
    /**
     * Resolve request signature.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    protected function resolveRequestSignature($request)
    {
        if ($route = $request->route()) {
            $key = array_merge($route->methods(), [
                $route->domain(),
                $route->uri(),
                $request->ip(),
            ]);

            return sha1(implode('|', array_filter($key)));
        }

        throw new RuntimeException(trans('Unable to generate the request signature. Route unavailable.'));
    }
}
