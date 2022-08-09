<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RequestDecryptMiddleware
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
        if($request->has('no_rawat') || $request->has('no_rm')) {
            $request->merge(['encrypt_param' => decrypt_function($request->get('encrypt_param'))]);
          }
        return $next($request);    
    }
}
