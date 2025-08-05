<?php

/**
 * BPJS PCare Decryption Usage Example
 * 
 * This file demonstrates how to use the official BPJS PCare decryption methods
 * that have been implemented in the EnkripsiData trait.
 * 
 * The implementation follows the official BPJS documentation:
 * - Compression: LZ-string
 * - Encryption: AES 256 (mode CBC) - SHA256
 * - Key: consid + conspwd + timestamp request (concatenate string)
 * 
 * Decryption Process:
 * 1. Decrypt: AES 256 (mode CBC) - SHA256
 * 2. Decompress: LZ-string (decompressFromEncodedURIComponent)
 */

require_once 'vendor/autoload.php';

use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

// Example class that uses the EnkripsiData trait
class BpjsPcareHandler {
    use EnkripsiData;
    
    /**
     * Example: Decrypt BPJS PCare response
     * 
     * @param string $encryptedResponse The encrypted response from BPJS PCare API
     * @param string $timestamp The timestamp used in the original request
     * @return array|false Decoded JSON response or false on failure
     */
    public function decryptPcareResponse($encryptedResponse, $timestamp) {
        try {
            // Get BPJS PCare credentials from environment
            $consid = env('BPJS_PCARE_CONS_ID');
            $conspwd = env('BPJS_PCARE_CONS_PWD');
            
            if (empty($consid) || empty($conspwd)) {
                throw new \Exception('BPJS PCare credentials not configured');
            }
            
            // Decrypt and decompress the response
            $decryptedData = $this->bpjsDecryptResponse(
                $encryptedResponse, 
                $consid, 
                $conspwd, 
                $timestamp
            );
            
            if ($decryptedData === false) {
                return false;
            }
            
            // Parse JSON response
            $jsonData = json_decode($decryptedData, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('BPJS PCare JSON decode error: ' . json_last_error_msg());
                return false;
            }
            
            return $jsonData;
            
        } catch (\Exception $e) {
            \Log::error('BPJS PCare decryption error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Example: Decrypt individual encrypted data using BPJS method
     * 
     * @param string $encryptedData Base64 encoded encrypted data
     * @param string $timestamp The timestamp used for key generation
     * @return string|false Decrypted data or false on failure
     */
    public function decryptBpjsData($encryptedData, $timestamp) {
        try {
            // Create decryption key
            $key = $this->createBpjsDecryptionKey($timestamp, 'pcare');
            
            // Decrypt using BPJS method
            return $this->bpjsStringDecrypt($key, $encryptedData);
            
        } catch (\Exception $e) {
            \Log::error('BPJS data decryption error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Example: Decompress LZ-string compressed data
     * 
     * @param string $compressedData LZ-string compressed data
     * @return string|false Decompressed data or false on failure
     */
    public function decompressBpjsData($compressedData) {
        return $this->bpjsDecompress($compressedData);
    }
}

/*
 * Usage Examples:
 * 
 * 1. Decrypt complete BPJS PCare response:
 * 
 * $handler = new BpjsPcareHandler();
 * $timestamp = '1640995200'; // Example timestamp
 * $encryptedResponse = 'base64_encoded_encrypted_response_here';
 * 
 * $result = $handler->decryptPcareResponse($encryptedResponse, $timestamp);
 * if ($result !== false) {
 *     // Process the decrypted JSON data
 *     echo json_encode($result, JSON_PRETTY_PRINT);
 * }
 * 
 * 2. Decrypt individual data:
 * 
 * $encryptedData = 'base64_encoded_data_here';
 * $decrypted = $handler->decryptBpjsData($encryptedData, $timestamp);
 * 
 * 3. Decompress LZ-string data:
 * 
 * $compressedData = 'lz_string_compressed_data_here';
 * $decompressed = $handler->decompressBpjsData($compressedData);
 * 
 * 4. Using the trait methods directly:
 * 
 * $handler = new BpjsPcareHandler();
 * 
 * // Complete decryption process
 * $result = $handler->bpjsDecryptResponse(
 *     $encryptedResponse, 
 *     $consid, 
 *     $conspwd, 
 *     $timestamp
 * );
 * 
 * // Individual steps
 * $key = $handler->createBpjsDecryptionKey($timestamp, 'pcare');
 * $decrypted = $handler->bpjsStringDecrypt($key, $encryptedData);
 * $decompressed = $handler->bpjsDecompress($compressedData);
 */

// Environment variables required (already configured in .env):
// BPJS_PCARE_CONS_ID=7925
// BPJS_PCARE_CONS_PWD=2eF2C8E837
// BPJS_PCARE_USER_KEY=403bf17ddf158790afcfe1e8dd682a67
// BPJS_PCARE_USER=11251616
// BPJS_PCARE_PASS=Pcare154#
// BPJS_PCARE_KODE_PPK=11251616
// BPJS_PCARE_APP_CODE=095

// For other BPJS services, use appropriate prefixes:
// BPJS_VCLAIM_CONS_ID, BPJS_VCLAIM_CONS_PWD
// BPJS_ICARE_CONS_ID, BPJS_ICARE_CONS_PWD
// etc.