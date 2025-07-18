<?php

// Memuat autoloader
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Mendapatkan kernel HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Membuat request
$request = Illuminate\Http\Request::capture();

// Menampilkan informasi tentang aplikasi Laravel
echo "<h1>Test Laravel</h1>";
echo "<p>Laravel Version: " . Illuminate\Foundation\Application::VERSION . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Menampilkan informasi tentang rute
echo "<h2>Registered Routes:</h2>";
echo "<pre>";
$router = $app->make('router');
$routes = $router->getRoutes();
if ($routes->count() > 0) {
    foreach ($routes as $route) {
        echo implode('|', $route->methods()) . " " . $route->uri() . " => " . $route->getActionName() . "\n";
    }
} else {
    echo "No routes registered.\n";
}
echo "</pre>";

// Menampilkan informasi tentang middleware
echo "<h2>Route Middleware:</h2>";
echo "<pre>";
$routeMiddleware = [];
try {
    // Menggunakan refleksi untuk mengakses properti protected
    $reflection = new ReflectionClass($kernel);
    if ($reflection->hasProperty('routeMiddleware')) {
        $property = $reflection->getProperty('routeMiddleware');
        $property->setAccessible(true);
        $routeMiddleware = $property->getValue($kernel);
    }
    
    foreach ($routeMiddleware as $name => $class) {
        echo $name . " => " . $class . "\n";
    }
} catch (Exception $e) {
    echo "Error accessing middleware: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang middleware grup
echo "<h2>Middleware Groups:</h2>";
echo "<pre>";
$middlewareGroups = [];
try {
    // Menggunakan refleksi untuk mengakses properti protected
    $reflection = new ReflectionClass($kernel);
    if ($reflection->hasProperty('middlewareGroups')) {
        $property = $reflection->getProperty('middlewareGroups');
        $property->setAccessible(true);
        $middlewareGroups = $property->getValue($kernel);
    }
    
    foreach ($middlewareGroups as $name => $middleware) {
        echo $name . ":\n";
        foreach ($middleware as $m) {
            echo "  - " . $m . "\n";
        }
    }
} catch (Exception $e) {
    echo "Error accessing middleware groups: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang service provider
echo "<h2>Service Providers:</h2>";
echo "<pre>";
$providers = $app->getLoadedProviders();
foreach ($providers as $provider => $loaded) {
    echo $provider . " => " . ($loaded ? "Loaded" : "Not Loaded") . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang environment
echo "<h2>Environment:</h2>";
echo "<pre>";
echo "APP_ENV: " . env('APP_ENV', 'Not set') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG', false) ? 'true' : 'false') . "\n";
echo "APP_URL: " . env('APP_URL', 'Not set') . "\n";
echo "</pre>";

// Menampilkan informasi tentang database
echo "<h2>Database Configuration:</h2>";
echo "<pre>";
echo "DB_CONNECTION: " . env('DB_CONNECTION', 'Not set') . "\n";
echo "DB_HOST: " . env('DB_HOST', 'Not set') . "\n";
echo "DB_PORT: " . env('DB_PORT', 'Not set') . "\n";
echo "DB_DATABASE: " . env('DB_DATABASE', 'Not set') . "\n";
echo "DB_USERNAME: " . env('DB_USERNAME', 'Not set') . "\n";
echo "</pre>";
?> 