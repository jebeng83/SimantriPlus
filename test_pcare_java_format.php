<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestPcareJavaFormat
{
    use PcareTrait;
    
    public function testKunjunganJavaFormat()
    {
        echo "=== TEST PCARE KUNJUNGAN DENGAN FORMAT JAVA ===\n";
        
        try {
            // 1. Ambil data pasien BPJS dengan mapping dokter dan poli
            echo "\n1. Mengambil data pasien BPJS...\n";
            
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
                echo "❌ Tidak ada data pasien BPJS dengan mapping lengkap\n";
                return;
            }
            
            echo "✓ Data pasien ditemukan:\n";
            echo "  No. Rawat: {$dataPasien->no_rawat}\n";
            echo "  Nama: {$dataPasien->nm_pasien}\n";
            echo "  No. Peserta: {$dataPasien->no_peserta}\n";
            echo "  Dokter PCare: {$dataPasien->kd_dokter_pcare}\n";
            echo "  Poli PCare: {$dataPasien->kd_poli_pcare}\n";
            
            // 2. Ambil data pemeriksaan
            $pemeriksaanData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $dataPasien->no_rawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->first();
            
            // 3. Ambil data diagnosa
            $diagnosaData = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $dataPasien->no_rawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
            
            // 4. Cek apakah sudah ada kunjungan hari ini
            $existingKunjungan = DB::table('pcare_kunjungan')
                ->where('no_rawat', $dataPasien->no_rawat)
                ->where('tglDaftar', date('Y-m-d', strtotime($dataPasien->tgl_registrasi)))
                ->first();
            
            $noKunjungan = null;
            if ($existingKunjungan) {
                $noKunjungan = $existingKunjungan->noKunjungan;
                echo "✓ Menggunakan noKunjungan existing: {$noKunjungan}\n";
            } else {
                echo "⚠️  Tidak ada noKunjungan, akan menggunakan format default\n";
                // Generate noKunjungan format: YYYYMMDD + no_rawat
                $noKunjungan = date('Ymd', strtotime($dataPasien->tgl_registrasi)) . str_replace('/', '', $dataPasien->no_rawat);
            }
            
            // 5. Siapkan data kunjungan sesuai format Java
            echo "\n2. Menyiapkan data kunjungan format Java...\n";
            
            $kunjunganData = [
                'noKunjungan' => $noKunjungan,
                'noKartu' => $dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => $dataPasien->kd_poli_pcare,
                'keluhan' => $pemeriksaanData->keluhan ?? 'Tidak Ada',
                'kdSadar' => '01', // Default: Compos Mentis
                'sistole' => (int)($pemeriksaanData->sistole ?? 120),
                'diastole' => (int)($pemeriksaanData->diastole ?? 80),
                'beratBadan' => (int)($pemeriksaanData->berat ?? 60),
                'tinggiBadan' => (int)($pemeriksaanData->tinggi ?? 160),
                'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
                'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
                'lingkarPerut' => (int)($pemeriksaanData->lingkar_perut ?? 80),
                'kdStatusPulang' => '3', // Sesuai contoh Java
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => $dataPasien->kd_dokter_pcare,
                'kdDiag1' => $diagnosaData->kd_penyakit ?? 'Z00.0',
                'kdDiag2' => null,
                'kdDiag3' => null,
                'kdPoliRujukInternal' => null,
                'rujukLanjut' => null,
                'kdTacc' => -1,
                'alasanTacc' => null,
                'anamnesa' => $pemeriksaanData->anamnesis ?? 'Tidak Ada',
                'alergiMakan' => '00', // Default: Tidak ada alergi
                'alergiUdara' => '00', // Default: Tidak ada alergi
                'alergiObat' => '00', // Default: Tidak ada alergi
                'kdPrognosa' => '01', // Default: Baik
                'terapiObat' => $pemeriksaanData->rtl ?? 'Tidak Ada',
                'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Edukasi Kesehatan',
                'bmhp' => 'Tidak ada',
                'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
            ];
            
            echo "✓ Data kunjungan disiapkan:\n";
            echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            // 6. Test dengan format Java menggunakan PUT method
            echo "\n3. Testing PUT kunjungan dengan format Java...\n";
            
            // Manual curl untuk debug sesuai format Java
            $baseUrl = config('bpjs.pcare.base_url');
            $consId = config('bpjs.pcare.cons_id');
            $secretKey = config('bpjs.pcare.secret_key');
            $username = config('bpjs.pcare.username');
            $password = config('bpjs.pcare.password');
            $userKey = config('bpjs.pcare.user_key');
            
            echo "Base URL: {$baseUrl}\n";
            echo "Cons ID: {$consId}\n";
            echo "User Key: {$userKey}\n";
            
            // Generate timestamp dan signature
            $timestamp = time();
            $signature = hash_hmac('sha256', $consId . '&' . $timestamp, $secretKey, true);
            $encodedSignature = base64_encode($signature);
            
            // Generate auth sesuai format Java
            $auth = base64_encode($username . ':' . $password . ':' . date('Y-m-d H:i:s'));
            
            // Headers sesuai format Java
            $headers = [
                'Content-Type: text/plain', // Sesuai Java: MediaType.TEXT_PLAIN
                'X-cons-id: ' . $consId,
                'X-timestamp: ' . $timestamp,
                'X-signature: ' . $encodedSignature,
                'X-authorization: Basic ' . $auth,
                'user_key: ' . $userKey
            ];
            
            echo "\nHeaders:\n";
            foreach ($headers as $header) {
                echo "  {$header}\n";
            }
            
            // URL endpoint sesuai Java (menggunakan PUT)
            $fullUrl = rtrim($baseUrl, '/') . '/pcare-rest/kunjungan/';
            echo "\nFull URL: {$fullUrl}\n";
            
            // Data JSON sesuai format Java
            $jsonData = json_encode($kunjunganData);
            echo "\nJSON Data: {$jsonData}\n";
            
            // CURL request dengan PUT method
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $fullUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => 'PUT', // Sesuai Java: HttpMethod.PUT
                CURLOPT_POSTFIELDS => $jsonData, // Langsung JSON, bukan encrypted
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_VERBOSE => true,
                CURLOPT_HEADER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ]);
            
            echo "\n=== MENGIRIM REQUEST PUT ===\n";
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
                    
                    if (isset($jsonResponse['metaData'])) {
                        $code = $jsonResponse['metaData']['code'] ?? 'NULL';
                        $message = $jsonResponse['metaData']['message'] ?? 'NULL';
                        echo "\n=== HASIL ===\n";
                        echo "Code: {$code}\n";
                        echo "Message: {$message}\n";
                        
                        if ($code == 200) {
                            echo "🎉 Kunjungan berhasil dikirim ke PCare!\n";
                        } else {
                            echo "❌ Kunjungan gagal: {$message}\n";
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
}

// Jalankan test
$test = new TestPcareJavaFormat();
$test->testKunjunganJavaFormat();

echo "\n=== TEST SELESAI ===\n";