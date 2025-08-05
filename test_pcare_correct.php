<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestPcareCorrect
{
    use PcareTrait;
    
    public function testKunjunganWithRealData()
    {
        echo "=== TEST PCARE KUNJUNGAN DENGAN DATA REAL ===\n";
        
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
            
            // 4. Test koneksi dasar dulu
            echo "\n2. Testing koneksi dasar...\n";
            try {
                $providerResponse = $this->requestPcare('provider', 'GET');
                echo "✓ Koneksi ke PCare berhasil\n";
                if (isset($providerResponse['metaData'])) {
                    echo "  Provider Code: " . ($providerResponse['metaData']['code'] ?? 'NULL') . "\n";
                    echo "  Provider Message: " . ($providerResponse['metaData']['message'] ?? 'NULL') . "\n";
                }
            } catch (\Exception $e) {
                echo "❌ Koneksi ke PCare gagal: " . $e->getMessage() . "\n";
                return;
            }
            
            // 5. Siapkan data kunjungan
            echo "\n3. Menyiapkan data kunjungan...\n";
            
            $kunjunganData = [
                'noKartu' => $dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => $dataPasien->kd_poli_pcare,
                'keluhan' => $pemeriksaanData->keluhan ?? 'Kontrol rutin',
                'kunjSakit' => true,
                'sistole' => (int)($pemeriksaanData->sistole ?? 120),
                'diastole' => (int)($pemeriksaanData->diastole ?? 80),
                'beratBadan' => (int)($pemeriksaanData->berat ?? 60),
                'tinggiBadan' => (int)($pemeriksaanData->tinggi ?? 160),
                'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
                'lingkarPerut' => (int)($pemeriksaanData->lingkar_perut ?? 80),
                'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
                'rujukBalik' => 0,
                'kdTkp' => '10',
                'kdStatusPulang' => '4',
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => $dataPasien->kd_dokter_pcare,
                'kdDiag1' => $diagnosaData->kd_penyakit ?? 'Z00.0',
                'kdDiag2' => null,
                'kdDiag3' => null,
                'kdPoliRujukInternal' => null,
                'rujukLanjut' => null,
                'kdTacc' => -1,
                'alasanTacc' => null,
                'anamnesa' => $pemeriksaanData->anamnesis ?? 'Pemeriksaan rutin',
                'alergiMakan' => '00',
                'alergiUdara' => '00',
                'alergiObat' => '00',
                'kdPrognosa' => '01',
                'terapiObat' => $pemeriksaanData->rtl ?? 'Sesuai indikasi',
                'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Kontrol rutin',
                'bmhp' => 'Tidak ada',
                'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
            ];
            
            echo "✓ Data kunjungan disiapkan:\n";
            echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            // 6. Test GET kunjungan dulu
            echo "\n4. Testing GET kunjungan...\n";
            try {
                $tglDaftar = date('d-m-Y', strtotime($dataPasien->tgl_registrasi));
                $getEndpoint = "kunjungan/tglDaftar/{$tglDaftar}";
                echo "GET Endpoint: {$getEndpoint}\n";
                
                $getResponse = $this->requestPcare($getEndpoint, 'GET');
                echo "✓ GET kunjungan berhasil\n";
                if (isset($getResponse['metaData'])) {
                    echo "  GET Code: " . ($getResponse['metaData']['code'] ?? 'NULL') . "\n";
                    echo "  GET Message: " . ($getResponse['metaData']['message'] ?? 'NULL') . "\n";
                }
            } catch (\Exception $e) {
                echo "❌ GET kunjungan gagal: " . $e->getMessage() . "\n";
            }
            
            // 7. Test POST kunjungan
            echo "\n5. Testing POST kunjungan...\n";
            try {
                $postResponse = $this->requestPcare('kunjungan', 'POST', $kunjunganData);
                echo "✓ POST kunjungan berhasil dikirim\n";
                
                if (empty($postResponse)) {
                    echo "⚠️  Response kosong dari PCare\n";
                } else {
                    echo "✓ Response diterima dari PCare\n";
                    echo json_encode($postResponse, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
                    
                    if (isset($postResponse['metaData'])) {
                        $code = $postResponse['metaData']['code'] ?? 'NULL';
                        $message = $postResponse['metaData']['message'] ?? 'NULL';
                        echo "  POST Code: {$code}\n";
                        echo "  POST Message: {$message}\n";
                        
                        if ($code == 200 || $code == 201) {
                            echo "🎉 Kunjungan berhasil dikirim ke PCare!\n";
                        } else {
                            echo "❌ Kunjungan gagal: {$message}\n";
                        }
                    }
                }
            } catch (\Exception $e) {
                echo "❌ POST kunjungan gagal: " . $e->getMessage() . "\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
}

// Jalankan test
$test = new TestPcareCorrect();
$test->testKunjunganWithRealData();

echo "\n=== TEST SELESAI ===\n";