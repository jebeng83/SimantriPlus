<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== CHECKING POLI MAPPING FOR PCARE ===\n\n";

// Check mapping for U0019
echo "1. Checking mapping for poli U0019...\n";
$mapping = DB::table('maping_poliklinik_pcare')
    ->where('kd_poli_rs', 'U0019')
    ->first();

if ($mapping) {
    echo "   ✓ Mapping found:\n";
    echo "     - RS Code: {$mapping->kd_poli_rs}\n";
    echo "     - PCare Code: {$mapping->kd_poli_pcare}\n";
    echo "     - PCare Name: {$mapping->nm_poli_pcare}\n";
} else {
    echo "   ❌ No mapping found for U0019\n";
}

echo "\n2. Checking all available poli mappings...\n";
$allMappings = DB::table('maping_poliklinik_pcare')
    ->join('poliklinik', 'maping_poliklinik_pcare.kd_poli_rs', '=', 'poliklinik.kd_poli')
    ->select(
        'maping_poliklinik_pcare.kd_poli_rs',
        'maping_poliklinik_pcare.kd_poli_pcare', 
        'maping_poliklinik_pcare.nm_poli_pcare',
        'poliklinik.nm_poli as nm_poli_rs'
    )
    ->get();

if ($allMappings->count() > 0) {
    echo "   ✓ Available mappings:\n";
    foreach ($allMappings as $map) {
        echo "     - {$map->kd_poli_rs} ({$map->nm_poli_rs}) → {$map->kd_poli_pcare} ({$map->nm_poli_pcare})\n";
    }
} else {
    echo "   ❌ No poli mappings found in database\n";
}

echo "\n3. Checking poli U0019 details...\n";
$poliDetails = DB::table('poliklinik')
    ->where('kd_poli', 'U0019')
    ->first();

if ($poliDetails) {
    echo "   ✓ Poli details:\n";
    echo "     - Code: {$poliDetails->kd_poli}\n";
    echo "     - Name: {$poliDetails->nm_poli}\n";
    echo "     - Status: {$poliDetails->status}\n";
} else {
    echo "   ❌ Poli U0019 not found\n";
}

echo "\n4. Suggested solution...\n";
if (!$mapping) {
    echo "   📝 SOLUTION: Add mapping for U0019\n";
    echo "   \n";
    echo "   Recommended PCare codes:\n";
    echo "   - 001: Poli Umum\n";
    echo "   - 002: Poli Gigi\n";
    echo "   - 003: Poli KIA\n";
    echo "   \n";
    echo "   SQL to add mapping:\n";
    if ($poliDetails) {
        $suggestedCode = '001'; // Default to Poli Umum
        $suggestedName = 'Poli Umum';
        
        // Try to guess based on poli name
        $poliName = strtolower($poliDetails->nm_poli);
        if (strpos($poliName, 'gigi') !== false || strpos($poliName, 'dental') !== false) {
            $suggestedCode = '002';
            $suggestedName = 'Poli Gigi';
        } elseif (strpos($poliName, 'kia') !== false || strpos($poliName, 'anak') !== false || strpos($poliName, 'ibu') !== false) {
            $suggestedCode = '003';
            $suggestedName = 'Poli KIA';
        }
        
        echo "   INSERT INTO maping_poliklinik_pcare (kd_poli_rs, kd_poli_pcare, nm_poli_pcare) VALUES\n";
        echo "   ('U0019', '{$suggestedCode}', '{$suggestedName}');\n";
    }
} else {
    echo "   ✓ Mapping exists, but may need verification with PCare reference\n";
}

echo "\n" . str_repeat("=", 60) . "\n";