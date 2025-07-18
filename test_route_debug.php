<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route /api/pcare/ref/poli
echo "Testing route: /api/pcare/ref/poli\n";
$request = Illuminate\Http\Request::create('/api/pcare/ref/poli', 'GET');
$request->headers->set('Accept', 'application/json');

try {
    $response = $kernel->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . $response->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

// Test route /api/pcare/ref/poli/tanggal/13-07-2025
echo "\n\nTesting route: /api/pcare/ref/poli/tanggal/13-07-2025\n";
$request2 = Illuminate\Http\Request::create('/api/pcare/ref/poli/tanggal/13-07-2025', 'GET');
$request2->headers->set('Accept', 'application/json');

try {
    $response2 = $kernel->handle($request2);
    echo "Status: " . $response2->getStatusCode() . "\n";
    echo "Content: " . $response2->getContent() . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

$kernel->terminate($request, $response);