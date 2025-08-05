<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Traits\PcareTrait;

class TestEndpointFixValidation
{
    use PcareTrait;
    
    public function validateEndpointFix()
    {
        echo "=== VALIDASI PERBAIKAN ENDPOINT KUNJUNGAN PCARE ===\n";
        echo "Testing endpoint change from 'kunjungan' to 'kunjungan/v1'\n\n";
        
        // Ambil data pendaftaran yang berhasil dari database
        echo "1. Mencari data pendaftaran PCare yang berhasil...\n";
        
        try {
            $pendaftaranBerhasil = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->where('penjab.png_jawab', 'LIKE', '%BPJS%')
                ->where('reg_periksa.tgl_registrasi', '>=', '2025-08-05')
                ->whereNotNull('pasien.no_peserta')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.kd_poli',
                    'pasien.no_peserta',
                    'pasien.nm_pasien'
                )
                ->first();
                
            if ($pendaftaranBerhasil) {
                echo "   ✓ Data pendaftaran ditemukan:\n";
                echo "     - No Rawat: {$pendaftaranBerhasil->no_rawat}\n";
                echo "     - Pasien: {$pendaftaranBerhasil->nm_pasien}\n";
                echo "     - No Peserta: {$pendaftaranBerhasil->no_peserta}\n";
                echo "     - Tanggal: {$pendaftaranBerhasil->tgl_registrasi}\n";
                echo "     - Poli: {$pendaftaranBerhasil->kd_poli}\n";
            } else {
                echo "   ⚠️  Tidak ada data pendaftaran BPJS yang ditemukan\n";
                echo "   Menggunakan data sample untuk testing...\n";
            }
        } catch (\Exception $e) {
            echo "   ❌ Error mengambil data: " . $e->getMessage() . "\n";
            echo "   Menggunakan data sample untuk testing...\n";
            $pendaftaranBerhasil = null;
        }
        
        echo "\n2. Menyiapkan data kunjungan untuk testing...\n";
        
        // Siapkan data kunjungan berdasarkan data real atau sample
        if ($pendaftaranBerhasil) {
            $kunjunganData = [
                "noKunjungan" => null,
                "noKartu" => $pendaftaranBerhasil->no_peserta,
                "tglDaftar" => date('d-m-Y', strtotime($pendaftaranBerhasil->tgl_registrasi)),
                "kdPoli" => $pendaftaranBerhasil->kd_poli,
                "keluhan" => "Test endpoint fix",
                "kdSadar" => "04",
                "sistole" => 120,
                "diastole" => 80,
                "beratBadan" => 67.0,
                "tinggiBadan" => 165.0,
                "respRate" => 20,
                "heartRate" => 80,
                "lingkarPerut" => 72.0,
                "kdStatusPulang" => "3",
                "tglPulang" => date('d-m-Y'),
                "kdDokter" => "131491",
                "kdDiag1" => "K29",
                "kdDiag2" => null,
                "kdDiag3" => null,
                "kdPoliRujukInternal" => null,
                "rujukLanjut" => null,
                "kdTacc" => -1,
                "alasanTacc" => null,
                "anamnesa" => "Test endpoint fix",
                "alergiMakan" => "00",
                "alergiUdara" => "00",
                "alergiObat" => "00",
                "kdPrognosa" => "01",
                "terapiObat" => "Test obat",
                "terapiNonObat" => "Edukasi Kesehatan",
                "bmhp" => "Tidak ada",
                "suhu" => "36.5"
            ];
            echo "   ✓ Menggunakan data real dari database\n";
        } else {
            $kunjunganData = [
                "noKunjungan" => null,
                "noKartu" => "0001234567890",
                "tglDaftar" => "05-08-2025",
                "kdPoli" => "001",
                "keluhan" => "Test endpoint fix",
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
                "anamnesa" => "Test endpoint fix",
                "alergiMakan" => "00",
                "alergiUdara" => "00",
                "alergiObat" => "00",
                "kdPrognosa" => "01",
                "terapiObat" => "Test obat",
                "terapiNonObat" => "Edukasi Kesehatan",
                "bmhp" => "Tidak ada",
                "suhu" => "36.5"
            ];
            echo "   ✓ Menggunakan data sample\n";
        }
        
        echo "\n3. Testing endpoint baru 'kunjungan/v1'...\n";
        
        try {
            $response = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');
            echo "   ✓ Response diterima dari endpoint 'kunjungan/v1'\n";
            echo "   Response: " . json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
            
            if (isset($response['metaData'])) {
                $code = $response['metaData']['code'] ?? 'NULL';
                $message = $response['metaData']['message'] ?? 'NULL';
                echo "\n   📊 HASIL ANALISIS:\n";
                echo "   Code: {$code}\n";
                echo "   Message: {$message}\n";
                
                if ($code == 200 || $code == 201) {
                    echo "   🎉 SUCCESS! Kunjungan berhasil dikirim ke PCare\n";
                    echo "   ✅ Perbaikan endpoint berhasil menyelesaikan masalah\n";
                } elseif ($code == 412) {
                    echo "   ⚠️  PRECONDITION_FAILED - Endpoint dapat diakses tetapi ada masalah data\n";
                    echo "   ✅ Perbaikan endpoint berhasil (tidak lagi error 404 Unauthorized)\n";
                    echo "   📝 Langkah selanjutnya: Pastikan data pendaftaran valid\n";
                } elseif ($code == 404) {
                    echo "   ❌ Masih error 404 - Perlu kontak BPJS untuk aktivasi layanan\n";
                } else {
                    echo "   ⚠️  Error code lain: {$code}\n";
                    echo "   📝 Perlu investigasi lebih lanjut\n";
                }
            }
        } catch (\Exception $e) {
            echo "   ❌ ERROR: " . $e->getMessage() . "\n";
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
        echo "RINGKASAN VALIDASI:\n";
        echo "\n";
        echo "✅ Endpoint telah diupdate dari 'kunjungan' ke 'kunjungan/v1'\n";
        echo "✅ Menggunakan format yang sama dengan implementasi Java\n";
        
        if (isset($code)) {
            if ($code == 200 || $code == 201) {
                echo "🎉 MASALAH TERSELESAIKAN! Kunjungan PCare berhasil\n";
            } elseif ($code == 412) {
                echo "✅ PROGRESS POSITIF! Error berubah dari 404 ke 412\n";
                echo "📝 Langkah selanjutnya: Validasi data pendaftaran\n";
            } else {
                echo "⚠️  Masih perlu tindakan lebih lanjut\n";
            }
        }
        
        echo "\n" . str_repeat("=", 60) . "\n";
    }
}

// Jalankan validasi
try {
    $test = new TestEndpointFixValidation();
    $test->validateEndpointFix();
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}