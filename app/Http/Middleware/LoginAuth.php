<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Log untuk debugging
        Log::info('LoginAuth: Checking session', [
            'session_id' => session()->getId(),
            'has_username' => session()->has('username'),
            'has_logged_in' => session()->has('logged_in'),
            'path' => $request->path(),
            'session_data' => session()->all(),
            'cookies' => $request->cookies->all(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        // Jika ini adalah rute login, biarkan lewat
        if ($request->routeIs('login') || $request->routeIs('customlogin')) {
            Log::info('LoginAuth: Allowing login route');
            return $next($request);
        }

        if (!session()->has('username') || !session()->has('logged_in') || session()->get('logged_in') !== true) {
            Log::warning('LoginAuth: Invalid session', [
                'has_username' => session()->has('username'),
                'has_logged_in' => session()->has('logged_in'),
                'logged_in_value' => session()->get('logged_in'),
                'session_id' => session()->getId()
            ]);
            
            // Jika ini adalah request AJAX, kembalikan response JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sesi login tidak valid atau telah berakhir',
                    'redirect' => route('login')
                ], 401);
            }
            
            // Redirect ke halaman login dengan pesan error
            return redirect()->route('login')
                ->with('error', 'Sesi login tidak valid atau telah berakhir. Silakan login kembali.');
        }

        Log::info('LoginAuth: Valid session, proceeding');
        return $next($request);
    }
}
