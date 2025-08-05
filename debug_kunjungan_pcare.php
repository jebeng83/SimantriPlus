<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

echo "\n=== DEBUG KUNJUNGAN PCARE STEP BY STEP ===\n";

class DebugKunjunganPcare
{
    use PcareTrait;
    
    public function debugKunjunganProcess($noRawat)
    {
        try {
            echo "\n🔍 Step 1: Dekode no_rawat\n";
            $decodedNoRawat = base64_decode($noRawat);
            echo "✓ Decoded: {$decodedNoRawat}\n";
            
            echo "\n🔍 Step 2: Ambil data pasien\n";
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->where('reg_periksa.no_rawat', $decodedNoRawat)
                ->select(
                    'reg_periksa.*',
                    'pasien.no_peserta',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'dokter.kd_dokter'
                )
                ->first();
            
            if (!$dataPasien) {
                echo "❌ Data pasien tidak ditemukan\n";
                return;
            }
            
            echo "✓ Data pasien ditemukan:\n";
            echo "   - Nama: {$dataPasien->nm_pasien}\n";
            echo "   - No Peserta: {$dataPasien->no_peserta}\n";
            echo "   - Poli: {$dataPasien->nm_poli}\n";
            echo "   - Dokter: {$dataPasien->nm_dokter}\n";
            
            if (empty($dataPasien->no_peserta)) {
                echo "❌ Pasien bukan peserta BPJS\n";
                return;
            }
            
            echo "\n🔍 Step 3: Ambil data pemeriksaan\n";
            $pemeriksaanData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $decodedNoRawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->first();
            
            if ($pemeriksaanData) {
                echo "✓ Data pemeriksaan ditemukan\n";
                echo "   - Keluhan: {$pemeriksaanData->keluhan}\n";
                echo "   - Tensi: {$pemeriksaanData->tensi}\n";
                echo "   - Berat: {$pemeriksaanData->berat}\n";
                echo "   - Tinggi: {$pemeriksaanData->tinggi}\n";
            } else {
                echo "⚠️  Data pemeriksaan tidak ditemukan\n";
            }
            
            echo "\n🔍 Step 4: Ambil data diagnosa\n";
            $diagnosaData = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $decodedNoRawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
            
            if ($diagnosaData) {
                echo "✓ Data diagnosa ditemukan:\n";
                echo "   - Kode: {$diagnosaData->kd_penyakit}\n";
                echo "   - Nama: {$diagnosaData->nm_penyakit}\n";
            } else {
                echo "⚠️  Data diagnosa tidak ditemukan, akan gunakan default\n";
            }
            
            echo "\n🔍 Step 5: Persiapkan vital signs\n";
            $sistole = 120;
            $diastole = 80;
            if ($pemeriksaanData && !empty($pemeriksaanData->tensi) && strpos($pemeriksaanData->tensi, '/') !== false) {
                $tensiParts = explode('/', $pemeriksaanData->tensi);
                $sistole = (int)trim($tensiParts[0]) ?: 120;
                $diastole = (int)trim($tensiParts[1]) ?: 80;
            }
            echo "✓ Sistole: {$sistole}, Diastole: {$diastole}\n";
            
            echo "\n🔍 Step 6: Ambil mapping poli PCare\n";
            $kdPoliPcare = $this->getKdPoliPcare($dataPasien->kd_poli);
            echo "✓ Kode Poli RS: {$dataPasien->kd_poli} -> PCare: {$kdPoliPcare}\n";
            
            echo "\n🔍 Step 7: Persiapkan data kunjungan\n";
            $kunjunganData = [
                'noKunjungan' => null,
                'noKartu' => $dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => $kdPoliPcare,
                'keluhan' => $pemeriksaanData->keluhan ?? 'Kontrol rutin',
                'kdSadar' => '04', // Compos Mentis
                'sistole' => $sistole,
                'diastole' => $diastole,
                'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
                'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 170),
                'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
                'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
                'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 0),
                'kdStatusPulang' => '4', // Sehat
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => $dataPasien->kd_dokter,
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
                'kdPrognosa' => '01', // Baik
                'terapiObat' => $pemeriksaanData->rtl ?? 'Sesuai indikasi',
                'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Kontrol rutin',
                'bmhp' => 'Tidak ada',
                'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
            ];
            
            echo "✓ Data kunjungan disiapkan:\n";
            echo "   - No Kartu: {$kunjunganData['noKartu']}\n";
            echo "   - Tanggal: {$kunjunganData['tglDaftar']}\n";
            echo "   - Poli: {$kunjunganData['kdPoli']}\n";
            echo "   - Keluhan: {$kunjunganData['keluhan']}\n";
            echo "   - Diagnosa: {$kunjunganData['kdDiag1']}\n";
            
            echo "\n🔍 Step 8: Test koneksi PCare\n";
            try {
                // Test dengan endpoint sederhana dulu
                echo "📡 Testing koneksi ke PCare...\n";
                $testResponse = $this->requestPcare('provider', 'GET');
                echo "✓ Koneksi PCare berhasil\n";
                echo "Response: " . json_encode($testResponse, JSON_PRETTY_PRINT) . "\n";
            } catch (\Exception $e) {
                echo "❌ Error koneksi PCare: " . $e->getMessage() . "\n";
                return;
            }
            
            echo "\n🔍 Step 9: Kirim data kunjungan ke PCare\n";
            try {
                echo "📤 Mengirim data kunjungan...\n";
                $responseData = $this->requestPcare('kunjungan', 'POST', $kunjunganData, 'application/json');
                
                echo "📥 Response diterima:\n";
                echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n";
                
                echo "\n🔍 Step 10: Proses response dan simpan ke database\n";
                if (isset($responseData['metaData']['code']) && $responseData['metaData']['code'] == '200') {
                    echo "✅ Response sukses dari PCare\n";
                    
                    // Definisikan variabel nama untuk database
                    $nmPoli = $dataPasien->nm_poli ?? '';
                    $nmSadar = 'Compos Mentis';
                    $nmDokter = $dataPasien->nm_dokter ?? '';
                    $nmDiag1 = $diagnosaData->nm_penyakit ?? 'General medical examination';
                    $nmDiag2 = '';
                    $nmDiag3 = '';
                    $nmStatusPulang = 'Sehat';
                    $nmAlergiMakanan = 'Tidak ada';
                    $nmAlergiUdara = 'Tidak ada';
                    $nmAlergiObat = 'Tidak ada';
                    $nmPrognosa = 'Baik';
                    
                    echo "💾 Menyimpan data ke database...\n";
                    $insertData = [
                        'no_rawat' => $decodedNoRawat,
                        'noKunjungan' => $responseData['response']['noKunjungan'] ?? null,
                        'tglDaftar' => $kunjunganData['tglDaftar'],
                        'no_rkm_medis' => $dataPasien->no_rkm_medis,
                        'nm_pasien' => $dataPasien->nm_pasien,
                        'noKartu' => $kunjunganData['noKartu'],
                        'kdPoli' => $kunjunganData['kdPoli'],
                        'nmPoli' => $nmPoli,
                        'keluhan' => $kunjunganData['keluhan'],
                        'kdSadar' => $kunjunganData['kdSadar'],
                        'nmSadar' => $nmSadar,
                        'sistole' => $kunjunganData['sistole'],
                        'diastole' => $kunjunganData['diastole'],
                        'beratBadan' => $kunjunganData['beratBadan'],
                        'tinggiBadan' => $kunjunganData['tinggiBadan'],
                        'respRate' => $kunjunganData['respRate'],
                        'heartRate' => $kunjunganData['heartRate'],
                        'lingkarPerut' => $kunjunganData['lingkarPerut'],
                        'terapi' => $kunjunganData['terapiObat'] ?? '',
                        'kdStatusPulang' => $kunjunganData['kdStatusPulang'],
                        'nmStatusPulang' => $nmStatusPulang,
                        'tglPulang' => $kunjunganData['tglPulang'],
                        'kdDokter' => $kunjunganData['kdDokter'],
                        'nmDokter' => $nmDokter,
                        'kdDiag1' => $kunjunganData['kdDiag1'],
                        'nmDiag1' => $nmDiag1,
                        'kdDiag2' => $kunjunganData['kdDiag2'] ?? '',
                        'nmDiag2' => $nmDiag2,
                        'kdDiag3' => $kunjunganData['kdDiag3'] ?? '',
                        'nmDiag3' => $nmDiag3,
                        'status' => 'Terkirim',
                        'kdAlergiMakanan' => $kunjunganData['alergiMakan'] ?? '',
                        'nmAlergiMakanan' => $nmAlergiMakanan,
                        'kdAlergiUdara' => $kunjunganData['alergiUdara'] ?? '',
                        'nmAlergiUdara' => $nmAlergiUdara,
                        'kdAlergiObat' => $kunjunganData['alergiObat'] ?? '',
                        'nmAlergiObat' => $nmAlergiObat,
                        'kdPrognosa' => $kunjunganData['kdPrognosa'],
                        'nmPrognosa' => $nmPrognosa,
                        'terapi_non_obat' => $kunjunganData['terapiNonObat'] ?? '',
                        'bmhp' => $kunjunganData['bmhp'] ?? '',
                        'updated_at' => now()
                    ];
                    
                    echo "Data yang akan disimpan:\n";
                    foreach ($insertData as $key => $value) {
                        echo "   {$key}: {$value}\n";
                    }
                    
                    $result = DB::table('pcare_kunjungan_umum')->insert($insertData);
                    
                    if ($result) {
                        echo "✅ Data berhasil disimpan ke database\n";
                    } else {
                        echo "❌ Gagal menyimpan data ke database\n";
                    }
                    
                } else {
                    echo "❌ Response error dari PCare\n";
                    $errorMessage = $responseData['metaData']['message'] ?? 'Unknown error';
                    echo "Error: {$errorMessage}\n";
                }
                
            } catch (\Exception $e) {
                echo "❌ Error saat kirim ke PCare: " . $e->getMessage() . "\n";
                echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ Error umum: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
        }
    }
    
    private function getKdPoliPcare($kdPoli)
    {
        $mapping = DB::table('maping_poliklinik_pcare')
            ->where('kd_poli_rs', $kdPoli)
            ->first();

        if ($mapping && !empty($mapping->kd_poli_pcare)) {
            return $mapping->kd_poli_pcare;
        }

        return '001';
    }
}

try {
    // Ambil data test
    $testData = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->whereNotNull('pasien.no_peserta')
        ->where('pasien.no_peserta', '!=', '')
        ->where('reg_periksa.tgl_registrasi', '>=', date('Y-m-d', strtotime('-7 days')))
        ->select('reg_periksa.no_rawat')
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->first();
    
    if (!$testData) {
        echo "❌ Tidak ada data test yang tersedia\n";
        exit(1);
    }
    
    $encodedNoRawat = base64_encode($testData->no_rawat);
    echo "🎯 Testing dengan no_rawat: {$testData->no_rawat} (encoded: {$encodedNoRawat})\n";
    
    $debugger = new DebugKunjunganPcare();
    $debugger->debugKunjunganProcess($encodedNoRawat);
    
} catch (\Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== DEBUG SELESAI ===\n";