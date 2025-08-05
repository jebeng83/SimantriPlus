<?php

/**
 * Script untuk menguji dan memperbaiki mapping kdProviderPeserta PCare
 */

require_once __DIR__ . '/vendor/autoload.php';

// Load Laravel environment
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\PcareTrait;

class PcareProviderTester
{
    use PcareTrait;
    
    /**
     * Test mendapatkan data peserta dari PCare
     */
    public function testGetPeserta($noKartu)
    {
        echo "\n=== TEST GET PESERTA PCARE ===\n";
        echo "No Kartu: {$noKartu}\n";
        
        try {
            // Format endpoint untuk mendapatkan data peserta
            $endpoint = "peserta/{$noKartu}";
            $response = $this->requestPcare($endpoint, 'GET');
            
            echo "\n📥 Response Peserta:\n";
            echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($response['response']['kdProviderPst']['kdProvider'])) {
                $kdProvider = $response['response']['kdProviderPst']['kdProvider'];
                $nmProvider = $response['response']['kdProviderPst']['nmProvider'];
                echo "\n✓ kdProviderPeserta yang benar: {$kdProvider}\n";
                echo "✓ Nama Provider: {$nmProvider}\n";
                return $kdProvider;
            } else {
                echo "\n❌ kdProviderPeserta tidak ditemukan dalam response\n";
                return null;
            }
            
        } catch (\Exception $e) {
            echo "\n❌ Error saat mengambil data peserta:\n";
            echo "Error: " . $e->getMessage() . "\n";
            return null;
        }
    }
    
    /**
     * Test pendaftaran dengan kdProviderPeserta yang benar
     */
    public function testPendaftaranWithCorrectProvider($noKartu, $kdProvider)
    {
        echo "\n=== TEST PENDAFTARAN DENGAN PROVIDER YANG BENAR ===\n";
        
        // Ambil data pasien dari database
        $pasien = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->where('pasien.no_peserta', $noKartu)
            ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
            ->select(
                'reg_periksa.*',
                'pasien.nm_pasien',
                'pasien.no_peserta'
            )
            ->first();
        
        if (!$pasien) {
            echo "❌ Pasien dengan no kartu {$noKartu} tidak ditemukan\n";
            return false;
        }
        
        // Data untuk pendaftaran
        $data = [
            'kdProviderPeserta' => $kdProvider, // Gunakan kdProvider yang benar
            'tglDaftar' => date('d-m-Y'),
            'noKartu' => $noKartu,
            'kdPoli' => '001', // Poli Umum
            'keluhan' => 'Kontrol rutin',
            'kunjSakit' => true,
            'sistole' => 120,
            'diastole' => 80,
            'beratBadan' => 70,
            'tinggiBadan' => 170,
            'respRate' => 20,
            'lingkarPerut' => 0,
            'heartRate' => 80,
            'rujukBalik' => 0,
            'kdTkp' => '10'
        ];
        
        echo "\nData pendaftaran:\n";
        foreach ($data as $key => $value) {
            echo "  {$key}: {$value}\n";
        }
        
        try {
            $endpoint = "pendaftaran";
            $response = $this->requestPcare($endpoint, 'POST', $data, 'text/plain');
            
            echo "\n📥 Response Pendaftaran:\n";
            echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($response['metaData']['code']) && $response['metaData']['code'] == 201) {
                echo "\n✅ PENDAFTARAN BERHASIL!\n";
                echo "No Urut: " . ($response['response']['message'] ?? 'N/A') . "\n";
                return true;
            } else {
                echo "\n❌ PENDAFTARAN GAGAL\n";
                echo "Code: " . ($response['metaData']['code'] ?? 'N/A') . "\n";
                echo "Message: " . ($response['metaData']['message'] ?? 'N/A') . "\n";
                return false;
            }
            
        } catch (\Exception $e) {
            echo "\n❌ Error saat pendaftaran:\n";
            echo "Error: " . $e->getMessage() . "\n";
            return false;
        }
    }
    
    /**
     * Test mendapatkan daftar provider
     */
    public function testGetProviders()
    {
        echo "\n=== TEST GET PROVIDERS ===\n";
        
        try {
            $endpoint = "provider";
            $response = $this->requestPcare($endpoint, 'GET');
            
            echo "\n📥 Response Providers:\n";
            echo json_encode($response, JSON_PRETTY_PRINT) . "\n";
            
            if (isset($response['response']) && is_array($response['response'])) {
                echo "\n✓ Daftar Provider:\n";
                foreach ($response['response'] as $provider) {
                    echo "  - Kode: {$provider['kdProvider']} | Nama: {$provider['nmProvider']}\n";
                }
            }
            
        } catch (\Exception $e) {
            echo "\n❌ Error saat mengambil daftar provider:\n";
            echo "Error: " . $e->getMessage() . "\n";
        }
    }
    
    /**
     * Jalankan semua test
     */
    public function runTests()
    {
        echo "🔍 PCARE PROVIDER TESTER\n";
        echo "========================\n";
        
        // Ambil nomor kartu BPJS dari database
        $pasienBpjs = DB::table('pasien')
            ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->whereIn('reg_periksa.kd_pj', ['BPJ', 'A14', 'A15'])
            ->where('reg_periksa.tgl_registrasi', date('Y-m-d'))
            ->whereNotNull('pasien.no_peserta')
            ->select('pasien.no_peserta', 'pasien.nm_pasien')
            ->first();
        
        if (!$pasienBpjs) {
            echo "❌ Tidak ada pasien BPJS hari ini\n";
            return;
        }
        
        $noKartu = $pasienBpjs->no_peserta;
        echo "\n🎯 Testing dengan pasien: {$pasienBpjs->nm_pasien}\n";
        echo "No Kartu: {$noKartu}\n";
        
        // Test 1: Get daftar provider
        $this->testGetProviders();
        
        // Test 2: Get data peserta untuk mendapatkan kdProvider yang benar
        $kdProvider = $this->testGetPeserta($noKartu);
        
        // Test 3: Jika berhasil mendapatkan kdProvider, test pendaftaran
        if ($kdProvider) {
            $success = $this->testPendaftaranWithCorrectProvider($noKartu, $kdProvider);
            
            echo "\n=== RINGKASAN ===\n";
            echo "Get Provider: ✓ OK\n";
            echo "Get Peserta: ✓ OK\n";
            echo "Pendaftaran: " . ($success ? '✅ BERHASIL' : '❌ GAGAL') . "\n";
            
            if ($success) {
                echo "\n🎉 SOLUSI DITEMUKAN!\n";
                echo "Gunakan kdProviderPeserta: {$kdProvider}\n";
                echo "Untuk semua pendaftaran pasien dengan no kartu: {$noKartu}\n";
            }
        } else {
            echo "\n❌ Tidak dapat melanjutkan test karena kdProvider tidak ditemukan\n";
        }
        
        echo "\n📋 Cek log di storage/logs/laravel-" . date('Y-m-d') . ".log untuk detail\n";
    }
}

// Jalankan tester
$tester = new PcareProviderTester();
$tester->runTests();