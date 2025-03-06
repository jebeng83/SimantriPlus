<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
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
            Log::error('Exception: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
        });
        
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('favicon.ico')) {
                return response()->file(public_path('favicon.ico'), [
                    'Content-Type' => 'image/x-icon'
                ]);
            }
        });
        
        $this->renderable(function (Throwable $e, $request) {
            if (!config('app.debug')) {
                if ($request->is('api/*')) {
                    return response()->json([
                        'message' => 'Server Error'
                    ], 500);
                }
                
                if ($request->is('favicon.ico')) {
                    return response()->file(public_path('favicon.ico'), [
                        'Content-Type' => 'image/x-icon'
                    ]);
                }
                
                return response()->view('errors.500', [], 500);
            }
        });
    }
}
