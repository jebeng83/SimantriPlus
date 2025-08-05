<?php

require_once 'vendor/autoload.php';

// Load environment variables
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
}

// PCare configuration
$pcareConfig = [
    'base_url' => $_ENV['BPJS_PCARE_BASE_URL'] ?? 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest',
    'cons_id' => $_ENV['BPJS_PCARE_CONS_ID'] ?? '7925',
    'secret_key' => $_ENV['BPJS_PCARE_SECRET_KEY'] ?? $_ENV['BPJS_PCARE_CONS_PWD'] ?? '',
    'user_key' => $_ENV['BPJS_PCARE_USER_KEY'] ?? '',
    'username' => $_ENV['BPJS_PCARE_USERNAME'] ?? $_ENV['BPJS_PCARE_USER'] ?? '',
    'password' => $_ENV['BPJS_PCARE_PASSWORD'] ?? $_ENV['BPJS_PCARE_PASS'] ?? '',
    'app_code' => $_ENV['BPJS_PCARE_APP_CODE'] ?? '095'
];

echo "=== TEST PCARE API SEDERHANA ===\n";
echo "Base URL: " . $pcareConfig['base_url'] . "\n";
echo "Cons ID: " . $pcareConfig['cons_id'] . "\n";
echo "Username: " . $pcareConfig['username'] . "\n\n";

// Function to generate timestamp
function generateTimestamp() {
    return time();
}

// Function to generate signature
function generateSignature($consId, $secretKey, $timestamp) {
    $data = $consId . '&' . $timestamp;
    return base64_encode(hash_hmac('sha256', $data, $secretKey, true));
}

// Function to generate authorization
function generateAuth($username, $password, $appCode) {
    $data = $username . ':' . $password . ':' . $appCode;
    return base64_encode($data);
}

// Function to encrypt data
function encryptData($data, $key) {
    $key = substr(hash('sha256', $key), 0, 32);
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Function to decrypt data
function decryptData($encryptedData, $key) {
    $key = substr(hash('sha256', $key), 0, 32);
    $data = base64_decode($encryptedData);
    $iv = substr($data, 0, 16);
    $encrypted = substr($data, 16);
    return openssl_decrypt($encrypted, 'AES-256-CBC', $key, 0, $iv);
}

// Function to make PCare request
function makePcareRequest($url, $method = 'GET', $data = null, $config) {
    $timestamp = generateTimestamp();
    $signature = generateSignature($config['cons_id'], $config['secret_key'], $timestamp);
    $authorization = generateAuth($config['username'], $config['password'], $config['app_code']);
    
    $headers = [
        'X-cons-id: ' . $config['cons_id'],
        'X-timestamp: ' . $timestamp,
        'X-signature: ' . $signature,
        'X-authorization: ' . $authorization,
        'user_key: ' . $config['user_key'],
        'Content-Type: application/json'
    ];
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POST, true);
        $encryptedData = encryptData($data, $config['cons_id'] . $config['secret_key'] . $timestamp);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $encryptedData);
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    return [
        'http_code' => $httpCode,
        'response' => $response,
        'error' => $error,
        'timestamp' => $timestamp
    ];
}

// Test 1: GET Provider
echo "=== TEST 1: GET PROVIDER ===\n";
$result = makePcareRequest($pcareConfig['base_url'] . '/provider', 'GET', null, $pcareConfig);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "Error: " . $result['error'] . "\n";
} else {
    echo "Response: " . substr($result['response'], 0, 200) . "...\n";
    
    if ($result['http_code'] == 200) {
        try {
            $decrypted = decryptData($result['response'], $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $result['timestamp']);
            echo "Decrypted: " . $decrypted . "\n";
        } catch (Exception $e) {
            echo "Decrypt error: " . $e->getMessage() . "\n";
        }
    }
}
echo "\n";

// Test 2: GET Poli
echo "=== TEST 2: GET POLI ===\n";
$result = makePcareRequest($pcareConfig['base_url'] . '/poli', 'GET', null, $pcareConfig);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "Error: " . $result['error'] . "\n";
} else {
    echo "Response: " . substr($result['response'], 0, 200) . "...\n";
    
    if ($result['http_code'] == 200) {
        try {
            $decrypted = decryptData($result['response'], $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $result['timestamp']);
            echo "Decrypted: " . $decrypted . "\n";
        } catch (Exception $e) {
            echo "Decrypt error: " . $e->getMessage() . "\n";
        }
    }
}
echo "\n";

// Test 3: GET Dokter
echo "=== TEST 3: GET DOKTER ===\n";
$result = makePcareRequest($pcareConfig['base_url'] . '/dokter', 'GET', null, $pcareConfig);
echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "Error: " . $result['error'] . "\n";
} else {
    echo "Response: " . substr($result['response'], 0, 200) . "...\n";
    
    if ($result['http_code'] == 200) {
        try {
            $decrypted = decryptData($result['response'], $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $result['timestamp']);
            echo "Decrypted: " . $decrypted . "\n";
        } catch (Exception $e) {
            echo "Decrypt error: " . $e->getMessage() . "\n";
        }
    }
}
echo "\n";

// Test 4: Simple Kunjungan Data
echo "=== TEST 4: POST KUNJUNGAN (SIMPLE) ===\n";
$simpleKunjunganData = [
    'noKartu' => '0001441909697',
    'tglDaftar' => date('d-m-Y'),
    'kdPoli' => '001',
    'keluhan' => 'Test kunjungan',
    'kunjSakit' => true,
    'sistole' => 120,
    'diastole' => 80,
    'beratBadan' => 70,
    'tinggiBadan' => 170,
    'respRate' => 20,
    'lingkarPerut' => 80,
    'heartRate' => 80,
    'rujukBalik' => 0,
    'kdTkp' => '10',
    'kdDokter' => '100001',
    'kdDiag1' => 'Z00.0'
];

echo "Data yang dikirim:\n";
echo json_encode($simpleKunjunganData, JSON_PRETTY_PRINT) . "\n\n";

$result = makePcareRequest(
    $pcareConfig['base_url'] . '/kunjungan', 
    'POST', 
    json_encode($simpleKunjunganData), 
    $pcareConfig
);

echo "HTTP Code: " . $result['http_code'] . "\n";
if ($result['error']) {
    echo "Error: " . $result['error'] . "\n";
} else {
    echo "Response: " . substr($result['response'], 0, 500) . "...\n";
    
    if ($result['http_code'] == 200 || $result['http_code'] == 201) {
        try {
            $decrypted = decryptData($result['response'], $pcareConfig['cons_id'] . $pcareConfig['secret_key'] . $result['timestamp']);
            echo "Decrypted: " . $decrypted . "\n";
        } catch (Exception $e) {
            echo "Decrypt error: " . $e->getMessage() . "\n";
        }
    }
}

echo "\n=== TEST SELESAI ===\n";