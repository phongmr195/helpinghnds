<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class LogsApiResponse
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
        $response = $next($request);
        $arrTypeLogs = [
            500 => 'error',
            400 => 'warning',
            401 => 'info',
            403 => 'warning',
            404 => 'warning',
            201 => 'alert',
        ];
        if ($response->status() != 200) {
            $typeLog = $arrTypeLogs[$response->status()];
            Log::channel('api_logs')->$typeLog(json_encode([
                'STATUS_CODE' => $response->status(),
                'URI' => $request->getUri(),
                'METHOD' => $request->getMethod(),
                'REQUEST_BODY' => $request->all(),
                'RESPONSE' => $response->getContent()
            ]));
        }

        return $response;
    }
}
