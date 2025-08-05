<?php

require_once 'vendor/autoload.php';

// Load environment
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING PCARE_KUNJUNGAN_UMUM TABLE ===\n";

try {
    // Check recent records
    $records = DB::table('pcare_kunjungan_umum')
        ->orderBy('no_rawat', 'desc')
        ->limit(5)
        ->get(['no_rawat', 'noKunjungan']);
    
    echo "\nRecent records in pcare_kunjungan_umum:\n";
    echo "Total records found: " . $records->count() . "\n\n";
    
    foreach ($records as $record) {
        echo "No Rawat: {$record->no_rawat}\n";
        echo "No Kunjungan: " . ($record->noKunjungan ?? 'NULL') . "\n";
        echo "---\n";
    }
    
    // Check specifically for today's records
    $today = date('Y/m/d');
    $todayRecords = DB::table('pcare_kunjungan_umum')
        ->where('no_rawat', 'like', $today . '%')
        ->get(['no_rawat', 'noKunjungan']);
    
    echo "\nToday's records ({$today}):\n";
    echo "Total today's records: " . $todayRecords->count() . "\n\n";
    
    foreach ($todayRecords as $record) {
        echo "No Rawat: {$record->no_rawat}\n";
        echo "No Kunjungan: " . ($record->noKunjungan ?? 'NULL') . "\n";
        echo "---\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

echo "\n=== CHECK COMPLETED ===\n";