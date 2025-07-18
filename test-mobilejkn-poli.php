<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Traits\BpjsTraits;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TestMobileJknPoli
{
    use BpjsTraits;
    
    public function testPoliEndpoint()
    {
        echo "=== Testing Mobile JKN Poli Reference API ===\n\n";
        
        try {
            // Test the endpoint that should work
            $tanggal = date('d-m-Y');
            $endpoint = "ref/poli/tanggal/{$tanggal}";
            
            echo "Testing endpoint: {$endpoint}\n";
            echo "Date parameter: {$tanggal}\n\n";
            
            $response = $this->requestGetBpjs($endpoint, 'mobilejkn');
            
            echo "=== Response Analysis ===\n";
            echo "Response type: " . gettype($response) . "\n";
            
            if (is_array($response)) {
                echo "Response keys: " . implode(', ', array_keys($response)) . "\n";
                
                if (isset($response['metaData'])) {
                    echo "MetaData Code: " . ($response['metaData']['code'] ?? 'N/A') . "\n";
                    echo "MetaData Message: " . ($response['metaData']['message'] ?? 'N/A') . "\n";
                }
                
                if (isset($response['response'])) {
                    echo "Response data type: " . gettype($response['response']) . "\n";
                    if (is_array($response['response'])) {
                        echo "Response data count: " . count($response['response']) . "\n";
                        if (count($response['response']) > 0) {
                            echo "First item keys: " . implode(', ', array_keys($response['response'][0])) . "\n";
                            echo "Sample data: " . json_encode($response['response'][0], JSON_PRETTY_PRINT) . "\n";
                        }
                    }
                } else {
                    echo "No 'response' key found\n";
                }
            } else {
                echo "Response is not an array\n";
                echo "Response content: " . print_r($response, true) . "\n";
            }
            
            // Test what the controller would return
            echo "\n=== Controller Logic Test ===\n";
            if ($response && isset($response['response']) && !empty($response['response'])) {
                echo "Controller would return: SUCCESS with data\n";
                echo "Data would be cached and returned as JSON\n";
            } else {
                echo "Controller would return: 'No data found' (404)\n";
                echo "Reason: response is empty or invalid\n";
            }
            
        } catch (Exception $e) {
            echo "=== ERROR ===\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . "\n";
            echo "Line: " . $e->getLine() . "\n";
        }
    }
}

$tester = new TestMobileJknPoli();
$tester->testPoliEndpoint();

echo "\n=== Test Complete ===\n";