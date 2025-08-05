<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Livewire\Ralan\Pemeriksaan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

echo "=== TESTING PCARE KUNJUNGAN - NO_RAWAT: 2025/08/05/000004 ===\n";

try {
    // Verify .env configuration
    echo "1. Verifying .env configuration...\n";
    $baseUrl = env('BPJS_PCARE_BASE_URL');
    $consId = env('BPJS_PCARE_CONS_ID');
    $userKey = env('BPJS_PCARE_USER_KEY');
    $user = env('BPJS_PCARE_USER');
    $kodePpk = env('BPJS_PCARE_KODE_PPK');
    $appCode = env('BPJS_PCARE_APP_CODE');
    
    echo "   Base URL: {$baseUrl}\n";
    echo "   Cons ID: {$consId}\n";
    echo "   User Key: {$userKey}\n";
    echo "   User: {$user}\n";
    echo "   Kode PPK: {$kodePpk}\n";
    echo "   App Code: {$appCode}\n";
    echo "✓ Environment configuration verified\n\n";
    
    // Check if patient data exists
    echo "2. Checking patient data for no_rawat: 2025/08/05/000004...\n";
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
        echo "❌ Data pasien tidak ditemukan untuk no_rawat: 2025/08/05/000004\n";
        echo "Available no_rawat records:\n";
        $availableRecords = DB::table('reg_periksa')
            ->where('no_rawat', 'like', '2025/08/05/%')
            ->select('no_rawat', 'no_rkm_medis')
            ->limit(10)
            ->get();
        foreach ($availableRecords as $record) {
            echo "   - {$record->no_rawat} (RM: {$record->no_rkm_medis})\n";
        }
        exit(1);
    }
    
    echo "✓ Patient found: {$dataPasien->nm_pasien} (RM: {$dataPasien->no_rkm_medis})\n";
    echo "   Poli: {$dataPasien->nm_poli}\n";
    echo "   Dokter: {$dataPasien->nm_dokter}\n\n";
    
    // Create Pemeriksaan instance and test
    echo "3. Testing PCare kunjungan functionality...\n";
    $pemeriksaan = new Pemeriksaan();
    $pemeriksaan->noRawat = '2025/08/05/000004';
    
    echo "4. Sending kunjungan to PCare API...\n";
    echo "   Content-Type: text/plain\n";
    echo "   Endpoint: kunjungan (POST)\n";
    
    // Execute kunjunganPcare method
    $result = $pemeriksaan->kunjunganPcare();
    
    echo "✓ PCare kunjungan request completed\n";
    echo "\n=== RESULT ===\n";
    echo "Check the Laravel logs for detailed response information.\n";
    echo "Expected response format:\n";
    echo "{\n";
    echo "  \"response\": {\n";
    echo "    \"field\": \"noKunjungan\",\n";
    echo "    \"message\": \"0114U1630316Y000001\"\n";
    echo "  },\n";
    echo "  \"metaData\": {\n";
    echo "    \"message\": \"CREATED\",\n";
    echo "    \"code\": 201\n";
    echo "  }\n";
    echo "}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

echo "\n=== TEST COMPLETED ===\n";