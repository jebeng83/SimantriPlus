<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$no_rawat = '2025/08/05/000005';

echo "=== CHECKING DATA FOR no_rawat: {$no_rawat} ===\n\n";

// 1. Check reg_periksa
echo "1. TABLE: reg_periksa\n";
echo "===================\n";
$reg_periksa = DB::table('reg_periksa')->where('no_rawat', $no_rawat)->first();
if ($reg_periksa) {
    echo "Data ditemukan:\n";
    foreach ($reg_periksa as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}
echo "\n";

// 2. Check pemeriksaan_ralan
echo "2. TABLE: pemeriksaan_ralan\n";
echo "========================\n";
$pemeriksaan_ralan = DB::table('pemeriksaan_ralan')->where('no_rawat', $no_rawat)->first();
if ($pemeriksaan_ralan) {
    echo "Data ditemukan:\n";
    foreach ($pemeriksaan_ralan as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}
echo "\n";

// 3. Check pcare_pendaftaran
echo "3. TABLE: pcare_pendaftaran\n";
echo "=========================\n";
$pcare_pendaftaran = DB::table('pcare_pendaftaran')->where('no_rawat', $no_rawat)->first();
if ($pcare_pendaftaran) {
    echo "Data ditemukan:\n";
    foreach ($pcare_pendaftaran as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}
echo "\n";

// 4. Check resep_obat join resep_dokter
echo "4. TABLE: resep_obat JOIN resep_dokter\n";
echo "===================================\n";
$resep_data = DB::table('resep_obat')
    ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
    ->where('resep_obat.no_rawat', $no_rawat)
    ->get();
if ($resep_data->count() > 0) {
    echo "Data ditemukan ({$resep_data->count()} records):\n";
    foreach ($resep_data as $index => $resep) {
        echo "  Record " . ($index + 1) . ":\n";
        foreach ($resep as $key => $value) {
            echo "    {$key}: {$value}\n";
        }
        echo "\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}
echo "\n";

// 5. Check diagnosa_pasien
echo "5. TABLE: diagnosa_pasien\n";
echo "=======================\n";
$diagnosa_pasien = DB::table('diagnosa_pasien')->where('no_rawat', $no_rawat)->get();
if ($diagnosa_pasien->count() > 0) {
    echo "Data ditemukan ({$diagnosa_pasien->count()} records):\n";
    foreach ($diagnosa_pasien as $index => $diagnosa) {
        echo "  Record " . ($index + 1) . ":\n";
        foreach ($diagnosa as $key => $value) {
            echo "    {$key}: {$value}\n";
        }
        echo "\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}
echo "\n";

// 6. Check pcare_kunjungan_umum (untuk melihat status PCare)
echo "6. TABLE: pcare_kunjungan_umum\n";
echo "============================\n";
$pcare_kunjungan = DB::table('pcare_kunjungan_umum')->where('no_rawat', $no_rawat)->get();
if ($pcare_kunjungan->count() > 0) {
    echo "Data ditemukan ({$pcare_kunjungan->count()} records):\n";
    foreach ($pcare_kunjungan as $index => $kunjungan) {
        echo "  Record " . ($index + 1) . ":\n";
        foreach ($kunjungan as $key => $value) {
            echo "    {$key}: {$value}\n";
        }
        echo "\n";
    }
} else {
    echo "Data tidak ditemukan\n";
}

echo "=== SELESAI CHECKING DATA ===\n";