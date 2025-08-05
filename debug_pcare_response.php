<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class DebugPcareResponse
{
    use PcareTrait;
    
    public function debugKunjunganResponse()
    {
        echo "=== DEBUG PCARE RESPONSE DETAIL ===\n";
        
        try {
            // Ambil data pasien yang sama
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('maping_dokter_pcare', 'reg_periksa.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
                ->leftJoin('maping_poliklinik_pcare', 'reg_periksa.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
                ->where('pasien.no_peserta', '!=', '')
                ->whereNotNull('pasien.no_peserta')
                ->whereNotNull('maping_dokter_pcare.kd_dokter_pcare')
                ->whereNotNull('maping_poliklinik_pcare.kd_poli_pcare')
                ->select(
                    'reg_periksa.*',
                    'pasien.no_peserta',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'maping_dokter_pcare.kd_dokter_pcare',
                    'maping_poliklinik_pcare.kd_poli_pcare'
                )
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->first();
            
            if (!$dataPasien) {
                echo "❌ Tidak ada data pasien\n";
                return;
            }
            
            echo "✓ Menggunakan data pasien: {$dataPasien->nm_pasien}\n";
            
            // Ambil data pemeriksaan
            $pemeriksaanData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $dataPasien->no_rawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->first();
            
            // Ambil data diagnosa
            $diagnosaData = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $dataPasien->no_rawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
            
            // Siapkan data kunjungan minimal
            $kunjunganData = [
                'noKartu' => $dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => $dataPasien->kd_poli_pcare,
                'keluhan' => 'Kontrol rutin',
                'kunjSakit' => true,
                'sistole' => 120,
                'diastole' => 80,
                'beratBadan' => 60,
                'tinggiBadan' => 160,
                'respRate' => 20,
                'heartRate' => 80,
                'rujukBalik' => 0,
                'kdTkp' => '10',
                'kdDokter' => $dataPasien->kd_dokter_pcare,
                'kdDiag1' => $diagnosaData->kd_penyakit ?? 'Z00.0'
            ];
            
            echo "\n=== TESTING DENGAN CURL MANUAL ===\n";
            
            // Manual curl untuk debug
            $baseUrl = config('bpjs.pcare.base_url');
            $consId = config('bpjs.pcare.cons_id');
            $secretKey = config('bpjs.pcare.secret_key');
            $username = config('bpjs.pcare.username');
            $password = config('bpjs.pcare.password');
            
            echo "Base URL: {$baseUrl}\n";
            echo "Cons ID: {$consId}\n";
            
            // Generate timestamp dan signature
            $timestamp = time();
            $signature = hash_hmac('sha256', $consId . '&' . $timestamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            
            // Generate auth
            $auth = base64_encode($username . ':' . $password . ':' . date('Y-m-d H:i:s'));
            
            // Encrypt data
            $jsonData = json_encode($kunjunganData);
            echo "\nData JSON: {$jsonData}\n";
            
            $key = $consId . $secretKey . $timestamp;
            $encryptedData = $this->encrypt($jsonData, $key);
            
            // Headers
            $headers = [
                'X-cons-id: ' . $consId,
                'X-timestamp: ' . $timestamp,
                'X-signature: ' . $encodedSignature,
                'X-authorization: Basic ' . $auth,
                'Content-Type: application/json',
                'Accept: application/json'
            ];
            
            echo "\nHeaders:\n";
            foreach ($headers as $header) {
                echo "  {$header}\n";
            }
            
            // URL endpoint
            $fullUrl = rtrim($baseUrl, '/') . '/pcare-rest/kunjungan';
            echo "\nFull URL: {$fullUrl}\n";
            
            // CURL request
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $fullUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode(['request' => $encryptedData]),
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_VERBOSE => true,
                CURLOPT_HEADER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            echo "\n=== MENGIRIM REQUEST ===\n";
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $error = curl_error($ch);
            curl_close($ch);
            
            echo "HTTP Code: {$httpCode}\n";
            
            if ($error) {
                echo "CURL Error: {$error}\n";
                return;
            }
            
            // Pisahkan header dan body
            $responseHeaders = substr($response, 0, $headerSize);
            $responseBody = substr($response, $headerSize);
            
            echo "\n=== RESPONSE HEADERS ===\n";
            echo $responseHeaders;
            
            echo "\n=== RESPONSE BODY (RAW) ===\n";
            echo "Length: " . strlen($responseBody) . " characters\n";
            echo "Body: {$responseBody}\n";
            
            if (!empty($responseBody)) {
                // Coba decode JSON
                $jsonResponse = json_decode($responseBody, true);
                if ($jsonResponse) {
                    echo "\n=== RESPONSE JSON ===\n";
                    echo json_encode($jsonResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                    
                    // Coba decrypt jika ada response field
                    if (isset($jsonResponse['response'])) {
                        echo "\n=== DECRYPTING RESPONSE ===\n";
                        try {
                            $decryptedResponse = $this->decrypt($jsonResponse['response'], $key);
                            echo "Decrypted: {$decryptedResponse}\n";
                            
                            // Coba decompress
                            $decompressed = $this->decompress($decryptedResponse);
                            echo "Decompressed: {$decompressed}\n";
                            
                            $finalData = json_decode($decompressed, true);
                            if ($finalData) {
                                echo "\n=== FINAL RESPONSE DATA ===\n";
                                echo json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                            }
                        } catch (\Exception $e) {
                            echo "Decrypt error: " . $e->getMessage() . "\n";
                        }
                    }
                } else {
                    echo "\n❌ Response bukan JSON valid\n";
                }
            } else {
                echo "\n⚠️  Response body kosong\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
    
    private function encrypt($data, $key)
    {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
        return base64_encode($iv . base64_decode($encrypted));
    }
    
    private function decrypt($data, $key)
    {
        $key = substr(hash('sha256', $key, true), 0, 32);
        $data = base64_decode($data);
        $iv = substr($data, 0, 16);
        $encrypted = substr($data, 16);
        return openssl_decrypt(base64_encode($encrypted), 'AES-256-CBC', $key, 0, $iv);
    }
    
    private function decompress($data)
    {
        return gzuncompress($data);
    }
}

// Jalankan debug
$debug = new DebugPcareResponse();
$debug->debugKunjunganResponse();

echo "\n=== DEBUG SELESAI ===\n";