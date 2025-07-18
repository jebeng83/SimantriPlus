<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test with today's date
$today = date('Y-m-d');
echo "Testing with today's date: $today\n";

// Create a request with tanggal parameter
$request = Illuminate\Http\Request::create("/api/pcare/ref/poli?tanggal=$today", 'GET');

// Process the request
$response = $kernel->handle($request);

echo "Status Code: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n";

$kernel->terminate($request, $response);