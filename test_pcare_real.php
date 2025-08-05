<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Livewire\Ralan\Pemeriksaan;

$no_rawat = '2025/08/05/000005';

echo "=== TESTING PCARE KUNJUNGAN (REAL METHOD) ===\n";
echo "No Rawat: {$no_rawat}\n\n";

try {
    echo "1. Hapus data lama jika ada...\n";
    
    // Hapus data lama dari pcare_kunjungan_umum
    $deleted = DB::table('pcare_kunjungan_umum')->where('no_rawat', $no_rawat)->delete();
    echo "✓ Data lama dihapus ({$deleted} record)\n";
    
    echo "\n2. Membuat instance Pemeriksaan component...\n";
    
    // Buat instance dari class Pemeriksaan
    $pemeriksaan = new Pemeriksaan();
    
    // Set property noRawat (base64 encoded)
    $pemeriksaan->noRawat = base64_encode($no_rawat);
    
    echo "✓ Instance Pemeriksaan dibuat\n";
    echo "✓ noRawat diset: " . $pemeriksaan->noRawat . "\n";
    
    echo "\n3. Menjalankan method kunjunganPcare()...\n";
    
    // Capture output dan error
    ob_start();
    $error = null;
    
    try {
        // Panggil method kunjunganPcare
        $result = $pemeriksaan->kunjunganPcare();
        $output = ob_get_clean();
        
        echo "✓ Method kunjunganPcare() berhasil dijalankan\n";
        
        if ($output) {
            echo "Output dari method:\n";
            echo $output . "\n";
        }
        
    } catch (Exception $e) {
        $output = ob_get_clean();
        $error = $e;
        echo "✗ Error saat menjalankan kunjunganPcare(): " . $e->getMessage() . "\n";
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
    }
    
    echo "\n4. Mengecek hasil di database...\n";
    
    // Cek hasil di database
    $result = DB::table('pcare_kunjungan_umum')->where('no_rawat', $no_rawat)->get();
    
    if ($result->count() > 0) {
        echo "✓ Data berhasil tersimpan ({$result->count()} record):\n";
        foreach ($result as $index => $record) {
            echo "  Record " . ($index + 1) . ":\n";
            echo "    No Kunjungan: {$record->noKunjungan}\n";
            echo "    Status: {$record->status}\n";
            echo "    Tanggal: {$record->tglDaftar}\n";
            echo "    Pasien: {$record->nm_pasien}\n";
            echo "    No Kartu: {$record->noKartu}\n";
            echo "    Poli: {$record->kdPoli} - {$record->nmPoli}\n";
            echo "    Keluhan: {$record->keluhan}\n";
            echo "    Diagnosa: {$record->kdDiag1} - {$record->nmDiag1}\n";
            echo "    Dokter: {$record->kdDokter} - {$record->nmDokter}\n";
            echo "    Vital Signs:\n";
            echo "      - Sistole/Diastole: {$record->sistole}/{$record->diastole}\n";
            echo "      - Berat/Tinggi: {$record->beratBadan}kg / {$record->tinggiBadan}cm\n";
            echo "      - Heart Rate: {$record->heartRate}\n";
            echo "      - Resp Rate: {$record->respRate}\n";
            echo "      - Lingkar Perut: {$record->lingkarPerut}\n";
            echo "    Terapi: {$record->terapi}\n";
            echo "    Terapi Non Obat: {$record->terapi_non_obat}\n";
            echo "    BMHP: {$record->bmhp}\n";
            echo "\n";
        }
    } else {
        echo "✗ Tidak ada data yang tersimpan\n";
        
        // Cek log untuk error
        echo "\n5. Mengecek log Laravel...\n";
        $logFile = storage_path('logs/laravel-' . date('Y-m-d') . '.log');
        
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $lines = explode("\n", $logContent);
            $recentLines = array_slice($lines, -20); // 20 baris terakhir
            
            echo "Log terakhir (20 baris):\n";
            foreach ($recentLines as $line) {
                if (stripos($line, 'pcare') !== false || stripos($line, 'kunjungan') !== false || stripos($line, 'error') !== false) {
                    echo "  {$line}\n";
                }
            }
        } else {
            echo "✗ File log tidak ditemukan\n";
        }
    }
    
    echo "\n6. Mengecek data terkait lainnya...\n";
    
    // Cek data pendaftaran PCare
    $pendaftaran = DB::table('pcare_pendaftaran')->where('no_rawat', $no_rawat)->first();
    if ($pendaftaran) {
        echo "✓ Data pendaftaran PCare ditemukan:\n";
        echo "  - No Urut: {$pendaftaran->noUrut}\n";
        echo "  - Status: {$pendaftaran->status}\n";
    } else {
        echo "✗ Data pendaftaran PCare tidak ditemukan\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error umum: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== SELESAI TESTING ===\n";