<?php

require_once __DIR__.'/../../vendor/autoload.php';

use AamDsam\Bpjs\PCare;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Capsule\Manager as Capsule;

// Setup Database Connection
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'kerjo',
    'username' => 'root',
    'password' => '',
    'charset' => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix' => '',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();

// Setup PCare Configuration
$config = [
    'base_url' => 'https://apijkn.bpjs-kesehatan.go.id/pcare-rest',
    'service_name' => 'kunjungan',
    'username' => '0159B0001',
    'password' => 'Puskesmas123#',
    'cons_id' => '27999',
    'secret_key' => '1aE95A11CB'
];

// Test Data
$dataKunjungan = [
    'noKunjungan' => null,
    'noKartu' => '0000043678034',
    'tglDaftar' => '28-05-2025',
    'kdPoli' => 'UMU',
    'keluhan' => 'Demam dan batuk',
    'kdSadar' => '01',
    'sistole' => 120,
    'diastole' => 80,
    'beratBadan' => 65,
    'tinggiBadan' => 170,
    'respRate' => 20,
    'heartRate' => 80,
    'lingkarPerut' => 80,
    'kdStatusPulang' => '3',
    'tglPulang' => '28-05-2025',
    'kdDokter' => '176',
    'kdDiag1' => 'A01.0',
    'kdDiag2' => null,
    'kdDiag3' => null,
    'kdPoliRujukInternal' => null,
    'rujukLanjut' => null,
    'kdTacc' => 0,
    'alasanTacc' => null,
    'suhu' => '36.5',
    'KdAlergiMakanan' => '00',
    'KdAlergiUdara' => '00',
    'KdAlergiObat' => '00',
    'KdPrognosa' => '01',
    'terapi' => 'Paracetamol 3x1',
    'terapi_non_obat' => 'Istirahat cukup',
    'bmhp' => '-'
];

