<?php

/**
 * Example usage of BPJS Signature Generation Methods
 * 
 * This file demonstrates how to use the BPJS signature generation methods
 * from the EnkripsiData trait for creating proper headers for BPJS web service calls.
 * 
 * Required Environment Variables:
 * - BPJS_PCARE_CONS_ID=7925
 * - BPJS_PCARE_CONS_PWD=2eF2C8E837
 * - BPJS_PCARE_USER_KEY=403bf17ddf158790afcfe1e8dd682a67
 * - BPJS_PCARE_USER=usernamePcare
 * - BPJS_PCARE_PASS=passwordPcare
 * - BPJS_PCARE_APP_CODE=095
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

class BpjsSignatureHandler
{
    use EnkripsiData;
    
    /**
     * Example 1: Generate individual signature components
     */
    public function generateIndividualComponents()
    {
        echo "\n=== BPJS Signature Components Example ===\n";
        
        try {
            // 1. Generate UTC timestamp
            $timestamp = $this->createBpjsTimestamp();
            echo "X-timestamp: {$timestamp}\n";
            
            // 2. Generate signature
            $consId = '7925';
            $consSecret = '2eF2C8E837';
            $signature = $this->createBpjsSignature($consId, $timestamp, $consSecret);
            echo "X-signature: {$signature}\n";
            
            // 3. Generate authorization
            $username = 'usernamePcare';
            $password = 'passwordPcare';
            $appCode = '095';
            $authorization = $this->createBpjsAuthorization($username, $password, $appCode);
            echo "X-authorization: {$authorization}\n";
            
            echo "\nIndividual components generated successfully!\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Example 2: Generate complete headers using environment variables
     */
    public function generateCompleteHeaders()
    {
        echo "\n=== Complete BPJS Headers Example ===\n";
        
        try {
            // Generate complete headers for PCare service
            $headers = $this->createBpjsHeaders('pcare');
            
            echo "Complete headers for BPJS PCare:\n";
            foreach ($headers as $key => $value) {
                echo "{$key}: {$value}\n";
            }
            
            echo "\nComplete headers generated successfully!\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Example 3: Validate signature generation against BPJS specification
     */
    public function validateSignatureGeneration()
    {
        echo "\n=== Signature Validation Example ===\n";
        
        try {
            // Test data from BPJS documentation
            $testConsId = 'aaa';
            $testSecret = 'bbb';
            $testTimestamp = '433223232';
            
            // Generate signature
            $signature = $this->createBpjsSignature($testConsId, $testTimestamp, $testSecret);
            
            echo "Test Parameters:\n";
            echo "Consumer ID: {$testConsId}\n";
            echo "Secret Key: {$testSecret}\n";
            echo "Timestamp: {$testTimestamp}\n";
            echo "Message: {$testConsId}&{$testTimestamp}\n";
            echo "Generated Signature: {$signature}\n";
            
            // Expected result from BPJS documentation: 20BKS3PWnD3XU4JbSSZvVlGi2WWnDa8Sv9uHJ+wsELA=
            $expectedSignature = '20BKS3PWnD3XU4JbSSZvVlGi2WWnDa8Sv9uHJ+wsELA=';
            
            if ($signature === $expectedSignature) {
                echo "✓ Signature validation PASSED! Matches BPJS specification.\n";
            } else {
                echo "✗ Signature validation FAILED!\n";
                echo "Expected: {$expectedSignature}\n";
                echo "Generated: {$signature}\n";
            }
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Example 4: Generate headers for different BPJS services
     */
    public function generateMultiServiceHeaders()
    {
        echo "\n=== Multi-Service Headers Example ===\n";
        
        $services = ['pcare', 'vclaim', 'icare'];
        
        foreach ($services as $service) {
            try {
                echo "\n--- {$service} Service Headers ---\n";
                $headers = $this->createBpjsHeaders($service);
                
                foreach ($headers as $key => $value) {
                    echo "{$key}: {$value}\n";
                }
                
            } catch (Exception $e) {
                echo "Error for {$service}: " . $e->getMessage() . "\n";
            }
        }
    }
    
    /**
     * Example 5: Manual signature generation following PHP example from documentation
     */
    public function manualSignatureExample()
    {
        echo "\n=== Manual Signature Generation (PHP Documentation Example) ===\n";
        
        try {
            $data = "testtesttest";
            $secretKey = "secretkey";
            
            // Computes the timestamp
            date_default_timezone_set('UTC');
            $tStamp = strval(time() - strtotime('1970-01-01 00:00:00'));
            
            // Computes the signature by hashing the salt with the secret key as the key
            $signature = hash_hmac('sha256', $data . "&" . $tStamp, $secretKey, true);
            
            // base64 encode
            $encodedSignature = base64_encode($signature);
            
            echo "X-cons-id: {$data}\n";
            echo "X-timestamp: {$tStamp}\n";
            echo "X-signature: {$encodedSignature}\n";
            
            echo "\nManual signature generated successfully!\n";
            
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Run all examples
     */
    public function runAllExamples()
    {
        echo "BPJS Signature Generation Examples\n";
        echo "=================================\n";
        
        $this->generateIndividualComponents();
        $this->generateCompleteHeaders();
        $this->validateSignatureGeneration();
        $this->generateMultiServiceHeaders();
        $this->manualSignatureExample();
        
        echo "\n=== All Examples Completed ===\n";
    }
}

// Simple logging function for standalone usage
if (!function_exists('logMessage')) {
    function logMessage($level, $message, $context = []) {
        echo "[{$level}] {$message}\n";
        if (!empty($context)) {
            echo "Context: " . json_encode($context, JSON_PRETTY_PRINT) . "\n";
        }
    }
}

// Mock Log class methods if not available
if (!class_exists('Log')) {
    class MockLog {
        public static function info($message, $context = []) {
            logMessage('INFO', $message, $context);
        }
        
        public static function error($message, $context = []) {
            logMessage('ERROR', $message, $context);
        }
    }
    
    // Create alias for Log class
    class_alias('MockLog', 'Log');
}

// Helper function to simulate env() if not available
if (!function_exists('env')) {
    function env($key, $default = null) {
        $envVars = [
            'BPJS_PCARE_CONS_ID' => '7925',
            'BPJS_PCARE_CONS_PWD' => '2eF2C8E837',
            'BPJS_PCARE_USER_KEY' => '403bf17ddf158790afcfe1e8dd682a67',
            'BPJS_PCARE_USER' => 'usernamePcare',
            'BPJS_PCARE_PASS' => 'passwordPcare',
            'BPJS_PCARE_APP_CODE' => '095'
        ];
        
        return $envVars[$key] ?? $default;
    }
}

// Run examples if this file is executed directly
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    $handler = new BpjsSignatureHandler();
    $handler->runAllExamples();
}

?>

<!-- 
Usage in Laravel Application:

1. Use the trait in your controller or service class:
   use App\Traits\EnkripsiData;
   
2. Generate complete headers:
   $headers = $this->createBpjsHeaders('pcare');
   
3. Use headers in HTTP request:
   $response = Http::withHeaders($headers)
       ->post('https://apijkn.bpjs-kesehatan.go.id/wsihs/api/pcare/validate', $data);

4. For individual components:
   $timestamp = $this->createBpjsTimestamp();
   $signature = $this->createBpjsSignature($consId, $timestamp, $consSecret);
   $authorization = $this->createBpjsAuthorization($username, $password, $appCode);

Required Environment Variables in .env:
BPJS_PCARE_CONS_ID=7925
BPJS_PCARE_CONS_PWD=2eF2C8E837
BPJS_PCARE_USER_KEY=403bf17ddf158790afcfe1e8dd682a67
BPJS_PCARE_USER=usernamePcare
BPJS_PCARE_PASS=passwordPcare
BPJS_PCARE_APP_CODE=095
-->