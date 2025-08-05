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

echo "=== TEST PCARE KUNJUNGAN CONTROLLER UPDATED ===\n\n";

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
    
    // 2. Test controller method
    echo "2. Testing PcareKunjunganController...\n";
    $controller = new PcareKunjunganController();
    
    // Create mock request
    $request = new Request();
    $request->merge([
        'alasan' => 'Test kirim ulang kunjungan dengan format BPJS yang benar'
    ]);
    
    echo "3. Mengirim kunjungan ke PCare API...\n";
    $response = $controller->kirimUlang($request, $dataPasien->no_rawat);
    
    echo "4. Response dari controller:\n";
    $responseData = $response->getData(true);
    echo json_encode($responseData, JSON_PRETTY_PRINT) . "\n\n";
    
    if ($responseData['success']) {
        echo "✅ SUKSES: Kunjungan berhasil dikirim ke PCare\n";
        echo "   No Kunjungan: " . ($responseData['noKunjungan'] ?? 'NULL') . "\n";
        echo "   Message: " . ($responseData['message'] ?? 'NULL') . "\n";
        
        // Check database update
        echo "\n5. Checking database update...\n";
        $updatedRecord = DB::table('reg_periksa')
            ->where('no_rawat', $dataPasien->no_rawat)
            ->first();
        
        if ($updatedRecord && isset($updatedRecord->status_pcare)) {
            echo "✓ Status PCare di database: {$updatedRecord->status_pcare}\n";
        } else {
            echo "⚠️  Status PCare tidak ditemukan di database\n";
        }
    } else {
        echo "❌ GAGAL: " . ($responseData['message'] ?? 'Unknown error') . "\n";
        if (isset($responseData['data'])) {
            echo "   Detail error: " . json_encode($responseData['data'], JSON_PRETTY_PRINT) . "\n";
        }
    }
    
} catch (\Exception $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo "   File: " . $e->getFile() . "\n";
    echo "   Line: " . $e->getLine() . "\n";
    echo "   Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== TEST SELESAI ===\n";