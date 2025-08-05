<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Livewire\Ralan\Pemeriksaan;

echo "\n=== TEST KUNJUNGAN PCARE SETELAH PERBAIKAN ===\n";

try {
    // 1. Cek data yang akan digunakan untuk testing
    echo "\n1. Mencari data pasien BPJS untuk testing...\n";
    
    $testData = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->whereNotNull('pasien.no_peserta')
        ->where('pasien.no_peserta', '!=', '')
        ->where('reg_periksa.tgl_registrasi', '>=', date('Y-m-d', strtotime('-7 days')))
        ->select(
            'reg_periksa.no_rawat',
            'reg_periksa.no_rkm_medis',
            'reg_periksa.tgl_registrasi',
            'pasien.nm_pasien',
            'pasien.no_peserta',
            'poliklinik.nm_poli',
            'dokter.nm_dokter'
        )
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->first();
    
    if (!$testData) {
        echo "❌ Tidak ada data pasien BPJS dalam 7 hari terakhir\n";
        exit(1);
    }
    
    echo "✓ Data test ditemukan:\n";
    echo "   - No Rawat: {$testData->no_rawat}\n";
    echo "   - Pasien: {$testData->nm_pasien}\n";
    echo "   - No Peserta: {$testData->no_peserta}\n";
    echo "   - Poli: {$testData->nm_poli}\n";
    echo "   - Dokter: {$testData->nm_dokter}\n";
    echo "   - Tanggal: {$testData->tgl_registrasi}\n";
    
    // 2. Hapus data kunjungan PCare yang mungkin sudah ada
    echo "\n2. Membersihkan data kunjungan PCare lama...\n";
    $deleted = DB::table('pcare_kunjungan_umum')
        ->where('no_rawat', $testData->no_rawat)
        ->delete();
    echo "✓ Dihapus {$deleted} record lama\n";
    
    // 3. Pastikan ada data pemeriksaan
    echo "\n3. Memastikan data pemeriksaan tersedia...\n";
    $pemeriksaan = DB::table('pemeriksaan_ralan')
        ->where('no_rawat', $testData->no_rawat)
        ->first();
    
    if (!$pemeriksaan) {
        echo "⚠️  Data pemeriksaan tidak ada, membuat data dummy...\n";
        DB::table('pemeriksaan_ralan')->insert([
            'no_rawat' => $testData->no_rawat,
            'tgl_perawatan' => $testData->tgl_registrasi,
            'jam_rawat' => '08:00:00',
            'suhu_tubuh' => '36.5',
            'tensi' => '120/80',
            'nadi' => '80',
            'respirasi' => '20',
            'tinggi' => '170',
            'berat' => '70',
            'spo2' => '98',
            'gcs' => '15',
            'kesadaran' => 'Compos Mentis',
            'keluhan' => 'Kontrol rutin',
            'pemeriksaan' => 'Pemeriksaan fisik dalam batas normal',
            'rtl' => 'Kontrol rutin',
            'penilaian' => 'Kondisi stabil',
            'instruksi' => 'Kontrol 1 minggu',
            'evaluasi' => 'Baik',
            'nip' => '12345',
            'lingkar_perut' => '80'
        ]);
        echo "✓ Data pemeriksaan dummy berhasil dibuat\n";
    } else {
        echo "✓ Data pemeriksaan sudah tersedia\n";
    }
    
    // 4. Pastikan ada data diagnosa
    echo "\n4. Memastikan data diagnosa tersedia...\n";
    $diagnosa = DB::table('diagnosa_pasien')
        ->where('no_rawat', $testData->no_rawat)
        ->where('prioritas', '1')
        ->first();
    
    if (!$diagnosa) {
        echo "⚠️  Data diagnosa tidak ada, membuat data dummy...\n";
        DB::table('diagnosa_pasien')->insert([
            'no_rawat' => $testData->no_rawat,
            'kd_penyakit' => 'Z00.0',
            'prioritas' => '1',
            'status' => 'Ralan'
        ]);
        echo "✓ Data diagnosa dummy berhasil dibuat\n";
    } else {
        echo "✓ Data diagnosa sudah tersedia\n";
    }
    
    // 5. Encode no_rawat seperti yang dilakukan Livewire
    echo "\n5. Menyiapkan komponen Livewire...\n";
    $encodedNoRawat = base64_encode($testData->no_rawat);
    echo "✓ No rawat encoded: {$encodedNoRawat}\n";
    
    // 6. Instantiate Livewire component
    $pemeriksaanComponent = new Pemeriksaan();
    $pemeriksaanComponent->noRawat = $encodedNoRawat;
    
    echo "✓ Komponen Livewire berhasil dibuat\n";
    
    // 7. Test method kunjunganPcare
    echo "\n6. Menjalankan method kunjunganPcare()...\n";
    echo "📤 Mengirim data kunjungan ke PCare...\n";
    
    // Capture output dan log
    ob_start();
    $pemeriksaanComponent->kunjunganPcare();
    $output = ob_get_clean();
    
    if (!empty($output)) {
        echo "Output dari method: {$output}\n";
    }
    
    // 8. Cek hasil di database
    echo "\n7. Mengecek hasil di database...\n";
    $result = DB::table('pcare_kunjungan_umum')
        ->where('no_rawat', $testData->no_rawat)
        ->first();
    
    if ($result) {
        echo "✅ Data kunjungan PCare berhasil disimpan!\n";
        echo "📋 Detail data yang disimpan:\n";
        echo "   - No Kunjungan: " . ($result->noKunjungan ?? 'NULL') . "\n";
        echo "   - Status: {$result->status}\n";
        echo "   - Tanggal: {$result->tglDaftar}\n";
        echo "   - Pasien: {$result->nm_pasien}\n";
        echo "   - No Kartu: {$result->noKartu}\n";
        echo "   - Poli: {$result->nmPoli} (Kode: {$result->kdPoli})\n";
        echo "   - Dokter: {$result->nmDokter} (Kode: {$result->kdDokter})\n";
        echo "   - Diagnosa: {$result->nmDiag1} (Kode: {$result->kdDiag1})\n";
        echo "   - Keluhan: {$result->keluhan}\n";
        echo "   - Vital Signs:\n";
        echo "     * Sistole/Diastole: {$result->sistole}/{$result->diastole}\n";
        echo "     * Berat/Tinggi: {$result->beratBadan}kg / {$result->tinggiBadan}cm\n";
        echo "     * Nadi: {$result->heartRate}x/menit\n";
        echo "     * Respirasi: {$result->respRate}x/menit\n";
        echo "     * Lingkar Perut: {$result->lingkarPerut}cm\n";
        echo "   - Kesadaran: {$result->nmSadar} (Kode: {$result->kdSadar})\n";
        echo "   - Status Pulang: {$result->nmStatusPulang} (Kode: {$result->kdStatusPulang})\n";
        echo "   - Prognosa: {$result->nmPrognosa} (Kode: {$result->kdPrognosa})\n";
        echo "   - Alergi:\n";
        echo "     * Makanan: {$result->nmAlergiMakanan}\n";
        echo "     * Udara: {$result->nmAlergiUdara}\n";
        echo "     * Obat: {$result->nmAlergiObat}\n";
        echo "   - Terapi Obat: {$result->terapi}\n";
        echo "   - Terapi Non-Obat: {$result->terapi_non_obat}\n";
        echo "   - BMHP: {$result->bmhp}\n";
        echo "   - Updated: {$result->updated_at}\n";
        
        if ($result->status === 'Terkirim') {
            echo "\n🎉 SUKSES! Kunjungan PCare berhasil dikirim dan data lengkap tersimpan!\n";
        } else {
            echo "\n⚠️  Data tersimpan tapi status: {$result->status}\n";
        }
    } else {
        echo "❌ Tidak ada data yang tersimpan di database\n";
        
        // Cek log Laravel untuk error
        echo "\n📋 Mengecek log Laravel...\n";
        $logFile = storage_path('logs/laravel.log');
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $recentLogs = array_slice(explode("\n", $logContent), -20);
            echo "Log terakhir:\n";
            foreach ($recentLogs as $line) {
                if (!empty(trim($line))) {
                    echo "   {$line}\n";
                }
            }
        }
    }
    
    echo "\n=== SELESAI ===\n";
    
} catch (\Exception $e) {
    echo "\n❌ Error saat testing:\n";
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "\nStack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}