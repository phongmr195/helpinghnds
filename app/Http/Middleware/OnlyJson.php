<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyJson
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->wantsJson() && in_array($request->method(), ["POST", "PUT", "PATCH"]))
        {
            return response()->json([
                'code' => 403,
                'message' => "Forbidden, only accept data json",
            ]);
        }

        return $next($request);
    }
}
