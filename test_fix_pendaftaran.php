<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Traits\PcareTrait;
use Illuminate\Support\Facades\Log;

class TestFixPendaftaran {
    use PcareTrait;
    
    public function testPendaftaranWithoutProblematicFields() {
        echo "=== TESTING PENDAFTARAN WITHOUT PROBLEMATIC FIELDS ===\n";
        
        // Data pendaftaran dengan semua field yang diperlukan
        $pcareData = [
            'kdProviderPeserta' => env('BPJS_PCARE_KODE_PPK', '11251616'),
            'tglDaftar' => date('d-m-Y'),
            'noKartu' => '0001441910575', // Test card number
            'kdPoli' => '003', // Test poli code
            'keluhan' => 'Test pendaftaran dengan field lengkap',
            'kunjSakit' => true,
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 65.0,
            'tinggiBadan' => 170.0,
            'respRate' => 20,
            'lingkarPerut' => 75.0,
            'heartRate' => 80,
            'rujukBalik' => 0,
            'kdTkp' => '10'
        ];
        
        echo "📤 Data yang akan dikirim:\n";
        echo json_encode($pcareData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        try {
            // Test API call
            $response = $this->requestPcare('pendaftaran', 'POST', $pcareData, 'text/plain');
            
            echo "📥 Response dari PCare API:\n";
            echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
            
            if (isset($response['metaData']['code'])) {
                $statusCode = $response['metaData']['code'];
                $message = $response['metaData']['message'] ?? 'No message';
                
                echo "Status Code: {$statusCode}\n";
                echo "Message: {$message}\n";
                
                if ($statusCode == 201) {
                    echo "✅ PENDAFTARAN BERHASIL!\n";
                } elseif ($statusCode == 200) {
                    echo "ℹ️  PENDAFTARAN SUDAH ADA (200)\n";
                } else {
                    echo "❌ PENDAFTARAN GAGAL\n";
                }
            } else {
                echo "❌ Response tidak valid\n";
            }
            
        } catch (\Exception $e) {
            echo "❌ ERROR: " . $e->getMessage() . "\n";
            echo "Stack trace: " . $e->getTraceAsString() . "\n";
        }
    }
}

$test = new TestFixPendaftaran();
$test->testPendaftaranWithoutProblematicFields();