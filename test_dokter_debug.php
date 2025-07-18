<?php

require_once __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Test route: /api/pcare/ref/dokter
echo "Testing route: /api/pcare/ref/dokter\n";
$request = Illuminate\Http\Request::create('/api/pcare/ref/dokter?tanggal=2025-07-13', 'GET');
$response = $kernel->handle($request);
echo "Status: " . $response->getStatusCode() . "\n";
echo "Content: " . $response->getContent() . "\n\n";

// Test route: /api/pcare/ref/dokter/tanggal/{tanggal}
echo "Testing route: /api/pcare/ref/dokter/tanggal/13-07-2025\n";
$request2 = Illuminate\Http\Request::create('/api/pcare/ref/dokter/tanggal/13-07-2025', 'GET');
$response2 = $kernel->handle($request2);
echo "Status: " . $response2->getStatusCode() . "\n";
echo "Content: " . $response2->getContent() . "\n";

$kernel->terminate($request, $response);
$kernel->terminate($request2, $response2);