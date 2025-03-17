<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\QueryException;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\ErrorHandler\Error\FatalError;

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
        // Logging lebih detail untuk semua exception
        $this->reportable(function (Throwable $e) {
            $message = 'Exception: ' . $e->getMessage();
            $context = [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ];
            
            if (method_exists($e, 'getStatusCode')) {
                $context['status_code'] = $e->getStatusCode();
            }
            
            Log::error($message, $context);
        });
        
        // Handle favicon.ico requests lebih baik
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('favicon.ico')) {
                if (file_exists(public_path('favicon.ico'))) {
                    return response()->file(public_path('favicon.ico'), [
                        'Content-Type' => 'image/x-icon'
                    ]);
                }
                return response()->noContent();
            }
        });
        
        // Penanganan khusus untuk DecryptException
        $this->reportable(function (DecryptException $e) {
            Log::error('Dekripsi gagal: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => request()->fullUrl()
            ]);
            return false; // Tangani sendiri, jangan kirim ke Flare/external reporting
        });
        
        $this->renderable(function (DecryptException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan pada keamanan data',
                    'message' => 'Silakan coba lagi atau hubungi administrator',
                    'detail' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            // Jika format URL menunjukkan request ke /ranap/pemeriksaan dengan parameter terenkripsi
            if (strpos($request->url(), 'ranap/pemeriksaan') !== false && 
                ($request->has('no_rawat') || $request->has('no_rm'))) {
                
                // Redirect ke halaman dengan parameter default jika terjadi error dekripsi
                return redirect()->route('ranap.pasien')
                    ->with('error', 'Terjadi kesalahan saat membaca data. Mohon coba lagi.');
            }
            
            // Default response untuk DecryptException non-JSON
            return response()->view('errors.500', [
                'exception' => $e,
                'error_type' => 'Kesalahan Dekripsi Data',
                'message' => 'Terjadi kesalahan pada pemrosesan data terenkripsi.'
            ], 500);
        });
        
        // Penanganan error Database
        $this->renderable(function (QueryException $e, $request) {
            Log::error('Database error: ' . $e->getMessage(), [
                'sql' => $e->getSql ?? 'Not available',
                'bindings' => $e->getBindings ?? []
            ]);
            
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Terjadi kesalahan pada koneksi database',
                    'message' => 'Silakan coba lagi dalam beberapa saat',
                    'detail' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }
            
            // Gunakan halaman error kustom jika file tersedia, jika tidak gunakan fallback
            if (view()->exists('errors.500')) {
                return response()->view('errors.500', [
                    'exception' => $e,
                    'error_type' => 'Kesalahan Database',
                    'message' => 'Terjadi kesalahan saat mengakses database.'
                ], 500);
            } else if (file_exists(public_path('500.php'))) {
                return response()->file(public_path('500.php'));
            }
            
            return response()->view('errors.500', [], 500);
        });
        
        // Penanganan TokenMismatchException (CSRF)
        $this->renderable(function (TokenMismatchException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Sesi telah kedaluwarsa',
                    'message' => 'Silakan refresh halaman dan coba lagi'
                ], 419);
            }
            
            return redirect()->back()->withInput($request->except('_token'))
                ->with('error', 'Sesi telah kedaluwarsa. Silakan coba lagi.');
        });
        
        // Penanganan umum untuk semua error non-produksi
        $this->renderable(function (Throwable $e, $request) {
            if (!config('app.debug')) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Server Error',
                        'status' => 'error'
                    ], 500);
                }
                
                // Coba gunakan view kustom, jika ada
                if (view()->exists('errors.500')) {
                    return response()->view('errors.500', [
                        'exception' => $e,
                        'error_type' => class_basename($e),
                        'message' => 'Terjadi kesalahan pada server.'
                    ], 500);
                }
                
                // Fallback ke file statis jika view tidak ditemukan
                if (file_exists(public_path('500.php'))) {
                    return response()->file(public_path('500.php'));
                }
                
                // Ultimate fallback
                return response()->view('errors.500', [], 500);
            }
        });
    }
}
