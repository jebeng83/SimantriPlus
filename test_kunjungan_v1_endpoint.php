<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use App\Traits\PcareTrait;

class TestKunjunganV1Endpoint
{
    use PcareTrait;
    
    public function testEndpointVersions()
    {
        echo "=== TESTING PCARE KUNJUNGAN ENDPOINT VERSIONS ===\n";
        echo "Comparing current 'kunjungan' vs Java version 'kunjungan/v1'\n\n";
        
        // Sample data yang sama dengan Java implementation
        $kunjunganData = [
            "noKunjungan" => null,
            "noKartu" => "0001234567890",
            "tglDaftar" => "05-08-2025",
            "kdPoli" => "001",
            "keluhan" => "Test kunjungan v1",
            "kdSadar" => "04",
            "sistole" => 120,
            "diastole" => 80,
            "beratBadan" => 67.0,
            "tinggiBadan" => 165.0,
            "respRate" => 20,
            "heartRate" => 80,
            "lingkarPerut" => 72.0,
            "kdStatusPulang" => "3",
            "tglPulang" => "05-08-2025",
            "kdDokter" => "131491",
            "kdDiag1" => "K29",
            "kdDiag2" => null,
            "kdDiag3" => null,
            "kdPoliRujukInternal" => null,
            "rujukLanjut" => null,
            "kdTacc" => -1,
            "alasanTacc" => null,
            "anamnesa" => "Test kunjungan v1",
            "alergiMakan" => "00",
            "alergiUdara" => "00",
            "alergiObat" => "00",
            "kdPrognosa" => "01",
            "terapiObat" => "Test obat",
            "terapiNonObat" => "Edukasi Kesehatan",
            "bmhp" => "Tidak ada",
            "suhu" => "36.5"
        ];
        
        echo "Data kunjungan yang akan dikirim:\n";
        echo json_encode($kunjunganData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";
        
        // Test 1: Current implementation (kunjungan)
        echo "1. Testing current endpoint: 'kunjungan'\n";
        echo "   URL akan menjadi: {base_url}/pcare-rest/kunjungan\n";
        try {
            $response1 = $this->requestPcare('kunjungan', 'POST', $kunjunganData, 'text/plain');
            echo "   ✓ Response diterima\n";
            echo "   Response: " . json_encode($response1, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            if (isset($response1['metaData'])) {
                $code1 = $response1['metaData']['code'] ?? 'NULL';
                $message1 = $response1['metaData']['message'] ?? 'NULL';
                echo "   Code: {$code1}\n";
                echo "   Message: {$message1}\n";
                
                if ($code1 == 200 || $code1 == 201) {
                    echo "   🎉 SUCCESS dengan endpoint 'kunjungan'\n";
                } else {
                    echo "   ❌ FAILED dengan endpoint 'kunjungan': {$message1}\n";
                }
            }
        } catch (\Exception $e) {
            echo "   ❌ ERROR: " . $e->getMessage() . "\n";
        }
        
        echo "\n" . str_repeat("-", 60) . "\n\n";
        
        // Test 2: Java version (kunjungan/v1)
        echo "2. Testing Java version endpoint: 'kunjungan/v1'\n";
        echo "   URL akan menjadi: {base_url}/pcare-rest/kunjungan/v1\n";
        try {
            $response2 = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');
            echo "   ✓ Response diterima\n";
            echo "   Response: " . json_encode($response2, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            if (isset($response2['metaData'])) {
                $code2 = $response2['metaData']['code'] ?? 'NULL';
                $message2 = $response2['metaData']['message'] ?? 'NULL';
                echo "   Code: {$code2}\n";
                echo "   Message: {$message2}\n";
                
                if ($code2 == 200 || $code2 == 201) {
                    echo "   🎉 SUCCESS dengan endpoint 'kunjungan/v1'\n";
                } else {
                    echo "   ❌ FAILED dengan endpoint 'kunjungan/v1': {$message2}\n";
                }
            }
        } catch (\Exception $e) {
            echo "   ❌ ERROR: " . $e->getMessage() . "\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "ANALISIS HASIL:\n";
        echo "\n";
        
        // Bandingkan hasil
        if (isset($response1) && isset($response2)) {
            $code1 = $response1['metaData']['code'] ?? null;
            $code2 = $response2['metaData']['code'] ?? null;
            
            if ($code1 != $code2) {
                echo "📊 PERBEDAAN DITEMUKAN!\n";
                echo "   - Endpoint 'kunjungan': Code {$code1}\n";
                echo "   - Endpoint 'kunjungan/v1': Code {$code2}\n";
                
                if (($code2 == 200 || $code2 == 201) && ($code1 != 200 && $code1 != 201)) {
                    echo "\n🎯 SOLUSI DITEMUKAN!\n";
                    echo "   Endpoint 'kunjungan/v1' berhasil, sedangkan 'kunjungan' gagal.\n";
                    echo "   Aplikasi harus menggunakan endpoint 'kunjungan/v1' seperti Java.\n";
                }
            } else {
                echo "📊 Kedua endpoint memberikan hasil yang sama.\n";
                echo "   Masalah bukan pada versi endpoint.\n";
            }
        }
        
        echo "\n";
        echo "REKOMENDASI:\n";
        if (isset($code2) && ($code2 == 200 || $code2 == 201)) {
            echo "✅ Gunakan endpoint 'kunjungan/v1' dalam aplikasi\n";
            echo "✅ Update kode untuk menggunakan versi yang sama dengan Java\n";
        } else {
            echo "⚠️  Kedua endpoint gagal - masalah bukan pada versi endpoint\n";
            echo "⚠️  Tetap perlu kontak BPJS untuk aktivasi layanan\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
    }
}

// Jalankan test
try {
    $test = new TestKunjunganV1Endpoint();
    $test->testEndpointVersions();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}