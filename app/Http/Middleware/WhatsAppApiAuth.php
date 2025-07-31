<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class WhatsAppApiAuth
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
        $apiKey = $request->header('X-API-Key') ?? $request->input('api_key');
        $expectedApiKey = config('whatsapp.api_key');

        if (!$apiKey || $apiKey !== $expectedApiKey) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid API key.'
            ], Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}