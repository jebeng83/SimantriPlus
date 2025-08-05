<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Livewire\Ralan\Pemeriksaan;

echo "=== TEST PCARE KUNJUNGAN 004 - CORRECTED ===\n";
echo "Testing PCare kunjungan for no_rawat: 2025/08/05/000004\n\n";

// 1. Verify environment configuration
echo "1. Verifying environment configuration...\n";
$baseUrl = env('BPJS_PCARE_BASE_URL');
$consId = env('BPJS_PCARE_CONS_ID');
$userKey = env('BPJS_PCARE_USER_KEY');
$user = env('BPJS_PCARE_USER');
$kodePpk = env('BPJS_PCARE_KODE_PPK');
$appCode = env('BPJS_PCARE_APP_CODE');

echo "✓ Base URL: {$baseUrl}\n";
echo "✓ Cons ID: {$consId}\n";
echo "✓ User Key: {$userKey}\n";
echo "✓ User: {$user}\n";
echo "✓ Kode PPK: {$kodePpk}\n";
echo "✓ App Code: {$appCode}\n\n";

// 2. Check patient data
echo "2. Checking patient data for no_rawat: 2025/08/05/000004...\n";
$testData = DB::table('reg_periksa')
    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
    ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
    ->where('reg_periksa.no_rawat', '2025/08/05/000004')
    ->select(
        'reg_periksa.*',
        'pasien.nm_pasien',
        'pasien.no_peserta',
        'penjab.png_jawab'
    )
    ->first();

if (!$testData) {
    echo "❌ Patient data not found for no_rawat: 2025/08/05/000004\n";
    exit(1);
}

echo "✓ Patient found: {$testData->nm_pasien}\n";
echo "✓ No Peserta: {$testData->no_peserta}\n";
echo "✓ Penjamin: {$testData->png_jawab}\n\n";

// 3. Encode no_rawat like Livewire does
echo "3. Preparing Livewire component...\n";
$encodedNoRawat = base64_encode($testData->no_rawat);
echo "✓ No rawat encoded: {$encodedNoRawat}\n";

// 4. Instantiate Livewire component with CORRECT property name
$pemeriksaanComponent = new Pemeriksaan();
$pemeriksaanComponent->noRawat = $encodedNoRawat;  // Use noRawat (camelCase)
$pemeriksaanComponent->noRm = $testData->no_rkm_medis;

echo "✓ Livewire component instantiated\n";
echo "✓ noRawat property set: {$pemeriksaanComponent->noRawat}\n";
echo "✓ noRm property set: {$pemeriksaanComponent->noRm}\n\n";

// 5. Test PCare kunjungan
echo "4. Testing PCare kunjungan...\n";
echo "Content-Type should be: text/plain\n";
echo "Expected API endpoint: {$baseUrl}/kunjungan\n\n";

try {
    $result = $pemeriksaanComponent->kunjunganPcare();
    echo "✓ PCare kunjungan method executed successfully\n";
    echo "\n=== CHECK LOGS FOR DETAILED RESULTS ===\n";
    echo "Look for:\n";
    echo "- Content-Type: text/plain (should be text/plain, not application/json)\n";
    echo "- API Response status and body\n";
    echo "- Any error messages\n\n";
    
    echo "Expected successful response format:\n";
    echo "{\n";
    echo "  \"response\": {\n";
    echo "    \"message\": \"12345\" // noKunjungan for HTTP 201\n";
    echo "  },\n";
    echo "  \"metaData\": {\n";
    echo "    \"code\": 201,\n";
    echo "    \"message\": \"OK\"\n";
    echo "  }\n";
    echo "}\n";
    
} catch (Exception $e) {
    echo "❌ Error during PCare kunjungan: " . $e->getMessage() . "\n";
    echo "Check Laravel logs for more details\n";
}

echo "\n=== TEST COMPLETED ===\n";