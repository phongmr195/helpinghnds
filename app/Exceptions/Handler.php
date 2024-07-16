<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Auth\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $exception)
    
    {
        if ($exception instanceof ModelNotFoundException) {
            // return response()->json(['error' => 'Entry for '.str_replace('App\\', '', $exception->getModel()).' not found'], 404);
            abort(404);
        }

        if($this->isHttpException($exception))
        {
            switch (intval($exception->getStatusCode())) {
                // Page not found
                case 404:
                    return redirect()->route('admin.pages.404');
                    break;
                // Server error
                // case 500:
                //     return redirect()->route('admin.pages.500');
                default:
                    return $this->renderHttpException($exception);
                    break;
            }
        }

        return parent::render($request, $exception);
    }

    /**
     * Custom unauthenticated response
     * 
     * @return object
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if ($request->expectsJson()) {
            $json = [
                'code' => 401,
                'message' => $exception->getMessage(),
                'data' => null
            ];
            return response()->json($json, 401);
        }

        return redirect(route('login'));
    }
}
