<?php

// Memuat autoloader
require __DIR__.'/../vendor/autoload.php';

// Memuat aplikasi Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Mendapatkan kernel HTTP
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Membuat request
$request = Illuminate\Http\Request::capture();

// Menjalankan aplikasi untuk memproses request
$response = $kernel->handle($request);

// Menampilkan informasi tentang aplikasi Laravel
echo "<h1>Test KYC Route</h1>";
echo "<p>Laravel Version: " . Illuminate\Foundation\Application::VERSION . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Menampilkan informasi tentang rute
echo "<h2>Registered Routes:</h2>";
echo "<pre>";
$router = $app->make('router');
$routes = $router->getRoutes();
if ($routes->count() > 0) {
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'kyc') !== false) {
            echo implode('|', $route->methods()) . " " . $route->uri() . " => " . $route->getActionName() . "\n";
        }
    }
} else {
    echo "No routes registered.\n";
}
echo "</pre>";

// Menampilkan informasi tentang KYCController
echo "<h2>KYC Controller:</h2>";
echo "<pre>";
try {
    $reflection = new ReflectionClass('App\Http\Controllers\KYCController');
    echo "Class exists: Yes\n";
    echo "Methods:\n";
    foreach ($reflection->getMethods() as $method) {
        echo "  - " . $method->getName() . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang middleware
echo "<h2>Middleware:</h2>";
echo "<pre>";
try {
    $reflection = new ReflectionClass($kernel);
    if ($reflection->hasProperty('routeMiddleware')) {
        $property = $reflection->getProperty('routeMiddleware');
        $property->setAccessible(true);
        $routeMiddleware = $property->getValue($kernel);
        
        if (isset($routeMiddleware['loginauth'])) {
            echo "loginauth middleware: " . $routeMiddleware['loginauth'] . "\n";
        } else {
            echo "loginauth middleware not found\n";
        }
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang view
echo "<h2>KYC View:</h2>";
echo "<pre>";
try {
    $viewPath = __DIR__ . '/../resources/views/kyc/index.blade.php';
    if (file_exists($viewPath)) {
        echo "View exists: Yes\n";
        echo "View path: " . $viewPath . "\n";
    } else {
        echo "View does not exist\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
echo "</pre>";

// Menampilkan informasi tentang environment
echo "<h2>Environment:</h2>";
echo "<pre>";
echo "APP_ENV: " . env('APP_ENV', 'Not set') . "\n";
echo "APP_DEBUG: " . (env('APP_DEBUG', false) ? 'true' : 'false') . "\n";
echo "APP_URL: " . env('APP_URL', 'Not set') . "\n";
echo "</pre>";

// Terminate the application
$kernel->terminate($request, $response);
?> 