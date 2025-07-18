<?php

require_once 'vendor/autoload.php';

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Create a request to test the PCare API
$request = Illuminate\Http\Request::create('/pcare/api/ref/poli', 'GET');
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');

// Add session data to simulate logged in user
$request->setLaravelSession(new Illuminate\Session\Store(
    'laravel_session',
    new Illuminate\Session\ArraySessionHandler([
        'username' => '102',
        'logged_in' => true,
        'login_time' => date('Y-m-d H:i:s')
    ])
));

try {
    $response = $kernel->handle($request);
    
    echo "Status Code: " . $response->getStatusCode() . "\n";
    echo "Headers: " . json_encode($response->headers->all()) . "\n";
    echo "Content: " . $response->getContent() . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

$kernel->terminate($request, $response ?? null);