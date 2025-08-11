<?php

/**
 * Script untuk debugging PCare API
 * Jalankan dengan: php test-pcare-debug.php
 */

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists('.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "=== PCare API Debug Test ===\n";
echo "Timestamp: " . date('Y-m-d H:i:s') . "\n\n";

// Check environment variables
echo "1. Checking Environment Variables:\n";
$requiredVars = [
    'BPJS_PCARE_BASE_URL',
    'BPJS_PCARE_CONS_ID',
    'BPJS_PCARE_CONS_PWD',
    'BPJS_PCARE_USER_KEY',
    'BPJS_PCARE_USER',
    'BPJS_PCARE_PASS',
    'BPJS_PCARE_APP_CODE'
];

foreach ($requiredVars as $var) {
    $value = $_ENV[$var] ?? 'NOT SET';
    $masked = in_array($var, ['BPJS_PCARE_CONS_PWD', 'BPJS_PCARE_PASS']) 
        ? str_repeat('*', strlen($value)) 
        : $value;
    echo "   {$var}: {$masked}\n";
}

echo "\n2. Testing Basic Connectivity:\n";
$baseUrl = $_ENV['BPJS_PCARE_BASE_URL'] ?? '';
if (empty($baseUrl)) {
    echo "   ERROR: Base URL not configured\n";
    exit(1);
}

// Test basic connectivity
echo "   Testing connectivity to: {$baseUrl}\n";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ERROR: {$error}\n";
} else {
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response length: " . strlen($response) . " bytes\n";
    if (stripos($response, 'html') !== false) {
        echo "   WARNING: Response appears to be HTML\n";
    }
}

echo "\n3. Testing PCare Authentication:\n";

// Generate timestamp and signature
date_default_timezone_set('UTC');
$timestamp = strval(time());
$consId = $_ENV['BPJS_PCARE_CONS_ID'] ?? '';
$secretKey = $_ENV['BPJS_PCARE_CONS_PWD'] ?? '';
$userKey = $_ENV['BPJS_PCARE_USER_KEY'] ?? '';

$data = $consId . "&" . $timestamp;
$signature = base64_encode(hash_hmac('sha256', $data, $secretKey, true));

// Generate authorization
$username = $_ENV['BPJS_PCARE_USER'] ?? '';
$password = $_ENV['BPJS_PCARE_PASS'] ?? '';
$appCode = $_ENV['BPJS_PCARE_APP_CODE'] ?? '095';

// Gunakan password sesuai dengan yang dikonfigurasi di environment
// PCare tidak selalu memerlukan '#' di akhir password

$authData = $username . ":" . $password . ":" . $appCode;
$authorization = base64_encode($authData);

echo "   Timestamp: {$timestamp}\n";
echo "   Signature: " . substr($signature, 0, 20) . "...\n";
echo "   Authorization: " . substr($authorization, 0, 20) . "...\n";

// Test provider endpoint (simple endpoint)
echo "\n4. Testing Provider Endpoint:\n";
$testUrl = rtrim($baseUrl, '/') . '/provider';
echo "   URL: {$testUrl}\n";

$headers = [
    'X-cons-id: ' . $consId,
    'X-timestamp: ' . $timestamp,
    'X-signature: ' . $signature,
    'X-authorization: Basic ' . $authorization,
    'user_key: ' . $userKey,
    'Content-Type: application/json',
    'Accept: application/json'
];

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $testUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ERROR: {$error}\n";
} else {
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response: " . substr($response, 0, 200) . "...\n";
    
    if ($httpCode == 200) {
        echo "   SUCCESS: Provider endpoint working\n";
    } else {
        echo "   ERROR: HTTP {$httpCode}\n";
        if (stripos($response, 'html') !== false) {
            echo "   WARNING: HTML response detected\n";
        }
    }
}

// Test dokter endpoint
echo "\n5. Testing Dokter Endpoint:\n";
$dokterUrl = rtrim($baseUrl, '/') . '/dokter/0/10';
echo "   URL: {$dokterUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dokterUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ERROR: {$error}\n";
} else {
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response: " . substr($response, 0, 200) . "...\n";
    
    if ($httpCode == 200) {
        echo "   SUCCESS: Dokter endpoint working\n";
    } else {
        echo "   ERROR: HTTP {$httpCode}\n";
        if (stripos($response, 'html') !== false) {
            echo "   WARNING: HTML response detected\n";
        }
        if (stripos($response, 'Request Error') !== false) {
            echo "   WARNING: BPJS Request Error detected\n";
        }
    }
}

echo "\n=== Debug Test Complete ===\n";