<?php

/**
 * Debug script untuk menguji koneksi PCare
 * Membandingkan konfigurasi dengan server yang berfungsi
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

echo "=== PCare Connection Debug ===\n\n";

// 1. Cek konfigurasi environment
echo "1. Environment Configuration:\n";
$config = [
    'BPJS_PCARE_BASE_URL' => $_ENV['BPJS_PCARE_BASE_URL'] ?? 'NOT SET',
    'BPJS_PCARE_CONS_ID' => $_ENV['BPJS_PCARE_CONS_ID'] ?? 'NOT SET',
    'BPJS_PCARE_USER' => $_ENV['BPJS_PCARE_USER'] ?? 'NOT SET',
    'BPJS_PCARE_USER_KEY' => substr($_ENV['BPJS_PCARE_USER_KEY'] ?? 'NOT SET', 0, 10) . '...',
    'BPJS_PCARE_APP_CODE' => $_ENV['BPJS_PCARE_APP_CODE'] ?? '095',
];

foreach ($config as $key => $value) {
    echo "   {$key}: {$value}\n";
}

// 2. Test basic connectivity
echo "\n2. Testing Basic Connectivity:\n";
$baseUrl = $_ENV['BPJS_PCARE_BASE_URL'] ?? 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest';
echo "   Base URL: {$baseUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $baseUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'PCare-Debug/1.0');

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

// 3. Generate PCare headers
echo "\n3. Generating PCare Headers:\n";

date_default_timezone_set('UTC');
$timestamp = strval(time());
$consId = $_ENV['BPJS_PCARE_CONS_ID'] ?? '';
$secretKey = $_ENV['BPJS_PCARE_CONS_PWD'] ?? '';
$userKey = $_ENV['BPJS_PCARE_USER_KEY'] ?? '';

// Generate signature
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
echo "   Cons ID: {$consId}\n";
echo "   Signature: " . substr($signature, 0, 20) . "...\n";
echo "   Authorization: " . substr($authorization, 0, 20) . "...\n";
echo "   User Key: " . substr($userKey, 0, 10) . "...\n";

// 4. Test simple endpoint
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
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ERROR: {$error}\n";
} else {
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response length: " . strlen($response) . " bytes\n";
    
    if ($httpCode == 200) {
        echo "   SUCCESS: Provider endpoint accessible\n";
        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['metaData'])) {
            echo "   MetaData Code: " . ($decoded['metaData']['code'] ?? 'N/A') . "\n";
            echo "   MetaData Message: " . ($decoded['metaData']['message'] ?? 'N/A') . "\n";
        }
    } else {
        echo "   FAILED: HTTP {$httpCode}\n";
        if (stripos($response, 'html') !== false) {
            echo "   Response is HTML (likely error page)\n";
            // Extract title if possible
            if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
                echo "   HTML Title: {$matches[1]}\n";
            }
        } else {
            echo "   Response: " . substr($response, 0, 200) . "...\n";
        }
    }
}

// 5. Test dokter endpoint (yang bermasalah)
echo "\n5. Testing Dokter Endpoint:\n";
$dokterUrl = rtrim($baseUrl, '/') . '/dokter/0/10';
echo "   URL: {$dokterUrl}\n";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $dokterUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "   ERROR: {$error}\n";
} else {
    echo "   HTTP Code: {$httpCode}\n";
    echo "   Response length: " . strlen($response) . " bytes\n";
    
    if ($httpCode == 200) {
        echo "   SUCCESS: Dokter endpoint accessible\n";
        $decoded = json_decode($response, true);
        if ($decoded && isset($decoded['metaData'])) {
            echo "   MetaData Code: " . ($decoded['metaData']['code'] ?? 'N/A') . "\n";
            echo "   MetaData Message: " . ($decoded['metaData']['message'] ?? 'N/A') . "\n";
            if (isset($decoded['response']) && is_array($decoded['response'])) {
                echo "   Data Count: " . count($decoded['response']) . "\n";
            }
        }
    } else {
        echo "   FAILED: HTTP {$httpCode}\n";
        if (stripos($response, 'html') !== false) {
            echo "   Response is HTML (likely error page)\n";
            if (preg_match('/<title>(.*?)<\/title>/i', $response, $matches)) {
                echo "   HTML Title: {$matches[1]}\n";
            }
        } else {
            echo "   Response: " . substr($response, 0, 200) . "...\n";
        }
    }
}

echo "\n=== Debug Complete ===\n";
echo "\nRekomendasi:\n";
echo "1. Jika provider endpoint berhasil tapi dokter gagal, masalahnya di endpoint spesifik\n";
echo "2. Jika semua endpoint gagal dengan HTML response, masalahnya di kredensial\n";
echo "3. Bandingkan hasil ini dengan server kerjo.faskesku.com yang berfungsi\n";
echo "4. Periksa apakah ada perbedaan dalam format password atau kredensial\n";