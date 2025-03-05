<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\App;

class DiagnosticController extends Controller
{
    /**
     * Menampilkan informasi diagnostik tentang aplikasi
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Informasi tentang route
        $routes = collect(Route::getRoutes())->map(function ($route) {
            return [
                'uri' => $route->uri(),
                'methods' => $route->methods(),
                'name' => $route->getName(),
                'action' => $route->getActionName(),
                'middleware' => $route->middleware(),
            ];
        })->filter(function ($route) {
            return str_contains($route['uri'], 'kyc');
        })->values();
        
        // Informasi tentang session
        $sessionInfo = [
            'session_id' => Session::getId(),
            'has_username' => Session::has('username'),
            'has_password' => Session::has('password'),
            'all_keys' => array_keys(Session::all()),
        ];
        
        // Informasi tentang middleware
        $middlewareGroups = app('router')->getMiddlewareGroups();
        $routeMiddleware = app('router')->getRouteMiddleware();
        
        // Informasi tentang aplikasi
        $appInfo = [
            'environment' => App::environment(),
            'debug' => config('app.debug'),
            'url' => config('app.url'),
            'locale' => App::getLocale(),
        ];
        
        // Log informasi untuk debugging
        Log::info('Diagnostic: Application information', [
            'session' => $sessionInfo,
            'app' => $appInfo,
        ]);
        
        // Kembalikan respons
        return response()->json([
            'routes' => $routes,
            'session' => $sessionInfo,
            'middleware_groups' => $middlewareGroups,
            'route_middleware' => $routeMiddleware,
            'app' => $appInfo,
        ]);
    }
} 