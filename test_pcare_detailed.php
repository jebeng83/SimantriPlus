<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Traits\PcareTrait;

class TestPcareDetailed {
    use PcareTrait;
    
    public function testKunjunganWithDetailedLogging() {
        echo "=== DETAILED PCARE KUNJUNGAN TEST ===\n";
        
        // Data test yang sudah diperbaiki dengan field yang diperlukan
        $kunjunganData = [
            'noKunjungan' => null,
            'noKartu' => '0001441910575',
            'tglDaftar' => '05-08-2025',
            'kdPoli' => '003',
            'keluhan' => 'Pasien melakukan kontrol rutin.',
            'kunjSakit' => true, // Field yang ditambahkan
            'kdSadar' => '04',
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 45.0,
            'tinggiBadan' => 145.0,
            'respRate' => 20,
            'heartRate' => 80,
            'lingkarPerut' => 0.0,
            'rujukBalik' => 0, // Field yang ditambahkan
            'kdTkp' => '10', // Field yang ditambahkan
            'kdStatusPulang' => '4',
            'tglPulang' => '05-08-2025',
            'kdDokter' => 'DK001',
            'kdDiag1' => 'J00',
            'kdDiag2' => null,
            'kdDiag3' => null,
            'kdPoliRujukInternal' => null,
            'rujukLanjut' => null,
            'kdTacc' => -1,
            'alasanTacc' => null,
            'anamnesa' => 'Pemeriksaan rutin',
            'alergiMakan' => '00',
            'alergiUdara' => '00',
            'alergiObat' => '00',
            'kdPrognosa' => '01',
            'terapiObat' => 'Sesuai indikasi',
            'terapiNonObat' => 'Kontrol rutin',
            'bmhp' => 'Tidak ada',
            'suhu' => '36.5'
        ];
        
        echo "Data yang akan dikirim:\n";
        echo json_encode($kunjunganData, JSON_PRETTY_PRINT) . "\n\n";
        
        echo "Testing koneksi dasar...\n";
        $basicTest = $this->requestPcare('', 'GET', [], 'application/json');
        echo "Basic test response: " . json_encode($basicTest) . "\n\n";
        
        echo "Testing endpoint kunjungan dengan GET...\n";
        $getTest = $this->requestPcare('kunjungan', 'GET', [], 'application/json');
        echo "GET test response: " . json_encode($getTest) . "\n\n";
        
        echo "Testing endpoint kunjungan dengan POST...\n";
        $postResponse = $this->requestPcare('kunjungan', 'POST', $kunjunganData, 'application/json');
        echo "POST response: " . json_encode($postResponse) . "\n\n";
        
        // Cek apakah ada error dalam response
        if (empty($postResponse)) {
            echo "❌ Response kosong dari PCare API\n";
        } elseif (isset($postResponse['metaData'])) {
            $code = $postResponse['metaData']['code'] ?? 'unknown';
            $message = $postResponse['metaData']['message'] ?? 'no message';
            echo "📊 Response Code: {$code}\n";
            echo "📝 Response Message: {$message}\n";
            
            if ($code == '200' || $code == '201') {
                echo "✅ Request berhasil!\n";
                if (isset($postResponse['response'])) {
                    echo "📦 Response Data: " . json_encode($postResponse['response']) . "\n";
                }
            } else {
                echo "❌ Request gagal dengan kode: {$code}\n";
            }
        } else {
            echo "⚠️ Response format tidak dikenali: " . json_encode($postResponse) . "\n";
        }
        
        echo "\n=== TEST SELESAI ===\n";
    }
}

$test = new TestPcareDetailed();
$test->testKunjunganWithDetailedLogging();