<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestKunjunganBpjsFormat
{
    use PcareTrait;
    
    public function testKunjungan($noRawat)
    {
        echo "=== TEST KUNJUNGAN PCARE - FORMAT SESUAI BPJS ===\n\n";
        echo "No. Rawat: {$noRawat}\n\n";
        
        try {
            // 1. Ambil data registrasi dengan mapping lengkap
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('maping_dokter_pcare', 'reg_periksa.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
                ->leftJoin('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.*',
                    'pasien.no_peserta',
                    'pasien.nm_pasien',
                    'maping_dokter_pcare.kd_dokter_pcare',
                    'poliklinik.nm_poli'
                )
                ->first();
            
            if (!$dataPasien) {
                echo "❌ Data registrasi tidak ditemukan\n";
                return;
            }
            
            echo "✅ Data Registrasi:\n";
            echo "   - Pasien: {$dataPasien->nm_pasien}\n";
            echo "   - No. Peserta: {$dataPasien->no_peserta}\n";
            echo "   - Poli: {$dataPasien->nm_poli} ({$dataPasien->kd_poli})\n";
            echo "   - Dokter PCare: " . ($dataPasien->kd_dokter_pcare ?? 'NULL') . "\n";
            echo "   - Tanggal: {$dataPasien->tgl_registrasi}\n\n";
            
            // 2. Cek mapping poli PCare
            $mappingPoli = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $dataPasien->kd_poli)
                ->first();
            
            $kdPoliPcare = $mappingPoli ? $mappingPoli->kd_poli_pcare : '001';
            $nmPoliPcare = $mappingPoli ? $mappingPoli->nm_poli_pcare : 'POLI UMUM';
            
            echo "✅ Mapping Poli PCare:\n";
            echo "   - Kode RS: {$dataPasien->kd_poli}\n";
            echo "   - Kode PCare: {$kdPoliPcare}\n";
            echo "   - Nama PCare: {$nmPoliPcare}\n\n";
            
            // 3. Ambil data pemeriksaan
            $pemeriksaanData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $noRawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->first();
            
            echo "📋 Data Pemeriksaan:\n";
            if ($pemeriksaanData) {
                echo "   - Keluhan: {$pemeriksaanData->keluhan}\n";
                echo "   - Tensi: {$pemeriksaanData->tensi}\n";
                echo "   - Suhu: {$pemeriksaanData->suhu_tubuh}\n";
                echo "   - Nadi: {$pemeriksaanData->nadi}\n";
                echo "   - Respirasi: {$pemeriksaanData->respirasi}\n";
                echo "   - Berat: {$pemeriksaanData->berat}\n";
                echo "   - Tinggi: {$pemeriksaanData->tinggi}\n";
            } else {
                echo "   - Menggunakan data default\n";
            }
            echo "\n";
            
            // 4. Ambil data diagnosa
            $diagnosaData = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $noRawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
            
            // Ambil diagnosa sekunder
            $diagnosaData2 = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $noRawat)
                ->where('diagnosa_pasien.prioritas', '2')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
                
            $diagnosaData3 = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $noRawat)
                ->where('diagnosa_pasien.prioritas', '3')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();
            
            echo "🩺 Data Diagnosa:\n";
            if ($diagnosaData) {
                echo "   - Diagnosa 1: {$diagnosaData->kd_penyakit} - {$diagnosaData->nm_penyakit}\n";
            } else {
                echo "   - Diagnosa 1: Z00.0 (default)\n";
            }
            if ($diagnosaData2) {
                echo "   - Diagnosa 2: {$diagnosaData2->kd_penyakit} - {$diagnosaData2->nm_penyakit}\n";
            }
            if ($diagnosaData3) {
                echo "   - Diagnosa 3: {$diagnosaData3->kd_penyakit} - {$diagnosaData3->nm_penyakit}\n";
            }
            echo "\n";
            
            // 5. Persiapkan data vital signs
            $sistole = 120;
            $diastole = 80;
            if ($pemeriksaanData && !empty($pemeriksaanData->tensi) && strpos($pemeriksaanData->tensi, '/') !== false) {
                $tensiParts = explode('/', $pemeriksaanData->tensi);
                $sistole = (int)trim($tensiParts[0]) ?: 120;
                $diastole = (int)trim($tensiParts[1]) ?: 80;
            }
            
            // 6. Ambil data resep obat
            $terapiObatData = DB::table('resep_obat')
                ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_obat.no_rawat', $noRawat)
                ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                ->get();
            
            $terapiObatString = 'Tidak Ada';
            if ($terapiObatData->isNotEmpty()) {
                $terapiObatArray = [];
                foreach ($terapiObatData as $obat) {
                    $terapiObatArray[] = $obat->nama_brng . ' ' . $obat->jml . ' [' . $obat->aturan_pakai . ']';
                }
                $terapiObatString = implode(', ', $terapiObatArray);
            }
            
            echo "💊 Data Terapi Obat:\n";
            echo "   - {$terapiObatString}\n\n";
            
            // 7. Persiapkan data kunjungan sesuai format Java BPJS
            // Format sesuai dengan kode Java yang diberikan
            $kunjunganData = [
                'noKunjungan' => null, // Sesuai Java: null
                'noKartu' => (string)$dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => (string)$kdPoliPcare,
                'keluhan' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'),
                'kdSadar' => '04', // Compos Mentis
                'sistole' => (int)$sistole,
                'diastole' => (int)$diastole,
                'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
                'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 170),
                'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
                'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
                'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 0),
                'kdStatusPulang' => '3', // Sesuai Java: "3"
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => (string)($dataPasien->kd_dokter_pcare ?? ''),
                'kdDiag1' => (string)($diagnosaData->kd_penyakit ?? 'Z00.0'),
                'kdDiag2' => $diagnosaData2 ? (string)$diagnosaData2->kd_penyakit : null, // Sesuai Java: bisa null
                'kdDiag3' => $diagnosaData3 ? (string)$diagnosaData3->kd_penyakit : null, // Sesuai Java: bisa null
                'kdPoliRujukInternal' => null, // Sesuai Java: null
                'rujukLanjut' => null, // Sesuai Java: null
                'kdTacc' => -1, // Sesuai Java: -1
                'alasanTacc' => null, // Sesuai Java: null
                'anamnesa' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'), // Sama dengan keluhan
                'alergiMakan' => '00',
                'alergiUdara' => '00',
                'alergiObat' => '00',
                'kdPrognosa' => '01', // Baik
                'terapiObat' => (string)$terapiObatString,
                'terapiNonObat' => (string)($pemeriksaanData->instruksi ?? 'Edukasi Kesehatan'),
                'bmhp' => 'Tidak Ada',
                'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
            ];
            
            echo "📤 Data Kunjungan yang Akan Dikirim (Format BPJS):\n";
            echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            // 8. Kirim request menggunakan PcareTrait
            echo "🚀 Mengirim Request ke PCare menggunakan PcareTrait...\n";
            
            Log::info('Testing PCare Kunjungan - BPJS Format', [
                'no_rawat' => $noRawat,
                'kunjungan_data' => $kunjunganData
            ]);
            
            $responseData = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');
            
            echo "📥 Response dari PCare:\n";
            echo json_encode($responseData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            // 9. Analisis response
            if (isset($responseData['metaData'])) {
                $metaData = $responseData['metaData'];
                $httpCode = $metaData['code'] ?? 'unknown';
                $message = $metaData['message'] ?? 'No message';
                
                echo "📊 Analisis Response:\n";
                echo "   - HTTP Code: {$httpCode}\n";
                echo "   - Message: {$message}\n";
                
                if ($httpCode == 200 || $httpCode == 201) {
                    echo "✅ SUKSES! Kunjungan PCare berhasil dikirim\n";
                    
                    // Cek format response sesuai yang diharapkan
                    if (isset($responseData['response'])) {
                        $response = $responseData['response'];
                        
                        // Format yang diharapkan: {\"field\": \"noKunjungan\", \"message\": \"0114U1630316Y000001\"}
                        if (isset($response['field']) && $response['field'] == 'noKunjungan') {
                            echo "   - No Kunjungan: {$response['message']}\n";
                            echo "✅ Format response sesuai dengan yang diharapkan BPJS!\n";
                        } elseif (isset($response['noKunjungan'])) {
                            echo "   - No Kunjungan: {$response['noKunjungan']}\n";
                        } elseif (isset($response['message'])) {
                            echo "   - No Kunjungan: {$response['message']}\n";
                        }
                    }
                } elseif ($httpCode == 412) {
                    echo "⚠️  PRECONDITION FAILED - Ada masalah dengan data:\n";
                    if (isset($responseData['response']) && is_array($responseData['response'])) {
                        foreach ($responseData['response'] as $error) {
                            echo "   - {$error['field']}: {$error['message']}\n";
                        }
                    }
                } elseif ($httpCode == 500) {
                    echo "❌ SERVER ERROR - Ada masalah dengan validasi data:\n";
                    if (isset($responseData['response']['message'])) {
                        echo "   - {$responseData['response']['message']}\n";
                    }
                    echo "\n🔧 FORMAT YANG DIGUNAKAN:\n";
                    echo "   - Menggunakan format sesuai kode Java BPJS\n";
                    echo "   - noKunjungan: null (sesuai Java)\n";
                    echo "   - kdStatusPulang: '3' (sesuai Java)\n";
                    echo "   - kdDiag2, kdDiag3: null jika tidak ada (sesuai Java)\n";
                    echo "   - kdPoliRujukInternal, rujukLanjut, alasanTacc: null (sesuai Java)\n";
                } else {
                    echo "❌ ERROR - HTTP {$httpCode}:\n";
                    echo "   - {$message}\n";
                }
            } else {
                echo "❌ Response tidak memiliki metaData yang valid\n";
            }
            
        } catch (Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
        
        echo "\n=== SELESAI ===\n";
    }
}

// Jalankan test
$test = new TestKunjunganBpjsFormat();
$test->testKunjungan('2025/08/05/000006');