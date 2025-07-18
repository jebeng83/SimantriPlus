<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Load Laravel app
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

echo "=== Testing PCare Configuration ===\n";
echo "Base URL: " . env('BPJS_PCARE_BASE_URL') . "\n";
echo "Cons ID: " . env('BPJS_PCARE_CONS_ID') . "\n";
echo "User: " . env('BPJS_PCARE_USER') . "\n";
echo "Has Password: " . (env('BPJS_PCARE_PASS') ? 'Yes' : 'No') . "\n";
echo "Has User Key: " . (env('BPJS_PCARE_USER_KEY') ? 'Yes' : 'No') . "\n";
echo "Simulation Mode: " . (env('PCARE_SIMULATION_MODE') ? 'true' : 'false') . "\n";

echo "\n=== Testing PCare API with Laravel Request ===\n";

// Test dengan membuat request Laravel yang proper
$request = Illuminate\Http\Request::create('/pcare/api/ref/poli', 'GET');
$request->headers->set('Accept', 'application/json');
$request->headers->set('X-Requested-With', 'XMLHttpRequest');
$request->headers->set('X-API-Testing', 'true');

try {
    $response = $kernel->handle($request);
    
    echo "HTTP Status: " . $response->getStatusCode() . "\n";
    echo "Content Type: " . $response->headers->get('Content-Type') . "\n";
    echo "Response Length: " . strlen($response->getContent()) . " characters\n";
    echo "Response: " . $response->getContent() . "\n";
    
    // Parse JSON if possible
    $data = json_decode($response->getContent(), true);
    if ($data) {
        echo "\n=== Parsed Response ===\n";
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                echo "$key: [array with " . count($value) . " items]\n";
                if ($key === 'data' && count($value) > 0) {
                    echo "First data item: " . json_encode($value[0], JSON_PRETTY_PRINT) . "\n";
                }
            } else {
                echo "$key: $value\n";
            }
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Testing Direct Controller Call ===\n";

try {
    // Create controller instance
    $controller = new App\Http\Controllers\PCare\ReferensiPoliController();
    
    // Create request
    $directRequest = new Illuminate\Http\Request();
    $directRequest->headers->set('X-API-Testing', 'true');
    
    // Call method directly
    $directResponse = $controller->getPoli($directRequest);
    
    echo "Direct Call Status: " . $directResponse->getStatusCode() . "\n";
    echo "Direct Call Response: " . $directResponse->getContent() . "\n";
    
    // Parse the response to understand why it's returning "No data found"
    $responseData = json_decode($directResponse->getContent(), true);
    if ($responseData && !$responseData['success']) {
        echo "\n=== Debugging 'No data found' ===\n";
        echo "This means the controller's requestPcare('poli') call either:\n";
        echo "1. Returned null/false\n";
        echo "2. Returned array without 'response' key\n";
        echo "3. Returned array with empty 'response'\n";
        echo "\nLet's check the Laravel logs for more details...\n";
    }
    
} catch (Exception $e) {
    echo "Direct Call Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n=== Testing Cache Configuration ===\n";
try {
    $cacheDriver = config('cache.default');
    echo "Cache Driver: " . $cacheDriver . "\n";
    
    // Test cache functionality
    $cache = app('cache');
    $testKey = 'test_cache_key';
    $testValue = 'test_cache_value';
    
    $cache->put($testKey, $testValue, 60);
    $retrieved = $cache->get($testKey);
    
    echo "Cache Test: " . ($retrieved === $testValue ? 'PASSED' : 'FAILED') . "\n";
    
    $cache->forget($testKey);
    
} catch (Exception $e) {
    echo "Cache Error: " . $e->getMessage() . "\n";
}

$kernel->terminate($request, $response ?? null);