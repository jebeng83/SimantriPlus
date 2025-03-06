<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;

class RefreshCsrfToken
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
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        // Jika request adalah AJAX atau Livewire
        if ($request->ajax() || $request->hasHeader('X-Livewire')) {
            // Periksa apakah token CSRF valid
            if (!$this->tokensMatch($request)) {
                // Regenerate token dan kirim sebagai header
                Session::regenerateToken();
                $response = $next($request);
                $response->header('X-CSRF-TOKEN', csrf_token());
                return $response;
            }
        }
        
        return $next($request);
    }
    
    /**
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');
        
        if (!$token && $header = $request->header('X-XSRF-TOKEN')) {
            try {
                $token = Crypt::decrypt($header, false);
            } catch (\Exception $e) {
                $token = '';
            }
        }
        
        return is_string($token) && is_string(Session::token()) &&
               hash_equals(Session::token(), $token);
    }
}
