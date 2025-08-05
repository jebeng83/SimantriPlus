<?php

/**
 * Script Debug untuk PCare Pendaftaran
 * Menguji pengiriman data pendaftaran ke PCare BPJS
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\API\PcarePendaftaran;
use Illuminate\Http\Request;

class PcareDebugger
{
    private $pcareController;
    
    public function __construct()
    {
        $this->pcareController = new PcarePendaftaran();
    }
    
    /**
     * Test koneksi ke PCare
     */
    public function testConnection()
    {
        echo "\n=== TEST KONEKSI PCARE ===\n";
        
        // Cek konfigurasi environment
        $configs = [
            'BPJS_PCARE_BASE_URL' => env('BPJS_PCARE_BASE_URL'),
            'BPJS_PCARE_CONS_ID' => env('BPJS_PCARE_CONS_ID'),
            'BPJS_PCARE_USER_KEY' => env('BPJS_PCARE_USER_KEY'),
            'BPJS_PCARE_USER' => env('BPJS_PCARE_USER'),
            'BPJS_PCARE_APP_CODE' => env('BPJS_PCARE_APP_CODE')
        ];
        
        foreach ($configs as $key => $value) {
            $status = !empty($value) ? '✓' : '✗';
            echo "{$status} {$key}: " . (empty($value) ? 'TIDAK ADA' : 'ADA') . "\n";
        }
        
        return !empty($configs['BPJS_PCARE_BASE_URL']);
    }
    
    /**
     * Test dekripsi data
     */
    public function testDecryption()
    {
        echo "\n=== TEST DEKRIPSI DATA ===\n";
        
        // Test data yang bermasalah dari log
        $testData = [
            '000542.5',
            '007057.10',
            'eyJpdiI6IlVUTXFUQTNNRzY1NVdDaTJYQVI0K0E9PSIsInZhbHVlIjoiMGxmTFRnV09NalBIaEoxQysxZWlxZz09IiwibWFjIjoiYmFhMDJlOWFhMWZkMDQyNTAzMzZhMDBhNjA0Njg0NmRhMzY3ZDk4MjA2ZGQ1ZjhmMDk1ZjZiZDE3NjZkYjE1YyIsInRhZyI6IiJ9'
        ];
        
        foreach ($testData as $data) {
            echo "\nTesting data: " . substr($data, 0, 50) . (strlen($data) > 50 ? '...' : '') . "\n";
            
            // Test dengan trait EnkripsiData
            $trait = new class {
                use \App\Traits\EnkripsiData;
            };
            
            $result = $trait->decryptData($data);
            echo "Hasil dekripsi: {$result}\n";
            
            // Cek apakah hasil valid
            if (preg_match('/^\d{6}\.\d{1,2}$/', $result)) {
                echo "✓ Format no_rkm_medis valid\n";
            } elseif (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $result)) {
                echo "✓ Format no_rawat valid\n";
            } else {
                echo "✗ Format tidak dikenali\n";
            }
        }
    }
    
    /**
     * Test pendaftaran PCare dengan data dummy
     */
    public function testPendaftaran()
    {
        echo "\n=== TEST PENDAFTARAN PCARE ===\n";
        
        // Ambil data pasien BPJS terbaru dari database
        $pasienBpjs = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->whereIn('reg_periksa.kd_pj', ['BPJ', 'A14', 'A15'])
            ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
            ->whereNotNull('pasien.no_peserta')
            ->select(
                'reg_periksa.*',
                'pasien.nm_pasien',
                'pasien.no_peserta',
                'pasien.tgl_lahir',
                'penjab.png_jawab'
            )
            ->first();
        
        if (!$pasienBpjs) {
            echo "✗ Tidak ada pasien BPJS hari ini untuk testing\n";
            return false;
        }
        
        echo "✓ Menggunakan data pasien: {$pasienBpjs->nm_pasien} (RM: {$pasienBpjs->no_rkm_medis})\n";
        
        // Buat request data
        $requestData = [
            'kdProviderPeserta' => $pasienBpjs->no_peserta,
            'tglDaftar' => date('d-m-Y', strtotime($pasienBpjs->tgl_registrasi)),
            'noKartu' => $pasienBpjs->no_peserta,
            'kdPoli' => $this->mapPoliToPcare($pasienBpjs->kd_poli),
            'keluhan' => 'Kontrol rutin',
            'kunjSakit' => true,
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 70,
            'tinggiBadan' => 170,
            'respRate' => 20,
            'lingkar_perut' => 0,
            'heartRate' => 80,
            'rujukBalik' => 0,
            'kdTkp' => '10',
            'no_rawat' => $pasienBpjs->no_rawat,
            'no_rkm_medis' => $pasienBpjs->no_rkm_medis,
            'nm_pasien' => $pasienBpjs->nm_pasien,
            'nmPoli' => 'Poli Umum'
        ];
        
        echo "\nData yang akan dikirim:\n";
        foreach ($requestData as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
        
        // Buat request object
        $request = new Request($requestData);
        
        try {
            echo "\n🚀 Mengirim data ke PCare...\n";
            $response = $this->pcareController->addPendaftaran($request);
            
            echo "\n📥 Response dari PCare:\n";
            echo json_encode($response->getData(), JSON_PRETTY_PRINT) . "\n";
            
            return $response->getStatusCode() === 200;
            
        } catch (\Exception $e) {
            echo "\n❌ Error saat mengirim ke PCare:\n";
            echo "Error: " . $e->getMessage() . "\n";
            echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
            
            return false;
        }
    }
    
    /**
     * Mapping kode poli ke PCare
     */
    private function mapPoliToPcare($kdPoli)
    {
        $mapping = [
            'U0001' => '001', // Poli Umum
            'U0002' => '002', // Poli Gigi
            'U0003' => '003', // Poli KIA
            'U0004' => '004', // Poli Gizi
            'U0005' => '005', // Poli Jiwa
        ];
        
        return $mapping[$kdPoli] ?? '001'; // Default ke Poli Umum
    }
    
    /**
     * Jalankan semua test
     */
    public function runAllTests()
    {
        echo "🔍 PCARE PENDAFTARAN DEBUGGER\n";
        echo "================================\n";
        
        $connectionOk = $this->testConnection();
        $this->testDecryption();
        
        if ($connectionOk) {
            $pendaftaranOk = $this->testPendaftaran();
            
            echo "\n=== RINGKASAN ===\n";
            echo "Koneksi PCare: " . ($connectionOk ? '✓ OK' : '✗ GAGAL') . "\n";
            echo "Test Pendaftaran: " . ($pendaftaranOk ? '✓ OK' : '✗ GAGAL') . "\n";
        } else {
            echo "\n❌ Tidak dapat melanjutkan test karena konfigurasi PCare tidak lengkap\n";
        }
        
        echo "\n📋 Cek log di storage/logs/laravel-" . date('Y-m-d') . ".log untuk detail\n";
    }
}

// Jalankan debugger
$debugger = new PcareDebugger();
$debugger->runAllTests();