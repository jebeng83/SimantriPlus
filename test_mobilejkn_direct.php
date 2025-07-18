<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// BPJS MobileJKN Configuration
$baseUrl = $_ENV['BPJS_MOBILEJKN_BASE_URL'];
$consId = $_ENV['BPJS_MOBILEJKN_CONS_ID'];
$secretKey = $_ENV['BPJS_MOBILEJKN_CONS_PWD'];
$userKey = $_ENV['BPJS_MOBILEJKN_USER_KEY'];
$username = $_ENV['BPJS_MOBILEJKN_USER'];
$password = $_ENV['BPJS_MOBILEJKN_PASS'];

echo "=== BPJS MobileJKN Direct Test ===\n";
echo "Base URL: {$baseUrl}\n";
echo "Cons ID: {$consId}\n";
echo "Username: {$username}\n\n";

// Generate timestamp
date_default_timezone_set('UTC');
$timestamp = strval(time());
echo "Timestamp: {$timestamp}\n";
echo "UTC Time: " . gmdate('Y-m-d H:i:s', time()) . "\n\n";

// Generate X-Authorization
$kdAplikasi = "095";
$authString = $username . ':' . $password . ':' . $kdAplikasi;
$encodedAuth = base64_encode($authString);
echo "Auth String: {$authString}\n";
echo "Encoded Auth: {$encodedAuth}\n\n";

// Generate signature
$message = $consId . '&' . $timestamp;
$signature = hash_hmac('sha256', $message, $secretKey, true);
$encodedSignature = base64_encode($signature);
echo "Message: {$message}\n";
echo "Signature: {$encodedSignature}\n\n";

// Test endpoint - try different date formats
$tanggal = '2025-07-13'; // Try YYYY-MM-DD format
$endpoint = "ref/poli/tanggal/{$tanggal}";
echo "Testing with date format: {$tanggal}\n";
$url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');
echo "Full URL: {$url}\n\n";

// Headers
$headers = [
    'Content-Type: application/json',
    'Accept: application/json',
    'X-cons-id: ' . $consId,
    'X-timestamp: ' . $timestamp,
    'X-signature: ' . $encodedSignature,
    'X-authorization: ' . $encodedAuth,
    'user_key: ' . $userKey
];

echo "Headers:\n";
foreach ($headers as $header) {
    echo "  {$header}\n";
}
echo "\n";

// Make request using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_VERBOSE, true);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "=== RESPONSE ===\n";
echo "HTTP Code: {$httpCode}\n";
if ($error) {
    echo "cURL Error: {$error}\n";
}
echo "Response Body:\n";
echo $response . "\n\n";

// Try to decode JSON
if ($response) {
    $jsonResponse = json_decode($response, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        echo "=== PARSED JSON ===\n";
        print_r($jsonResponse);
    } else {
        echo "JSON Parse Error: " . json_last_error_msg() . "\n";
    }
}