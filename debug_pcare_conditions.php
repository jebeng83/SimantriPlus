<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Http\Livewire\Ralan\Pemeriksaan;

echo "=== DEBUG PCARE CONDITIONS ===\n";
echo "Checking all conditions for no_rawat: 2025/08/05/000004\n\n";

// 1. Check patient data with all joins
echo "1. Checking patient data with joins...\n";
$dataPasien = DB::table('reg_periksa')
    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
    ->leftJoin('maping_dokter_pcare', 'reg_periksa.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
    ->where('reg_periksa.no_rawat', '2025/08/05/000004')
    ->select(
        'reg_periksa.*',
        'pasien.no_peserta',
        'pasien.nm_pasien',
        'poliklinik.nm_poli',
        'dokter.nm_dokter',
        'dokter.kd_dokter',
        'maping_dokter_pcare.kd_dokter_pcare'
    )
    ->first();

if (!$dataPasien) {
    echo "❌ CONDITION 1 FAILED: Data pasien tidak ditemukan\n";
    exit(1);
} else {
    echo "✓ Patient found: {$dataPasien->nm_pasien}\n";
    echo "  - No Peserta: {$dataPasien->no_peserta}\n";
    echo "  - Dokter: {$dataPasien->nm_dokter} (kd: {$dataPasien->kd_dokter})\n";
    echo "  - Poli: {$dataPasien->nm_poli}\n";
    echo "  - PCare Dokter Mapping: " . ($dataPasien->kd_dokter_pcare ?? 'NULL') . "\n";
}

// 2. Check BPJS condition
echo "\n2. Checking BPJS condition...\n";
if (empty($dataPasien->no_peserta)) {
    echo "❌ CONDITION 2 FAILED: Pasien bukan peserta BPJS\n";
    exit(1);
} else {
    echo "✓ Patient is BPJS member: {$dataPasien->no_peserta}\n";
}

// 3. Check doctor PCare mapping
echo "\n3. Checking doctor PCare mapping...\n";
if (empty($dataPasien->kd_dokter_pcare)) {
    echo "❌ CONDITION 3 FAILED: Dokter belum dimapping ke PCare\n";
    echo "Available doctor mappings:\n";
    $mappings = DB::table('maping_dokter_pcare')
        ->join('dokter', 'maping_dokter_pcare.kd_dokter', '=', 'dokter.kd_dokter')
        ->select('dokter.kd_dokter', 'dokter.nm_dokter', 'maping_dokter_pcare.kd_dokter_pcare')
        ->get();
    
    if ($mappings->isEmpty()) {
        echo "  - No doctor mappings found in database\n";
    } else {
        foreach ($mappings as $mapping) {
            echo "  - {$mapping->nm_dokter} ({$mapping->kd_dokter}) -> PCare: {$mapping->kd_dokter_pcare}\n";
        }
    }
    
    echo "\nTo fix this, you need to add a mapping for doctor {$dataPasien->kd_dokter}\n";
    exit(1);
} else {
    echo "✓ Doctor has PCare mapping: {$dataPasien->kd_dokter_pcare}\n";
}

// 4. Check pemeriksaan data
echo "\n4. Checking pemeriksaan data...\n";
$pemeriksaanData = DB::table('pemeriksaan_ralan')
    ->where('no_rawat', '2025/08/05/000004')
    ->orderBy('tgl_perawatan', 'desc')
    ->orderBy('jam_rawat', 'desc')
    ->first();

if (!$pemeriksaanData) {
    echo "⚠️  No pemeriksaan data found (will use defaults)\n";
} else {
    echo "✓ Pemeriksaan data found:\n";
    echo "  - Keluhan: {$pemeriksaanData->keluhan}\n";
    echo "  - Tensi: {$pemeriksaanData->tensi}\n";
    echo "  - Suhu: {$pemeriksaanData->suhu_tubuh}\n";
}

// 5. Check diagnosa data
echo "\n5. Checking diagnosa data...\n";
$diagnosaData = DB::table('diagnosa_pasien')
    ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
    ->where('diagnosa_pasien.no_rawat', '2025/08/05/000004')
    ->where('diagnosa_pasien.prioritas', '1')
    ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
    ->first();

if (!$diagnosaData) {
    echo "⚠️  No diagnosa data found (will use default Z00.0)\n";
} else {
    echo "✓ Diagnosa data found: {$diagnosaData->kd_penyakit} - {$diagnosaData->nm_penyakit}\n";
}

echo "\n=== ALL CONDITIONS PASSED ===\n";
echo "The PCare kunjungan should proceed to API call.\n";
echo "If it's still not working, the issue might be in the API call itself.\n";