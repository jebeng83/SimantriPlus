<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\EnkripsiData;
use App\Traits\PcareTrait;

class TestPcareRegistration extends Command
{
    use EnkripsiData, PcareTrait;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pcare-registration {no_rawat?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test PCare registration functionality';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== TEST PCARE REGISTRATION ===');
        
        $noRawat = $this->argument('no_rawat');
        
        if (!$noRawat) {
            // Ambil data terbaru dari reg_periksa untuk testing
            $latestReg = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->whereIn('pasien.kd_pj', ['BPJ', 'PBI', 'NON'])
                ->whereNotNull('pasien.no_peserta')
                ->where('pasien.no_peserta', '!=', '')
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->select('reg_periksa.no_rawat', 'pasien.nm_pasien', 'pasien.no_peserta')
                ->first();
                
            if (!$latestReg) {
                $this->error('Tidak ada data pasien BPJS yang ditemukan untuk testing');
                return 1;
            }
            
            $noRawat = $latestReg->no_rawat;
            $this->info("Menggunakan data pasien: {$latestReg->nm_pasien} (No. Rawat: {$noRawat})");
        }
        
        try {
            $this->info("Testing PCare registration untuk No. Rawat: {$noRawat}");
            
            // Ambil data pasien dan registrasi
            $this->info('1. Mengambil data pasien...');
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.*',
                    'pasien.nm_pasien',
                    'pasien.no_peserta',
                    'pasien.kd_pj',
                    'poliklinik.nm_poli'
                )
                ->first();

            if (!$dataPasien) {
                $this->error('Data pasien tidak ditemukan!');
                return 1;
            }
            
            $this->info("   - Nama: {$dataPasien->nm_pasien}");
            $this->info("   - No. Peserta: {$dataPasien->no_peserta}");
            $this->info("   - Kode Penjamin: {$dataPasien->kd_pj}");
            $this->info("   - Poli: {$dataPasien->nm_poli}");

            // Validasi BPJS
            $this->info('2. Validasi peserta BPJS...');
            $validBpjsTypes = ['BPJ', 'PBI', 'NON'];
            if (!in_array($dataPasien->kd_pj, $validBpjsTypes) || empty($dataPasien->no_peserta)) {
                $this->error('Pasien bukan peserta BPJS atau tidak memiliki nomor peserta!');
                return 1;
            }
            $this->info('   ✓ Pasien adalah peserta BPJS');

            // Cek duplikasi
            $this->info('3. Mengecek duplikasi pendaftaran...');
            $cekPcare = DB::table('pcare_pendaftaran')
                ->where('no_rawat', $noRawat)
                ->where('tglDaftar', date('Y-m-d'))
                ->first();

            if ($cekPcare) {
                $this->warn('Pasien sudah terdaftar di PCare hari ini!');
                $this->info('Melanjutkan testing untuk melihat proses API call...');
            } else {
                $this->info('   ✓ Tidak ada duplikasi');
            }

            // Mapping poli
            $this->info('4. Mengambil mapping poli PCare...');
            $kdPoliPcare = $this->getKdPoliPcare($dataPasien->kd_poli);
            $this->info("   - Kode Poli RS: {$dataPasien->kd_poli}");
            $this->info("   - Kode Poli PCare: {$kdPoliPcare}");

            // Persiapkan data PCare
            $this->info('5. Menyiapkan data PCare...');
            
            // Dapatkan kdProviderPeserta dari environment variable (sama seperti PcarePendaftaran controller)
            $kdProviderPeserta = env('BPJS_PCARE_KODE_PPK', '11251919');
            
            if (empty($kdProviderPeserta)) {
                $this->error('BPJS_PCARE_KODE_PPK environment variable is not set');
                return 1;
            }
            
            $this->info("   kdProviderPeserta: {$kdProviderPeserta}");
            
            $pcareData = [
                'kdProviderPeserta' => $kdProviderPeserta, // Gunakan dari environment variable
                'tglDaftar' => date('d-m-Y'),
                'noKartu' => $dataPasien->no_peserta,
                'kdPoli' => $kdPoliPcare,
                'keluhan' => 'Test keluhan dari command',
                'kunjSakit' => true,
                'sistole' => 120,
                'diastole' => 80,
                'beratBadan' => 70.0,
                'tinggiBadan' => 170.0,
                'respRate' => 20,
                'lingkarPerut' => 80.0,
                'heartRate' => 80,
                'rujukBalik' => 0,
                'kdTkp' => '10'
            ];
            
            $this->info('   Data PCare:');
            foreach ($pcareData as $key => $value) {
                $this->info("     {$key}: {$value}");
            }

            $this->info('6. Mengirim request ke PCare API menggunakan PcareTrait...');
            
