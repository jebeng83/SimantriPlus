<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Create Laravel application instance
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing MobileJKN API Endpoints ===\n\n";

// Test routes
$routes = [
    [
        'name' => 'Poli Reference (no date)',
        'url' => '/api/pcare/ref/poli',
        'method' => 'GET'
    ],
    [
        'name' => 'Poli Reference (with date)',
        'url' => '/api/pcare/ref/poli/tanggal/13-07-2025',
        'method' => 'GET'
    ],
    [
        'name' => 'Doctor Reference (no date)',
        'url' => '/api/pcare/ref/dokter',
        'method' => 'GET'
    ],
    [
        'name' => 'Doctor Reference (with date)',
        'url' => '/api/pcare/ref/dokter/tanggal/13-07-2025',
        'method' => 'GET'
    ],
    [
        'name' => 'Doctor Reference (with poli code)',
        'url' => '/api/pcare/ref/dokter/kodepoli/001/tanggal/13-07-2025',
        'method' => 'GET'
    ]
];

foreach ($routes as $route) {
    echo "Testing: {$route['name']}\n";
    echo "URL: {$route['url']}\n";
    
    try {
        $request = Illuminate\Http\Request::create($route['url'], $route['method']);
        $response = $kernel->handle($request);
        
        $status = $response->getStatusCode();
        $content = $response->getContent();
        
        echo "Status: {$status}\n";
        
        // Parse JSON response
        $jsonData = json_decode($content, true);
        if ($jsonData) {
            if (isset($jsonData['success']) && $jsonData['success']) {
                echo "Result: SUCCESS\n";
                echo "Data count: " . (isset($jsonData['data']) ? count($jsonData['data']) : 0) . "\n";
                echo "Source: " . ($jsonData['source'] ?? 'unknown') . "\n";
            } elseif (isset($jsonData['metaData'])) {
                echo "Result: API Response\n";
                echo "Code: " . ($jsonData['metaData']['code'] ?? 'unknown') . "\n";
                echo "Message: " . ($jsonData['metaData']['message'] ?? 'unknown') . "\n";
            } else {
                echo "Result: UNKNOWN FORMAT\n";
                echo "Content: " . substr($content, 0, 200) . "...\n";
            }
        } else {
            echo "Result: INVALID JSON\n";
            echo "Content: " . substr($content, 0, 200) . "...\n";
        }
        
    } catch (Exception $e) {
        echo "Status: ERROR\n";
        echo "Error: " . $e->getMessage() . "\n";
    }
    
    echo "\n" . str_repeat('-', 50) . "\n\n";
}

echo "=== Test Completed ===\n";