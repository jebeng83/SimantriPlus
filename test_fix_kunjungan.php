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
use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestKunjunganFix
{
    use PcareTrait;
    
    public function testKunjunganAPI()
    {
        echo "\n=== TEST KUNJUNGAN PCARE API (AFTER NULL FIX) ===\n";
        
        // Data kunjungan dengan optional fields omitted when not available
        $kunjunganData = [
            'noKartu' => '0002062926922',
            'tglDaftar' => '05-08-2025',
            'kdPoli' => '003',
            'keluhan' => 'Kontrol rutin',
            'kdSadar' => '04',
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 65.0,
            'tinggiBadan' => 170.0,
            'respRate' => 20,
            'heartRate' => 80,
            'lingkarPerut' => 0.0,
            'kdStatusPulang' => '3',
            'tglPulang' => '05-08-2025',
            'kdDokter' => '131491',
            'kdDiag1' => 'Z00.0',
            // kdDiag2 and kdDiag3 omitted when not available
            'kdTacc' => -1,
            'anamnesa' => 'Kontrol rutin',
            'alergiMakan' => '00',
            'alergiUdara' => '00',
            'alergiObat' => '00',
            'kdPrognosa' => '01',
            'terapiObat' => 'Tidak Ada',
            'terapiNonObat' => 'Edukasi Kesehatan',
            'bmhp' => 'Tidak Ada',
            'suhu' => '36.5'
        ];
        
        echo "\nData yang akan dikirim ke PCare:\n";
        echo json_encode($kunjunganData, JSON_PRETTY_PRINT) . "\n";
        
        try {
            // Test kunjungan API call
            $response = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');
            
            echo "\n=== RESPONSE FROM PCARE ===\n";
            echo "Status Code: " . ($response['status_code'] ?? 'Unknown') . "\n";
            echo "Response: " . json_encode($response, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($response['status_code'])) {
                if ($response['status_code'] == 201) {
                    echo "\n✅ SUCCESS: Kunjungan berhasil dikirim ke PCare!\n";
                } elseif ($response['status_code'] == 500) {
                    echo "\n❌ ERROR 500: Masih ada masalah dengan format data\n";
                    if (isset($response['response']['message'])) {
                        echo "Error Message: " . $response['response']['message'] . "\n";
                    }
                } else {
                    echo "\n⚠️  UNEXPECTED RESPONSE: Status " . $response['status_code'] . "\n";
                }
            }
            
        } catch (Exception $e) {
            echo "\n❌ EXCEPTION: " . $e->getMessage() . "\n";
        }
    }
}

// Run the test
$test = new TestKunjunganFix();
$test->testKunjunganAPI();

echo "\n=== TEST COMPLETED ===\n";