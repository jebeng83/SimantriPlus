<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Closure;
use Illuminate\Support\Facades\Session;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        '/livewire/*',
        '/refresh-csrf',
        '/customlogin',
        '/ilp/whatsapp/node/*',
        '/test-*',
    ];
    
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Jika request adalah Livewire, lewati verifikasi CSRF
        if ($request->hasHeader('X-Livewire')) {
            return $next($request);
        }
        
        return parent::handle($request, $next);
    }
}
