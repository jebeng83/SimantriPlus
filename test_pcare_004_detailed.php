<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Livewire\Ralan\Pemeriksaan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "=== DETAILED PCARE TEST - NO_RAWAT: 2025/08/05/000004 ===\n";

try {
    // Clear any existing logs for this test
    Log::info('=== STARTING DETAILED PCARE TEST ===', [
        'no_rawat' => '2025/08/05/000004',
        'timestamp' => now()->toDateTimeString()
    ]);
    
    // Check patient data
    $dataPasien = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->where('reg_periksa.no_rawat', '2025/08/05/000004')
        ->select(
            'reg_periksa.*',
            'pasien.nm_pasien',
            'pasien.no_ktp',
            'pasien.tgl_lahir',
            'pasien.jk',
            'poliklinik.nm_poli',
            'dokter.nm_dokter'
        )
        ->first();
    
    if (!$dataPasien) {
        echo "❌ Patient data not found for no_rawat: 2025/08/05/000004\n";
        exit(1);
    }
    
    echo "✓ Patient: {$dataPasien->nm_pasien}\n";
    echo "✓ Poli: {$dataPasien->nm_poli}\n";
    echo "✓ Dokter: {$dataPasien->nm_dokter}\n";
    
    // Check if there's prescription data
    $resepData = DB::table('resep_obat')
        ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
        ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
        ->where('resep_obat.no_rawat', '2025/08/05/000004')
        ->select(
            'databarang.nama_brng',
            'resep_dokter.jml',
            'resep_dokter.aturan_pakai'
        )
        ->get();
    
    if ($resepData->count() > 0) {
        echo "✓ Found {$resepData->count()} prescription items:\n";
        foreach ($resepData as $obat) {
            echo "   - {$obat->nama_brng} {$obat->jml} [{$obat->aturan_pakai}]\n";
        }
    } else {
        echo "⚠️  No prescription data found - will use default 'Edukasi Kesehatan'\n";
    }
    
    // Create Pemeriksaan instance
    $pemeriksaan = new Pemeriksaan();
    $pemeriksaan->noRawat = '2025/08/05/000004';
    
    echo "\n=== EXECUTING PCARE KUNJUNGAN ===\n";
    
    // Log before execution
    Log::info('About to execute kunjunganPcare', [
        'no_rawat' => '2025/08/05/000004',
        'expected_content_type' => 'text/plain'
    ]);
    
    // Execute the method
    $result = $pemeriksaan->kunjunganPcare();
    
    // Log after execution
    Log::info('kunjunganPcare execution completed', [
        'no_rawat' => '2025/08/05/000004',
        'result' => $result
    ]);
    
    echo "✓ PCare kunjungan method executed\n";
    echo "\n=== CHECK LOGS FOR DETAILED RESPONSE ===\n";
    echo "Look for:\n";
    echo "1. 'PCare API Request' - should show contentType: text/plain\n";
    echo "2. 'PCare API Response' - should show the API response\n";
    echo "3. Response format should be JSON with metaData.code 200 or 201\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    Log::error('Test execution failed', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    exit(1);
}

echo "\n=== TEST COMPLETED ===\n";