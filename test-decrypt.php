<?php
require_once 'vendor/autoload.php';

// Data dari response BPJS
$encryptedResponse = "2rv3/cR4+xt2UE0oj8Y5Fl9mg817HatnMJyhdH+jTM58wLB7j4p6o0VVmCKrfhGRyZfv8Om69IRSi80ypKNyVzBlDQOoa1D6VbOcvkb2vCEEe4h7STz8pNkOJlTdTbkHWAykQeixg4f9HsafRXx2wkE6gf12hFabyn8k/PfIIcPizv/YbOLTtPX7nZPBu11fFNpkyPZNRlX/PvXjMuR0PiwQxyG/74nlzNYhWL2Hc3crX+J+CcBDKpMNQK+kXK51";

// Konfigurasi
$consId = "7925";
$secretKey = "2eF2C8E837";
$timestamp = "1748615681"; // Timestamp dari request sebelumnya

// Generate decrypt key
$key = $consId . $secretKey . $timestamp;

function stringDecrypt($key, $string)
{
    $encrypt_method = 'AES-256-CBC';
    $key_hash = hex2bin(hash('sha256', $key));
    $iv = substr(hex2bin(hash('sha256', $key)), 0, 16);

    $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, OPENSSL_RAW_DATA, $iv);
    return $output;
}

function decompress($string)
{
    return \LZCompressor\LZString::decompressFromEncodedURIComponent($string);
}

try {
    // Step 1: Decrypt
    echo "Mencoba mendekripsi data...\n";
    $decrypted = stringDecrypt($key, $encryptedResponse);
    echo "Data terdekripsi: " . substr($decrypted, 0, 100) . "...\n\n";

    // Step 2: Decompress
    echo "Mencoba mendekompresi data...\n";
    $decompressed = decompress($decrypted);
    echo "Data terdekompresi: " . $decompressed . "\n\n";

    // Step 3: Parse JSON
    $data = json_decode($decompressed, true);
    echo "Data JSON:\n";
    print_r($data);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 