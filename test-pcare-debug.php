<?php

require_once 'vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Laravel properly
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== PCare Debug Test ===\n";
echo "Base URL: " . env('BPJS_PCARE_BASE_URL') . "\n";
echo "Cons ID: " . env('BPJS_PCARE_CONS_ID') . "\n";
echo "User: " . env('BPJS_PCARE_USER') . "\n";

// Create a custom trait implementation for debugging
class PcareDebugger {
    use App\Traits\PcareTrait {
        requestPcare as originalRequestPcare;
    }
    
    public function debugRequestPcare($endpoint) {
        echo "\n=== Debug requestPcare('$endpoint') ===\n";
        
        try {
            // Check cache first
            $cacheKey = 'pcare_' . md5($endpoint . json_encode(null));
            echo "Cache Key: $cacheKey\n";
            
            if (\Illuminate\Support\Facades\Cache::has($cacheKey)) {
                echo "Cache HIT - returning cached data\n";
                $cachedData = \Illuminate\Support\Facades\Cache::get($cacheKey);
                echo "Cached data type: " . gettype($cachedData) . "\n";
                return $cachedData;
            } else {
                echo "Cache MISS - will call API\n";
            }
            
            // Get configuration
            $baseUrl = env('BPJS_PCARE_BASE_URL');
            $consId = env('BPJS_PCARE_CONS_ID');
            $userKey = env('BPJS_PCARE_USER_KEY');
            
            echo "Config check:\n";
            echo "- Base URL: " . ($baseUrl ? 'OK' : 'MISSING') . "\n";
            echo "- Cons ID: " . ($consId ? 'OK' : 'MISSING') . "\n";
            echo "- User Key: " . ($userKey ? 'OK' : 'MISSING') . "\n";
            
            if (!$baseUrl || !$consId || !$userKey) {
                echo "ERROR: Missing required configuration\n";
                return [
                    'metaData' => [
                        'code' => 500,
                        'message' => 'Missing PCare configuration'
                    ],
                    'response' => null
                ];
            }
            
            // Generate auth components
            $timestamp = $this->getTimestamp();
            echo "Timestamp: $timestamp\n";
            
            $signature = $this->generateSignature($timestamp);
            echo "Signature: " . substr($signature, 0, 20) . "...\n";
            
            $authorization = $this->generateAuthorization();
            echo "Authorization: " . substr($authorization, 0, 20) . "...\n";
            
            // Build URL
            $baseUrl = rtrim($baseUrl, '/');
            $fullUrl = $baseUrl . '/' . $endpoint . '?offset=0&limit=10';
            echo "Full URL: $fullUrl\n";
            
            // Build headers
            $headers = [
                'X-cons-id' => $consId,
                'X-timestamp' => $timestamp,
                'X-signature' => $signature,
                'X-authorization' => 'Basic ' . $authorization,
                'user_key' => $userKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];
            
            echo "Headers prepared\n";
            
            // Make HTTP request
            echo "Making HTTP request...\n";
            
            $httpClient = \Illuminate\Support\Facades\Http::timeout(30)->withHeaders($headers);
            $response = $httpClient->get($fullUrl);
            
            echo "HTTP Status: " . $response->status() . "\n";
            echo "Response Size: " . strlen($response->body()) . " bytes\n";
            
            if ($response->status() >= 400) {
                echo "Error Response Body: " . $response->body() . "\n";
            }
            
            // Decode response
            $responseData = $response->json() ?? [];
            echo "Response JSON decoded: " . (empty($responseData) ? 'EMPTY' : 'OK') . "\n";
            
            if (!empty($responseData)) {
                echo "Response keys: " . implode(', ', array_keys($responseData)) . "\n";
                
                if (isset($responseData['metaData'])) {
                    echo "Metadata code: " . ($responseData['metaData']['code'] ?? 'N/A') . "\n";
                    echo "Metadata message: " . ($responseData['metaData']['message'] ?? 'N/A') . "\n";
                }
                
                if (isset($responseData['response'])) {
                    echo "Response data type: " . gettype($responseData['response']) . "\n";
                    if (is_array($responseData['response'])) {
                        echo "Response data count: " . count($responseData['response']) . "\n";
                    } else if (is_string($responseData['response'])) {
                        echo "Response data (encrypted): " . substr($responseData['response'], 0, 50) . "...\n";
                        
                        // Try to decrypt
                        echo "Attempting to decrypt...\n";
                        $decrypted = $this->decrypt($responseData['response'], $timestamp);
                        echo "Decryption result type: " . gettype($decrypted) . "\n";
                        
                        if (is_array($decrypted)) {
                            echo "Decrypted data count: " . count($decrypted) . "\n";
                            $responseData['response'] = $decrypted;
                        }
                    }
                }
            }
            
            return $responseData;
            
        } catch (\Exception $e) {
            echo "EXCEPTION: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            
            return [
                'metaData' => [
                    'code' => 500,
                    'message' => $e->getMessage()
                ],
                'response' => null
            ];
        }
    }
}

// Test the debug version
try {
    $debugger = new PcareDebugger();
    
    // Test dengan endpoint yang benar berdasarkan WsBPJSController
    $tanggal = date('Y-m-d'); // Hari ini
    echo "\n=== Testing with correct endpoint format ===\n";
    echo "Testing endpoint: ref/poli/tanggal/$tanggal\n";
    $result = $debugger->debugRequestPcare("ref/poli/tanggal/$tanggal");
    
    if ($result && isset($result['response']) && !empty($result['response'])) {
        echo "\n=== SUCCESS! Testing simple 'poli' endpoint ===\n";
        $result2 = $debugger->debugRequestPcare('poli');
    } else {
        echo "\n=== Trying alternative: provider endpoint ===\n";
        $result2 = $debugger->debugRequestPcare('provider');
    }
    
    echo "\n=== Final Result ===\n";
    echo "Result type: " . gettype($result) . "\n";
    
    if (is_array($result)) {
        echo "Result keys: " . implode(', ', array_keys($result)) . "\n";
        
        // Check what controller logic would do
        if ($result && isset($result['response'])) {
            echo "Controller would return: SUCCESS\n";
            echo "Data count: " . (is_array($result['response']) ? count($result['response']) : 'not array') . "\n";
        } else {
            echo "Controller would return: 'No data found'\n";
            echo "Reason: " . (!$result ? 'result is null/false' : 'no response key') . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Test Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}