try {
    echo "=== Test Kirim dan Simpan Kunjungan PCare ===\n\n";
    
    // 1. Test koneksi database
    echo "Testing koneksi database...\n";
    try {
        $testConnection = Capsule::connection()->getPdo();
        echo "✅ Koneksi database berhasil\n\n";
    } catch (\Exception $e) {
        throw new Exception("Koneksi database gagal: " . $e->getMessage());
    }

    // Get data reg_periksa
    $regPeriksa = Capsule::table('reg_periksa')
        ->where('tgl_registrasi', '2025-05-28')
        ->where('p_jawab', 'MARGONO')
        ->first();
        
    if (!$regPeriksa) {
        throw new Exception("Data reg_periksa untuk pasien Margono tidak ditemukan");
    }
    
    echo "Data Pendaftaran:\n";
    echo "No Rawat: " . $regPeriksa->no_rawat . "\n";
    echo "No RM: " . $regPeriksa->no_rkm_medis . "\n";
    echo "Nama: " . $regPeriksa->p_jawab . "\n";
    echo "Tanggal: " . $regPeriksa->tgl_registrasi . "\n\n";
    
    // 2. Kirim data ke BPJS
    echo "Mengirim data ke BPJS...\n";
    $kunjungan = new PCare\Kunjungan($config);
    $response = $kunjungan->store($dataKunjungan);
    
    echo "\n=== Response BPJS ===\n";
    echo "Status Code: " . $response['metaData']['code'] . "\n";
    echo "Message: " . $response['metaData']['message'] . "\n";
    
    if (isset($response['response'])) {
        echo "Response Data:\n";
        print_r($response['response']);
    }
    
    // Extract noKunjungan from response
    $noKunjungan = null;
    if (isset($response['response'])) {
        if (is_array($response['response'])) {
            if (isset($response['response']['message'])) {
                $noKunjungan = $response['response']['message'];
            } else if (isset($response['response']['noKunjungan'])) {
                $noKunjungan = $response['response']['noKunjungan'];
            }
        } else if (is_string($response['response'])) {
            $noKunjungan = $response['response'];
        }
        
        if ($noKunjungan) {
            echo "\n✅ Nomor Kunjungan dari BPJS: " . $noKunjungan . "\n";
        } else {
            echo "\n❌ WARNING: Format response tidak sesuai ekspektasi\n";
            print_r($response);
        }
    } else {
        echo "\n❌ WARNING: Tidak mendapatkan response dari BPJS\n";
    }
    
    // 3. Cek response
    if (!isset($response['metaData']['code']) || $response['metaData']['code'] != '201') {
        throw new Exception("Gagal mengirim data ke BPJS: " . 
            ($response['metaData']['message'] ?? 'Unknown error'));
    }
    
    // Get nama pasien from pasien table
    $pasien = Capsule::table('pasien')
        ->where('no_rkm_medis', $regPeriksa->no_rkm_medis)
        ->first();
    
    // 4. Siapkan data untuk disimpan
    $tglDaftar = DateTime::createFromFormat('d-m-Y', $dataKunjungan['tglDaftar']);
    $tglPulang = DateTime::createFromFormat('d-m-Y', $dataKunjungan['tglPulang']);
    
    $dataToSave = [
        'no_rawat' => $regPeriksa->no_rawat,
        'noKunjungan' => $noKunjungan,
        'tglDaftar' => $tglDaftar->format('Y-m-d'),
        'no_rkm_medis' => $regPeriksa->no_rkm_medis,
        'nm_pasien' => $pasien ? $pasien->nm_pasien : '',
        'noKartu' => $dataKunjungan['noKartu'],
        'kdPoli' => $dataKunjungan['kdPoli'],
        'nmPoli' => 'POLI UMUM',
        'keluhan' => $dataKunjungan['keluhan'],
        'kdSadar' => $dataKunjungan['kdSadar'],
        'nmSadar' => 'Compos Mentis',
        'sistole' => $dataKunjungan['sistole'],
        'diastole' => $dataKunjungan['diastole'],
        'beratBadan' => $dataKunjungan['beratBadan'],
        'tinggiBadan' => $dataKunjungan['tinggiBadan'],
        'respRate' => $dataKunjungan['respRate'],
        'heartRate' => $dataKunjungan['heartRate'],
        'lingkarPerut' => $dataKunjungan['lingkarPerut'],
        'terapi' => $dataKunjungan['terapi'],
        'kdStatusPulang' => $dataKunjungan['kdStatusPulang'],
        'nmStatusPulang' => 'Berobat Jalan',
        'tglPulang' => $tglPulang->format('Y-m-d'),
        'kdDokter' => $regPeriksa->kd_dokter,
        'nmDokter' => 'dr. BUDI',
        'kdDiag1' => $dataKunjungan['kdDiag1'],
        'nmDiag1' => 'Demam Tifoid',
        'kdDiag2' => $dataKunjungan['kdDiag2'],
        'nmDiag2' => null,
        'kdDiag3' => $dataKunjungan['kdDiag3'],
        'nmDiag3' => null,
        'status' => $noKunjungan ? 'Terkirim' : 'Gagal',
        'KdAlergiMakanan' => $dataKunjungan['KdAlergiMakanan'],
        'NmAlergiMakanan' => 'TIDAK ADA ALERGI MAKANAN',
        'KdAlergiUdara' => $dataKunjungan['KdAlergiUdara'],
        'NmAlergiUdara' => 'TIDAK ADA ALERGI UDARA',
        'KdAlergiObat' => $dataKunjungan['KdAlergiObat'],
        'NmAlergiObat' => 'TIDAK ADA ALERGI OBAT',
        'KdPrognosa' => $dataKunjungan['KdPrognosa'],
        'NmPrognosa' => 'BAIK',
        'terapi_non_obat' => $dataKunjungan['terapi_non_obat'],
        'bmhp' => $dataKunjungan['bmhp']
    ];
    
    // 5. Simpan ke database
    echo "\nMenyimpan data ke database...\n";
    try {
        // Hapus data lama jika ada
        Capsule::table('pcare_kunjungan_umum')
            ->where('no_rawat', $regPeriksa->no_rawat)
            ->delete();
            
        $saved = Capsule::table('pcare_kunjungan_umum')->insert($dataToSave);
        
        if ($saved) {
            echo "\n✅ SUCCESS: Data berhasil disimpan ke database!\n";
            echo "Detail data yang disimpan:\n";
            echo "No Rawat: " . $dataToSave['no_rawat'] . "\n";
            echo "No Kunjungan: " . ($dataToSave['noKunjungan'] ?: 'BELUM ADA') . "\n";
            echo "Nama Pasien: " . $dataToSave['nm_pasien'] . "\n";
            echo "Tanggal: " . $dataToSave['tglDaftar'] . "\n";
            echo "Status: " . $dataToSave['status'] . "\n";
            
            // 6. Verifikasi data tersimpan
            echo "\nMemverifikasi data di database...\n";
            $savedData = Capsule::table('pcare_kunjungan_umum')
                ->where('no_rawat', $dataToSave['no_rawat'])
                ->first();
                
            if ($savedData) {
                echo "✅ Data ditemukan di database\n";
                if ($savedData->noKunjungan) {
                    echo "✅ Nomor Kunjungan tersimpan: " . $savedData->noKunjungan . "\n";
                } else {
                    echo "❌ WARNING: Nomor Kunjungan belum tersimpan\n";
                }
            } else {
                echo "❌ ERROR: Data tidak ditemukan setelah disimpan!\n";
            }
        } else {
            throw new Exception("Gagal menyimpan data ke database");
        }
    } catch (\Exception $e) {
        throw new Exception("Error saat menyimpan ke database: " . $e->getMessage());
    }

} catch (Exception $e) {
    echo "\n❌ ERROR:\n";
    echo $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (Line: " . $e->getLine() . ")\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
} 