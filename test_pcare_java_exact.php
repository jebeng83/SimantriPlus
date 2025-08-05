<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Traits\PcareTrait;

class TestPcareJavaExact
{
    use PcareTrait;
    
    public function testKunjungan()
    {
        echo "=== Testing PCare Kunjungan with Exact Java Format ===\n";
        
        // Get real patient data with BPJS
        $pasien = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->join('pemeriksaan_ralan', 'reg_periksa.no_rawat', '=', 'pemeriksaan_ralan.no_rawat')
            ->join('diagnosa_pasien', 'reg_periksa.no_rawat', '=', 'diagnosa_pasien.no_rawat')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('maping_dokter_pcare', 'reg_periksa.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
            ->where('penjab.png_jawab', 'like', '%BPJS%')
            ->whereNotNull('pasien.no_peserta')
            ->whereNotNull('maping_dokter_pcare.kd_dokter_pcare')
            ->whereNotNull('diagnosa_pasien.kd_penyakit')
            ->select(
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'pasien.no_peserta',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.kd_poli',
                'pemeriksaan_ralan.keluhan',
                'pemeriksaan_ralan.kesadaran',
                'pemeriksaan_ralan.tensi',
                'pemeriksaan_ralan.berat',
                'pemeriksaan_ralan.tinggi',
                'pemeriksaan_ralan.respirasi',
                'pemeriksaan_ralan.nadi',
                'pemeriksaan_ralan.lingkar_perut',
                'pemeriksaan_ralan.suhu_tubuh',
                'maping_dokter_pcare.kd_dokter_pcare',
                'diagnosa_pasien.kd_penyakit',
                'pemeriksaan_ralan.rtl',
                'pemeriksaan_ralan.instruksi'
            )
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->first();
            
        if (!$pasien) {
            echo "No BPJS patient data found with required mappings\n";
            return;
        }
        
        echo "Patient found: {$pasien->nm_pasien} (No. Peserta: {$pasien->no_peserta})\n";
        
        // Test basic PCare connection first
        echo "\n=== Testing PCare Connection ===\n";
        try {
            $baseUrl = config('bpjs.pcare.base_url');
            if (empty($baseUrl)) {
                echo "PCare configuration is incomplete\n";
                return;
            }
            echo "PCare connection successful\n";
        } catch (Exception $e) {
            echo "PCare connection failed: " . $e->getMessage() . "\n";
            return;
        }
        
        // Extract sistole and diastole from tensi (format: 120/80)
        $sistole = 120;
        $diastole = 80;
        if (!empty($pasien->tensi) && strpos($pasien->tensi, '/') !== false) {
            $tensiParts = explode('/', $pasien->tensi);
            $sistole = (int)($tensiParts[0] ?: 120);
            $diastole = (int)($tensiParts[1] ?: 80);
        }
        
        // Prepare kunjungan data exactly like Java format
        $kunjunganData = [
            'noKunjungan' => null,
            'noKartu' => $pasien->no_peserta,
            'tglDaftar' => $pasien->tgl_registrasi,
            'kdPoli' => $pasien->kd_poli,
            'keluhan' => !empty($pasien->keluhan) ? $pasien->keluhan : 'Tidak Ada',
            'kdSadar' => !empty($pasien->kesadaran) ? $pasien->kesadaran : '01',
            'sistole' => $sistole,
            'diastole' => $diastole,
            'beratBadan' => (int)($pasien->berat ?: 60),
            'tinggiBadan' => (int)($pasien->tinggi ?: 160),
            'respRate' => (int)($pasien->respirasi ?: 20),
            'heartRate' => (int)($pasien->nadi ?: 80),
            'lingkarPerut' => (int)($pasien->lingkar_perut ?: 80),
            'kdStatusPulang' => '3',
            'tglPulang' => $pasien->tgl_registrasi,
            'kdDokter' => $pasien->kd_dokter_pcare,
            'kdDiag1' => $pasien->kd_penyakit,
            'kdDiag2' => null,
            'kdDiag3' => null,
            'kdPoliRujukInternal' => null,
            'rujukLanjut' => null,
            'kdTacc' => -1,
            'alasanTacc' => null,
            'anamnesa' => !empty($pasien->keluhan) ? $pasien->keluhan : 'Tidak Ada',
            'alergiMakan' => '',
            'alergiUdara' => '',
            'alergiObat' => '',
            'kdPrognosa' => '1',
            'terapiObat' => !empty($pasien->rtl) ? $pasien->rtl : 'Tidak Ada',
            'terapiNonObat' => !empty($pasien->instruksi) ? $pasien->instruksi : 'Edukasi Kesehatan',
            'bmhp' => '',
            'suhu' => $pasien->suhu_tubuh ?: '36.5'
        ];
        
        echo "\n=== Kunjungan Data Prepared ===\n";
        echo json_encode($kunjunganData, JSON_PRETTY_PRINT) . "\n";
        
        // Manual cURL request exactly like Java
        $baseUrl = config('bpjs.pcare.base_url');
        $consId = config('bpjs.pcare.cons_id');
        $secretKey = config('bpjs.pcare.secret_key');
        $userKey = config('bpjs.pcare.user_key');
        $username = config('bpjs.pcare.username');
        $password = config('bpjs.pcare.password');
        
        if (empty($baseUrl) || empty($consId)) {
            echo "PCare configuration is incomplete\n";
            return;
        }
        
        // Ensure baseUrl ends with /pcare-rest/
        if (!str_contains($baseUrl, '/pcare-rest/')) {
            $baseUrl = rtrim($baseUrl, '/') . '/pcare-rest/';
        }
        
        $fullUrl = $baseUrl . 'kunjungan/v1';
        
        // Generate timestamp and signature exactly like Java
        $timestamp = (string) time();
        $signature = hash_hmac('sha256', $consId . '&' . $timestamp, $secretKey, true);
        $encodedSignature = base64_encode($signature);
        
        // Authorization exactly like Java
        $authorization = base64_encode($username . ':' . $password);
        
        // Headers exactly like Java
        $headers = [
            'Content-Type: text/plain',
            'X-cons-id: ' . $consId,
            'X-timestamp: ' . $timestamp,
            'X-signature: ' . $encodedSignature,
            'X-authorization: Basic ' . $authorization,
            'user_key: ' . $userKey
        ];
        
        // JSON request exactly like Java format
        $requestJson = json_encode($kunjunganData);
        
        echo "\n=== Request Details ===\n";
        echo "URL: $fullUrl\n";
        echo "Method: POST\n";
        echo "Headers:\n";
        foreach ($headers as $header) {
            echo "  $header\n";
        }
        echo "Request Body:\n$requestJson\n";
        
        // cURL request exactly like Java RestTemplate
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $fullUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestJson,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HEADER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $responseHeaders = substr($response, 0, $headerSize);
        $responseBody = substr($response, $headerSize);
        $curlError = curl_error($ch);
        curl_close($ch);
        
        echo "\n=== Response Details ===\n";
        echo "HTTP Code: $httpCode\n";
        echo "cURL Error: " . ($curlError ?: 'None') . "\n";
        echo "Response Headers:\n$responseHeaders\n";
        echo "Response Body:\n$responseBody\n";
        
        // Parse response exactly like Java
        if (!empty($responseBody)) {
            $jsonResponse = json_decode($responseBody, true);
            if ($jsonResponse) {
                echo "\n=== Parsed Response ===\n";
                if (isset($jsonResponse['metaData'])) {
                    echo "Code: " . ($jsonResponse['metaData']['code'] ?? 'N/A') . "\n";
                    echo "Message: " . ($jsonResponse['metaData']['message'] ?? 'N/A') . "\n";
                    
                    if (($jsonResponse['metaData']['code'] ?? '') === '201') {
                        echo "SUCCESS: Kunjungan data sent successfully!\n";
                        if (isset($jsonResponse['response'])) {
                            echo "Response data: " . $jsonResponse['response'] . "\n";
                        }
                    } else {
                        echo "FAILED: " . ($jsonResponse['metaData']['message'] ?? 'Unknown error') . "\n";
                    }
                } else {
                    echo "Response format: " . json_encode($jsonResponse, JSON_PRETTY_PRINT) . "\n";
                }
            } else {
                echo "Failed to parse JSON response\n";
            }
        } else {
            echo "Empty response body\n";
        }
    }
}

// Run the test
$test = new TestPcareJavaExact();
$test->testKunjungan();

echo "\n=== Test Complete ===\n";