<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

$no_rawat = '2025/08/05/000005';

echo "=== TESTING PCARE KUNJUNGAN SUBMISSION (Manual) ===\n";
echo "No Rawat: {$no_rawat}\n\n";

try {
    echo "1. Mengambil data pasien dan pemeriksaan...\n";
    
    // Ambil data pasien dan pemeriksaan
    $dataPasien = DB::table('reg_periksa')
        ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
        ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
        ->where('reg_periksa.no_rawat', $no_rawat)
        ->select(
            'reg_periksa.*',
            'pasien.no_peserta',
            'pasien.nm_pasien',
            'poliklinik.nm_poli',
            'dokter.nm_dokter',
            'dokter.kd_dokter'
        )
        ->first();

    if (!$dataPasien) {
        echo "✗ Data pasien tidak ditemukan\n";
        exit(1);
    }

    echo "✓ Data pasien ditemukan: {$dataPasien->nm_pasien}\n";

    // Cek apakah pasien BPJS
    if (empty($dataPasien->no_peserta)) {
        echo "✗ Pasien bukan peserta BPJS\n";
        exit(1);
    }

    echo "✓ Pasien adalah peserta BPJS: {$dataPasien->no_peserta}\n";

    // Ambil data pemeriksaan
    $pemeriksaanData = DB::table('pemeriksaan_ralan')
        ->where('no_rawat', $no_rawat)
        ->first();

    if (!$pemeriksaanData) {
        echo "✗ Data pemeriksaan tidak ditemukan\n";
        exit(1);
    }

    echo "✓ Data pemeriksaan ditemukan\n";

    // Ambil diagnosa utama
    $diagnosaUtama = DB::table('diagnosa_pasien')
        ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
        ->where('diagnosa_pasien.no_rawat', $no_rawat)
        ->where('diagnosa_pasien.prioritas', 1)
        ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
        ->first();

    if (!$diagnosaUtama) {
        echo "✗ Diagnosa utama tidak ditemukan\n";
        exit(1);
    }

    echo "✓ Diagnosa utama: {$diagnosaUtama->kd_penyakit} - {$diagnosaUtama->nm_penyakit}\n";

    echo "\n2. Menyiapkan data untuk PCare...\n";

    // Siapkan data kunjungan
    $kunjunganData = [
        'noKunjungan' => '', // akan diisi oleh PCare
        'noKartu' => $dataPasien->no_peserta,
        'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
        'kdPoli' => '003', // sesuaikan dengan mapping poli
        'keluhan' => $pemeriksaanData->keluhan ?? 'Kontrol rutin',
        'kunjSakit' => true,
        'sistole' => (int)($pemeriksaanData->tensi ? explode('/', $pemeriksaanData->tensi)[0] : 120),
        'diastole' => (int)($pemeriksaanData->tensi ? explode('/', $pemeriksaanData->tensi)[1] : 80),
        'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
        'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 160),
        'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
        'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 80),
        'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
        'rujukBalik' => 0,
        'kdTkp' => '10',
        'kdDokter' => $dataPasien->kd_dokter,
        'kdDiag1' => $diagnosaUtama->kd_penyakit,
        'kdSadar' => '01',
        'terapi' => $pemeriksaanData->rtl ?? 'Sesuai indikasi',
        'terapiObat' => $pemeriksaanData->rtl ?? 'Sesuai indikasi',
        'terapiNonObat' => $pemeriksaanData->instruksi ?? 'Kontrol rutin',
        'bmhp' => 'Tidak ada',
        'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
    ];

    echo "✓ Data kunjungan disiapkan\n";
    echo "   - No Kartu: {$kunjunganData['noKartu']}\n";
    echo "   - Tanggal Daftar: {$kunjunganData['tglDaftar']}\n";
    echo "   - Kode Poli: {$kunjunganData['kdPoli']}\n";
    echo "   - Keluhan: {$kunjunganData['keluhan']}\n";
    echo "   - Diagnosa: {$kunjunganData['kdDiag1']}\n";

    echo "\n3. Simulasi penyimpanan ke database (tanpa API call)...\n";

    // Simulasi response sukses dari PCare
    $responseData = [
        'metaData' => ['code' => '200'],
        'response' => ['noKunjungan' => 'TEST_' . time()]
    ];

    // Simpan ke database
    DB::table('pcare_kunjungan_umum')->insert([
        'no_rawat' => $no_rawat,
        'noKunjungan' => $responseData['response']['noKunjungan'],
        'tglDaftar' => date('Y-m-d', strtotime(str_replace('-', '/', $kunjunganData['tglDaftar']))),
        'no_rkm_medis' => $dataPasien->no_rkm_medis,
        'nm_pasien' => $dataPasien->nm_pasien,
        'noKartu' => $kunjunganData['noKartu'],
        'kdPoli' => $kunjunganData['kdPoli'],
        'nmPoli' => $dataPasien->nm_poli ?? '',
        'keluhan' => $kunjunganData['keluhan'],
        'kdSadar' => $kunjunganData['kdSadar'] ?? '',
        'nmSadar' => 'Composmentis',
        'sistole' => $kunjunganData['sistole'] ?? 0,
        'diastole' => $kunjunganData['diastole'] ?? 0,
        'beratBadan' => $kunjunganData['beratBadan'] ?? 0,
        'tinggiBadan' => $kunjunganData['tinggiBadan'] ?? 0,
        'respRate' => $kunjunganData['respRate'] ?? 0,
        'heartRate' => $kunjunganData['heartRate'] ?? 0,
        'lingkarPerut' => $kunjunganData['lingkarPerut'] ?? 0,
        'terapi' => $kunjunganData['terapi'] ?? '',
        'kdDokter' => $kunjunganData['kdDokter'],
        'nmDokter' => $dataPasien->nm_dokter ?? '',
        'kdDiag1' => $kunjunganData['kdDiag1'],
        'nmDiag1' => $diagnosaUtama->nm_penyakit ?? '',
        'terapi_non_obat' => $kunjunganData['terapiNonObat'] ?? '',
        'bmhp' => $kunjunganData['bmhp'] ?? '',
        'status' => 'Test - Berhasil'
    ]);

    echo "✓ Data berhasil disimpan ke pcare_kunjungan_umum\n";

    echo "\n4. Verifikasi data tersimpan...\n";
    $result = DB::table('pcare_kunjungan_umum')->where('no_rawat', $no_rawat)->get();
    
    if ($result->count() > 0) {
        echo "✓ Data berhasil tersimpan ({$result->count()} record):\n";
        foreach ($result as $index => $record) {
            echo "  Record " . ($index + 1) . ":\n";
            echo "    No Kunjungan: {$record->noKunjungan}\n";
            echo "    Status: {$record->status}\n";
            echo "    Tanggal: {$record->tglDaftar}\n";
            echo "    Pasien: {$record->nm_pasien}\n";
            echo "    Diagnosa: {$record->kdDiag1} - {$record->nmDiag1}\n";
            echo "\n";
        }
    } else {
        echo "✗ Data tidak tersimpan\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error terjadi: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
}

echo "=== SELESAI TESTING ===\n";