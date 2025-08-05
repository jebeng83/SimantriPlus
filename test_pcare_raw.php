<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use LZString\LZString;

class TestPcareRaw {
    
    private function generateTimestamp() {
        return (int)(microtime(true) * 1000);
    }
    
    private function generateSignature($method, $url, $timestamp, $data = null) {
        $consId = env('BPJS_PCARE_CONS_ID');
        $consSecret = env('BPJS_PCARE_CONS_PWD');
        
        $stringToSign = $method . "&" . urlencode($url) . "&" . $timestamp;
        if ($data && $method !== 'GET') {
            $stringToSign .= "&" . hash('sha256', $data);
        }
        
        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $consSecret . "&", true));
        return $signature;
    }
    
    private function generateAuth($timestamp) {
        $username = env('BPJS_PCARE_USER');
        $password = env('BPJS_PCARE_PASS');
        $consId = env('BPJS_PCARE_CONS_ID');
        $consSecret = env('BPJS_PCARE_CONS_PWD');
        
        $key = $consId . $consSecret . $timestamp;
        $auth = $this->stringEncrypt($key, $username . ':' . $password . ':' . env('BPJS_PCARE_APP_CODE'));
        return $auth;
    }
    
    private function stringEncrypt($key, $string) {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hash('sha256', $key);
        $iv = substr($key_hash, 0, 16);
        $output = openssl_encrypt($string, $encrypt_method, $key_hash, 0, $iv);
        return base64_encode($output);
    }
    
    private function stringDecrypt($key, $string) {
        $encrypt_method = 'AES-256-CBC';
        $key_hash = hash('sha256', $key);
        $iv = substr($key_hash, 0, 16);
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key_hash, 0, $iv);
        return $output;
    }
    
    public function testRawRequest() {
        echo "=== RAW PCARE API TEST ===\n";
        
        $baseUrl = env('BPJS_PCARE_BASE_URL');
        $endpoint = 'kunjungan';
        $fullUrl = $baseUrl . '/' . $endpoint;
        
        echo "Base URL: {$baseUrl}\n";
        echo "Full URL: {$fullUrl}\n\n";
        
        // Test data dengan field yang diperlukan
        $kunjunganData = [
            'noKunjungan' => null,
            'noKartu' => '0001441910575',
            'tglDaftar' => '05-08-2025',
            'kdPoli' => '003',
            'keluhan' => 'Pasien melakukan kontrol rutin.',
            'kunjSakit' => true,
            'kdSadar' => '04',
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 45.0,
            'tinggiBadan' => 145.0,
            'respRate' => 20,
            'heartRate' => 80,
            'lingkarPerut' => 0.0,
            'rujukBalik' => 0,
            'kdTkp' => '10',
            'kdStatusPulang' => '4',
            'tglPulang' => '05-08-2025',
            'kdDokter' => 'DK001',
            'kdDiag1' => 'J00'
        ];
        
        $jsonData = json_encode($kunjunganData);
        echo "JSON Data: {$jsonData}\n\n";
        
        // Generate headers
        $timestamp = $this->generateTimestamp();
        $signature = $this->generateSignature('POST', $fullUrl, $timestamp, $jsonData);
        $auth = $this->generateAuth($timestamp);
        
        $headers = [
            'X-cons-id' => env('BPJS_PCARE_CONS_ID'),
            'X-timestamp' => $timestamp,
            'X-signature' => $signature,
            'X-authorization' => $auth,
            'user_key' => env('BPJS_PCARE_USER_KEY'),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
        
        echo "Headers:\n";
        foreach ($headers as $key => $value) {
            if ($key === 'X-authorization') {
                echo "  {$key}: [HIDDEN]\n";
            } else {
                echo "  {$key}: {$value}\n";
            }
        }
        echo "\n";
        
        try {
            echo "Sending POST request...\n";
            $response = Http::timeout(30)
                ->withHeaders($headers)
                ->post($fullUrl, $kunjunganData);
            
            echo "Response Status: " . $response->status() . "\n";
            echo "Response Headers: " . json_encode($response->headers()) . "\n";
            echo "Raw Response Body: " . $response->body() . "\n\n";
            
            if ($response->successful()) {
                $responseBody = $response->body();
                
                if (empty($responseBody)) {
                    echo "❌ Response body kosong meskipun status sukses\n";
                } else {
                    echo "✅ Response diterima, mencoba dekripsi...\n";
                    
                    // Coba dekripsi
                    try {
                        $decrypted = $this->decrypt($responseBody, $timestamp);
                        echo "Decrypted Response: " . json_encode($decrypted, JSON_PRETTY_PRINT) . "\n";
                    } catch (Exception $e) {
                        echo "❌ Gagal dekripsi: " . $e->getMessage() . "\n";
                        echo "Raw response mungkin sudah dalam format JSON: " . $responseBody . "\n";
                    }
                }
            } else {
                echo "❌ Request gagal dengan status: " . $response->status() . "\n";
                echo "Error body: " . $response->body() . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Exception: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
        
        echo "\n=== TEST SELESAI ===\n";
    }
    
    private function decrypt($response, $timestamp) {
        if (empty($response)) {
            return null;
        }
        
        $consId = env('BPJS_PCARE_CONS_ID');
        $consSecret = env('BPJS_PCARE_CONS_PWD');
        
        // Generate decryption key
        $key = $consId . $consSecret . $timestamp;
        
        // Decrypt
        $decrypted = $this->stringDecrypt($key, $response);
        
        // Decompress
        $decompressed = LZString::decompressFromEncodedURIComponent($decrypted);
        
        return json_decode($decompressed, true);
    }
}

$test = new TestPcareRaw();
$test->testRawRequest();