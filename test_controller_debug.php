<?php

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel application
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\PcareKunjunganController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

echo "=== DEBUG PCARE KUNJUNGAN CONTROLLER ===\n\n";

try {
    // 1. Cari data pasien yang ada
    echo "1. Mencari data pasien untuk testing...\n";
    $dataPasien = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->leftJoin('maping_dokter_pcare', 'dokter.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
        ->where('reg_periksa.status_lanjut', 'Ralan')
        ->whereNotNull('pasien.no_peserta')
        ->where('pasien.no_peserta', '!=', '')
        ->whereNotNull('maping_dokter_pcare.kd_dokter_pcare')
        ->select(
            'reg_periksa.*',
            'pasien.nm_pasien',
            'pasien.no_peserta',
            'poliklinik.nm_poli',
            'dokter.nm_dokter',
            'maping_dokter_pcare.kd_dokter_pcare'
        )
        ->orderBy('reg_periksa.tgl_registrasi', 'desc')
        ->first();
    
    if (!$dataPasien) {
        echo "❌ Tidak ada data pasien yang memenuhi kriteria\n";
        exit(1);
    }
    
    echo "✓ Data pasien ditemukan:\n";
    echo "   No Rawat: {$dataPasien->no_rawat}\n";
    echo "   Pasien: {$dataPasien->nm_pasien}\n";
    echo "   No Peserta: {$dataPasien->no_peserta}\n";
    echo "   Poli: {$dataPasien->nm_poli}\n";
    echo "   Dokter: {$dataPasien->nm_dokter}\n";
    echo "   Kd Dokter PCare: {$dataPasien->kd_dokter_pcare}\n\n";
    
    // 2. Test controller method step by step
    echo "2. Testing PcareKunjunganController step by step...\n";
    $controller = new PcareKunjunganController();
    
    // Test getKunjunganData method
    echo "3. Testing getKunjunganData method...\n";
    $reflection = new ReflectionClass($controller);
    $getKunjunganDataMethod = $reflection->getMethod('getKunjunganData');
    $getKunjunganDataMethod->setAccessible(true);
    
    $kunjunganData = $getKunjunganDataMethod->invoke($controller, $dataPasien->no_rawat);
    
    if ($kunjunganData) {
        echo "✓ getKunjunganData berhasil\n";
        echo "   No Peserta: " . ($kunjunganData->no_peserta ?? 'NULL') . "\n";
        echo "   Kd Dokter PCare: " . ($kunjunganData->kd_dokter_pcare ?? 'NULL') . "\n";
    } else {
        echo "❌ getKunjunganData gagal\n";
        exit(1);
    }
    
    // Test preparePcareKunjunganData method
    echo "\n4. Testing preparePcareKunjunganData method...\n";
    $preparePcareKunjunganDataMethod = $reflection->getMethod('preparePcareKunjunganData');
    $preparePcareKunjunganDataMethod->setAccessible(true);
    
    $pcareData = $preparePcareKunjunganDataMethod->invoke($controller, $kunjunganData);
    
    echo "✓ preparePcareKunjunganData berhasil\n";
    echo "   Data yang akan dikirim:\n";
    echo json_encode($pcareData, JSON_PRETTY_PRINT) . "\n\n";
    
    // Test sendKunjunganToPcare method
    echo "5. Testing sendKunjunganToPcare method...\n";
    $sendKunjunganToPcareMethod = $reflection->getMethod('sendKunjunganToPcare');
    $sendKunjunganToPcareMethod->setAccessible(true);
    
    $response = $sendKunjunganToPcareMethod->invoke($controller, $pcareData);
    
    echo "Response dari sendKunjunganToPcare:\n";
    echo json_encode($response, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($response && isset($response['success']) && $response['success']) {
        echo "✅ SUKSES: Kunjungan berhasil dikirim ke PCare\n";
        echo "   No Kunjungan: " . ($response['noKunjungan'] ?? 'NULL') . "\n";
        echo "   Message: " . ($response['message'] ?? 'NULL') . "\n";
    } else {
        echo "❌ GAGAL: " . ($response['message'] ?? 'Unknown error') . "\n";
        if (isset($response['data'])) {
            echo "   Detail error: " . json_encode($response['data'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== DEBUG SELESAI ===\n";