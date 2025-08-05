<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestPcareEndpoint
{
    use PcareTrait;
    
    public function testKunjunganEndpoint()
    {
        echo "=== TESTING PCARE KUNJUNGAN ENDPOINT ===\n";
        
        try {
            // 1. Test koneksi dasar
            echo "\n1. Testing koneksi dasar ke PCare...\n";
            $testResponse = $this->requestPcare('provider', 'GET');
            echo "✓ Koneksi berhasil\n";
            echo "Provider response: " . json_encode($testResponse, JSON_PRETTY_PRINT) . "\n";
            
            // 2. Siapkan data kunjungan minimal
            echo "\n2. Menyiapkan data kunjungan test...\n";
            $kunjunganData = [
                'noKunjungan' => null,
                'noKartu' => '0001234567890', // Nomor kartu test
                'tglDaftar' => date('d-m-Y'),
                'kdPoli' => '001', // Poli umum
                'keluhan' => 'Test kunjungan',
                'kdSadar' => '04', // Compos Mentis
                'sistole' => 120,
                'diastole' => 80,
                'beratBadan' => 60.0,
                'tinggiBadan' => 170.0,
                'respRate' => 20,
                'heartRate' => 80,
                'lingkarPerut' => 0.0,
                'kdStatusPulang' => '4', // Sehat
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => '001',
                'kdDiag1' => 'Z00.0',
                'kdDiag2' => null,
                'kdDiag3' => null,
                'kdPoliRujukInternal' => null,
                'rujukLanjut' => null,
                'kdTacc' => -1,
                'alasanTacc' => null,
                'anamnesa' => 'Test anamnesa',
                'alergiMakan' => '00',
                'alergiUdara' => '00',
                'alergiObat' => '00',
                'kdPrognosa' => '01', // Baik
                'terapiObat' => 'Test terapi',
                'terapiNonObat' => 'Test terapi non obat',
                'bmhp' => 'Tidak ada',
                'suhu' => '36.5'
            ];
            
            echo "Data kunjungan yang akan dikirim:\n";
            echo json_encode($kunjunganData, JSON_PRETTY_PRINT) . "\n";
            
            // 3. Test endpoint kunjungan dengan berbagai method
            echo "\n3. Testing endpoint kunjungan...\n";
            
            // Test GET dulu untuk melihat struktur endpoint
            echo "\n3a. Testing GET kunjungan/tglDaftar/" . date('d-m-Y') . "...\n";
            try {
                $getResponse = $this->requestPcare('kunjungan/tglDaftar/' . date('d-m-Y'), 'GET');
                echo "GET Response: " . json_encode($getResponse, JSON_PRETTY_PRINT) . "\n";
            } catch (\Exception $e) {
                echo "GET Error: " . $e->getMessage() . "\n";
            }
            
            // Test POST ke endpoint kunjungan
            echo "\n3b. Testing POST kunjungan...\n";
            try {
                $postResponse = $this->requestPcare('kunjungan', 'POST', $kunjunganData, 'application/json');
                echo "POST Response: " . json_encode($postResponse, JSON_PRETTY_PRINT) . "\n";
                
                if (empty($postResponse)) {
                    echo "⚠️  Response kosong!\n";
                } else {
                    echo "✓ Response diterima\n";
                    
                    if (isset($postResponse['metaData'])) {
                        echo "MetaData Code: " . ($postResponse['metaData']['code'] ?? 'NULL') . "\n";
                        echo "MetaData Message: " . ($postResponse['metaData']['message'] ?? 'NULL') . "\n";
                    }
                    
                    if (isset($postResponse['response'])) {
                        echo "Response Data: " . json_encode($postResponse['response'], JSON_PRETTY_PRINT) . "\n";
                    }
                }
            } catch (\Exception $e) {
                echo "POST Error: " . $e->getMessage() . "\n";
                echo "Error Class: " . get_class($e) . "\n";
                echo "Error File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            }
            
            // 4. Test dengan data dari database real
            echo "\n4. Testing dengan data pasien real...\n";
            $realPatient = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->whereNotNull('pasien.no_peserta')
                ->where('pasien.no_peserta', '!=', '')
                ->where('reg_periksa.tgl_registrasi', '>=', date('Y-m-d', strtotime('-7 days')))
                ->select('reg_periksa.*', 'pasien.no_peserta', 'pasien.nm_pasien')
                ->first();
                
            if ($realPatient) {
                echo "✓ Ditemukan pasien BPJS: {$realPatient->nm_pasien} (No Peserta: {$realPatient->no_peserta})\n";
                
                $realKunjunganData = $kunjunganData;
                $realKunjunganData['noKartu'] = $realPatient->no_peserta;
                $realKunjunganData['tglDaftar'] = date('d-m-Y', strtotime($realPatient->tgl_registrasi));
                
                echo "Testing dengan data real...\n";
                try {
                    $realResponse = $this->requestPcare('kunjungan', 'POST', $realKunjunganData, 'application/json');
                    echo "Real Response: " . json_encode($realResponse, JSON_PRETTY_PRINT) . "\n";
                } catch (\Exception $e) {
                    echo "Real POST Error: " . $e->getMessage() . "\n";
                }
            } else {
                echo "⚠️  Tidak ditemukan pasien BPJS dalam 7 hari terakhir\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            echo "Trace: " . $e->getTraceAsString() . "\n";
        }
    }
}

$tester = new TestPcareEndpoint();
$tester->testKunjunganEndpoint();