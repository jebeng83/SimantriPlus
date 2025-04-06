<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot(): void
    {
        // Tambahkan error handling yang lebih baik
        try {
            $this->configureRateLimiting();

            $this->routes(function () {
                Route::middleware('api')
                    ->prefix('api')
                    ->group(base_path('routes/api.php'));

                Route::middleware('web')
                    ->group(base_path('routes/web.php'));
                
                // ANC Routes
                Route::middleware('web')
                    ->group(base_path('routes/anc.php'));
            });

            // Tambahkan logging untuk route fallback
            Route::fallback(function () {
                Log::error('Route not found', [
                    'url' => request()->url(),
                    'method' => request()->method(),
                    'user_agent' => request()->userAgent(),
                    'ip' => request()->ip()
                ]);
                
                if (request()->expectsJson()) {
                    return response()->json([
                        'message' => 'Route tidak ditemukan'
                    ], 404);
                }
                
                return redirect()->route('home')->with('error', 'Halaman yang anda cari tidak ditemukan');
            });
        } catch (\Exception $e) {
            // Log error
            if (app()->bound('log')) {
                app('log')->error("RouteServiceProvider Error: " . $e->getMessage(), [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            
            // Re-throw in production, display in debug
            if (!config('app.debug')) {
                throw $e;
            }
            
            // Dalam debug mode, tampilkan error dengan jelas
            echo "<h1>Route Service Provider Error</h1>";
            echo "<p><strong>Message:</strong> " . $e->getMessage() . "</p>";
            echo "<p><strong>File:</strong> " . $e->getFile() . " (Line: " . $e->getLine() . ")</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
            exit;
        }
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