            try {
                // Gunakan PcareTrait untuk mengirim request (sama seperti yang digunakan controller)
                $response = $this->requestPcare('pendaftaran', 'POST', $pcareData, 'text/plain');
                
                $this->info('7. Menganalisis response...');
                
                if (isset($response['metaData']['code'])) {
                    $statusCode = $response['metaData']['code'];
                    $message = $response['metaData']['message'] ?? 'No message';
                    
                    $this->info("   Status Code: {$statusCode}");
                    $this->info("   Message: {$message}");
                    
                    if ($statusCode === 201) {
                        $this->info('   ✓ PENDAFTARAN BERHASIL!');
                        if (isset($response['response']['noUrut'])) {
                            $this->info('   No Urut: ' . $response['response']['noUrut']);
                        }
                        
                        // Simpan ke database jika belum ada
                        if (!$cekPcare) {
                            $this->info('8. Menyimpan ke database lokal...');
                            $this->simpanDataPendaftaranPcare($noRawat, $pcareData, $response, $dataPasien);
                            $this->info('   ✓ Data berhasil disimpan');
                        }
                        
                    } elseif ($statusCode === 401 && strpos($message, 'sudah di-entri') !== false) {
                        $this->warn('   ⚠ PENDAFTARAN SUDAH ADA!');
                        $this->warn("   Message: {$message}");
                        $this->info('   Status: Pasien sudah terdaftar hari ini - ini normal untuk testing');
                    } else {
                        $this->error('   ✗ PENDAFTARAN GAGAL!');
                        $this->error("   Status Code: {$statusCode}");
                        $this->error("   Error: {$message}");
                        
                        if (isset($response['response'])) {
                            $this->info('   Response detail:');
                            $this->info('     ' . json_encode($response['response'], JSON_PRETTY_PRINT));
                        }
                    }
                } else {
                    $this->error('   ✗ Response tidak valid dari PCare API');
                }
                
            } catch (\Exception $e) {
                $this->error('   ✗ Exception saat mengirim request:');
                $this->error('   Error: ' . $e->getMessage());
            }
            
            $this->info('=== TEST SELESAI ===');
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Exception terjadi: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
    
    /**
     * Ambil kode poli PCare dari database mapping
     */
    private function getKdPoliPcare($kdPoli)
    {
        $mapping = DB::table('maping_poliklinik_pcare')
            ->where('kd_poli_rs', $kdPoli)
            ->first();

        if ($mapping && !empty($mapping->kd_poli_pcare)) {
            return $mapping->kd_poli_pcare;
        }

        return '001'; // Default
    }
    
    /**
     * Simpan data pendaftaran PCare ke database lokal
     */
    private function simpanDataPendaftaranPcare($noRawat, $pcareData, $responseData, $dataPasien)
    {
        try {
            // Konversi format tanggal dari d-m-Y ke Y-m-d untuk database
            $tglDaftarParts = explode('-', $pcareData['tglDaftar']);
            $tglDaftarDB = $tglDaftarParts[2] . '-' . $tglDaftarParts[1] . '-' . $tglDaftarParts[0];

            DB::table('pcare_pendaftaran')->insert([
                'no_rawat' => $noRawat,
                'tglDaftar' => $tglDaftarDB,
                'no_rkm_medis' => $dataPasien->no_rkm_medis ?? '',
                'nm_pasien' => $dataPasien->nm_pasien ?? '',
                'kdProviderPeserta' => $pcareData['kdProviderPeserta'],
                'noKartu' => $pcareData['noKartu'],
                'kdPoli' => $pcareData['kdPoli'],
                'nmPoli' => $dataPasien->nm_poli ?? '',
                'keluhan' => $pcareData['keluhan'] ?? '',
                'kunjSakit' => $pcareData['kunjSakit'] ? 'Kunjungan Sakit' : 'Kunjungan Sehat',
                'sistole' => (string)$pcareData['sistole'],
                'diastole' => (string)$pcareData['diastole'],
                'beratBadan' => (string)$pcareData['beratBadan'],
                'tinggiBadan' => (string)$pcareData['tinggiBadan'],
                'respRate' => (string)$pcareData['respRate'],
                'lingkar_perut' => (string)$pcareData['lingkarPerut'],
                'heartRate' => (string)$pcareData['heartRate'],
                'rujukBalik' => (string)$pcareData['rujukBalik'],
                'kdTkp' => $pcareData['kdTkp'],
                'noUrut' => $responseData['response']['noUrut'] ?? $responseData['response']['message'] ?? null,
                'status' => 'Terkirim'
            ]);
            
        } catch (\Exception $e) {
            $this->error('Error saat menyimpan ke database: ' . $e->getMessage());
        }
    }
}