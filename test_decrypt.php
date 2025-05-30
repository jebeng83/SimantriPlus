<?php

require_once __DIR__ . '/vendor/autoload.php';

use LZCompressor\LZString;

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
    return LZString::decompressFromEncodedURIComponent($string);
}

// Data dari response BPJS
$timestamp = "1748616567"; // Timestamp dari response sebelumnya
$cons_id = '7925';
$secret_key = '2eF2C8E837';
$encrypted_response = "4I3IR8tuNYWx6IypQ/ZBm7WyZp0+b2bZ398H+RfXbAl40/yNmT4oOFFz0rqpszHwGrWytuer83Ggfv8Qx5BVZLDPOWyAWaUc7dhnvkkQQ9uO3rIbx20irrCppt+AnJ3d2FMA5lJ5JMjmJDRWhpIsOxdBfJfc+bh+Z3zZ8P7s5zaEqnblK+IELyLXMH9H5nRhbCmtXC3eJy+X3827/XFuuXLhIjbi1ttAniA1d9Pj+fssgdlsziUTraMV55yOKUW9";

// Proses dekripsi
$key = $cons_id . $secret_key . $timestamp;
echo "Key: " . $key . "\n";

try {
    $decrypted = stringDecrypt($key, $encrypted_response);
    echo "Decrypted: " . $decrypted . "\n";
    
    $decompressed = decompress($decrypted);
    echo "Decompressed: " . $decompressed . "\n";
    
    $json = json_decode($decompressed, true);
    echo "JSON: " . print_r($json, true) . "\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
} 