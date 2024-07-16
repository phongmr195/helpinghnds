<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class OnlyAdminAccess
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
        if($request->user()->hasRole(['root', 'admin'])){
            return $next($request);
        }

        if($request->ajax()){
            return response()->json([
                "status" => "Forbidden",
                "code" => 403,
                "message" => "You don't have permission access to edit or update!"
            ], 403);
        }

        return abort(403, 'Forbidden');
    }
}
