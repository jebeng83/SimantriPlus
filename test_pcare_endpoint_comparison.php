<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

class PcareEndpointTest
{
    private $baseUrl;
    private $consId;
    private $userKey;
    private $username;
    private $password;
    private $appCode;
    private $secretKey;
    
    public function __construct()
    {
        $this->baseUrl = $_ENV['BPJS_PCARE_BASE_URL'];
        $this->consId = $_ENV['BPJS_PCARE_CONS_ID'];
        $this->userKey = $_ENV['BPJS_PCARE_USER_KEY'];
        $this->username = $_ENV['BPJS_PCARE_USER'];
        $this->password = $_ENV['BPJS_PCARE_PASS'];
        $this->appCode = $_ENV['BPJS_PCARE_APP_CODE'];
        $this->secretKey = $_ENV['BPJS_PCARE_CONS_PWD'];
    }
    
    private function getTimestamp()
    {
        date_default_timezone_set('UTC');
        return strval(time());
    }
    
    private function generateSignature($timestamp)
    {
        $data = $this->consId . "&" . $timestamp;
        $signature = hash_hmac('sha256', $data, $this->secretKey, true);
        return base64_encode($signature);
    }
    
    private function generateAuthorization()
    {
        $data = $this->username . ":" . $this->password . ":" . $this->appCode;
        return base64_encode($data);
    }
    
    private function makeRequest($endpoint, $method = 'GET', $data = null)
    {
        $timestamp = $this->getTimestamp();
        $signature = $this->generateSignature($timestamp);
        $authorization = $this->generateAuthorization();
        
        $headers = [
            'X-cons-id: ' . $this->consId,
            'X-timestamp: ' . $timestamp,
            'X-signature: ' . $signature,
            'X-authorization: Basic ' . $authorization,
            'user_key: ' . $this->userKey,
            'Content-Type: text/plain',
            'Accept: application/json'
        ];
        
        $fullUrl = rtrim($this->baseUrl, '/') . '/' . $endpoint;
        
        echo "\n=== TESTING ENDPOINT: {$endpoint} ===\n";
        echo "URL: {$fullUrl}\n";
        echo "Method: {$method}\n";
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $fullUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        if ($method === 'POST' && $data) {
            $jsonData = json_encode($data);
            echo "\nData to send:\n" . substr($jsonData, 0, 200) . "...\n";
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        }
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            echo "\nCURL ERROR: {$error}\n";
            return null;
        }
        
        echo "\nHTTP Status: {$httpCode}\n";
        echo "Response Body: " . substr($response, 0, 500) . "...\n";
        
        $responseData = json_decode($response, true);
        
        if (isset($responseData['metaData'])) {
            echo "\nMetaData Code: " . ($responseData['metaData']['code'] ?? 'N/A') . "\n";
            echo "MetaData Message: " . ($responseData['metaData']['message'] ?? 'N/A') . "\n";
        }
        
        return $responseData;
    }
    
    public function testEndpoints()
    {
        echo "\n=== PCARE ENDPOINT COMPARISON TEST ===\n";
        echo "Testing different PCare endpoints with same credentials...\n";
        
        // Test 1: Provider endpoint (should work if credentials are correct)
        echo "\n\n1. Testing Provider endpoint (basic connectivity test)...\n";
        $this->makeRequest('provider');
        
        // Test 2: Pendaftaran endpoint (we know this works)
        echo "\n\n2. Testing Pendaftaran endpoint (known to work)...\n";
        $samplePendaftaranData = [
            "kdProviderPeserta" => "11251616",
            "tglDaftar" => "05-08-2025",
            "noKartu" => "0002062926922",
            "kdPoli" => "003",
            "keluhan" => "Test endpoint",
            "kunjSakit" => true,
            "sistole" => 120,
            "diastole" => 80,
            "beratBadan" => 70.0,
            "tinggiBadan" => 170.0,
            "respRate" => 20,
            "lingkarPerut" => 80.0,
            "heartRate" => 80,
            "rujukBalik" => 0,
            "kdTkp" => "10"
        ];
        $this->makeRequest('pendaftaran', 'POST', $samplePendaftaranData);
        
        // Test 3: Kunjungan endpoint (this is failing)
        echo "\n\n3. Testing Kunjungan endpoint (currently failing)...\n";
        $sampleKunjunganData = [
            "noKunjungan" => null,
            "noKartu" => "0002062926922",
            "tglDaftar" => "05-08-2025",
            "kdPoli" => "003",
            "keluhan" => "Test endpoint",
            "kunjSakit" => true,
            "kdSadar" => "04",
            "sistole" => 120,
            "diastole" => 80,
            "beratBadan" => 67.0,
            "tinggiBadan" => 165.0,
            "respRate" => 20,
            "heartRate" => 80,
            "lingkarPerut" => 72.0,
            "rujukBalik" => 0,
            "kdTkp" => "10",
            "kdStatusPulang" => "4",
            "tglPulang" => "05-08-2025",
            "kdDokter" => "131491",
            "kdDiag1" => "K29",
            "kdDiag2" => null,
            "kdDiag3" => null,
            "kdPoliRujukInternal" => null,
            "rujukLanjut" => null,
            "kdTacc" => -1,
            "alasanTacc" => null,
            "anamnesa" => "Test endpoint",
            "alergiMakan" => "00",
            "alergiUdara" => "00",
            "alergiObat" => "00",
            "kdPrognosa" => "01",
            "terapiObat" => "Test obat",
            "terapiNonObat" => "Test non obat",
            "bmhp" => "Tidak ada",
            "suhu" => "36.5"
        ];
        $this->makeRequest('kunjungan', 'POST', $sampleKunjunganData);
        
        // Test 4: Dokter endpoint
        echo "\n\n4. Testing Dokter endpoint...\n";
        $this->makeRequest('dokter');
        
        echo "\n\n=== TEST COMPLETED ===\n";
        echo "\n=== ANALYSIS ===\n";
        echo "Based on the test results above:\n";
        echo "\n1. If Provider/Dokter endpoints return 200 OK but Kunjungan returns 404/412:\n";
        echo "   → Your facility is NOT registered for the Kunjungan PCare service\n";
        echo "   → Contact BPJS administrator to register for Kunjungan service\n";
        echo "\n2. If Pendaftaran works (201 CREATED) but Kunjungan fails:\n";
        echo "   → This confirms service registration issue\n";
        echo "   → Different services require separate registration\n";
        echo "\n3. If all endpoints fail with authentication errors:\n";
        echo "   → Check credentials in .env file\n";
        echo "\n4. If all endpoints return 404:\n";
        echo "   → Check base URL configuration\n";
        echo "\nRECOMMENDATION:\n";
        echo "Contact your BPJS administrator and request registration for:\n";
        echo "- PCare Kunjungan Service\n";
        echo "- Provide your facility code: 11251616\n";
        echo "- Mention that Pendaftaran works but Kunjungan doesn't\n";
    }
}

$test = new PcareEndpointTest();
$test->testEndpoints();

?>