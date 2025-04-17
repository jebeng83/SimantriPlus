<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\API\PcareController;
use App\Http\Controllers\API\PemeriksaanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TestPcareIntegration extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:pcare-integration {--no_rawat=2024/06/26/000001} {--nip=102}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test integrasi PCare dan penyimpanan data pemeriksaan';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('=== TEST PCARE INTEGRATION ===');
        
        $no_rawat = $this->option('no_rawat');
        $nip = $this->option('nip');
        
        $this->info("Menggunakan no_rawat: $no_rawat");
        $this->info("Menggunakan nip: $nip");
        
        // 1. Buat data PCare tes
        $dataPcare = [
            "no_rawat" => $no_rawat,
            "no_rkm_medis" => "000001",
            "tgl_registrasi" => date('Y-m-d'),
            "tgl_perawatan" => date('Y-m-d'),
            "jam_rawat" => date('H:i:s'),
            "noKunjungan" => "012345678901",
            "noKartu" => "0001112223334",
            "nm_pasien" => "Pasien Test",
            "kdProviderPeserta" => "0001",
            "kdPoli" => "001",
            "nmPoli" => "POLI UMUM",
            "tglDaftar" => date('d-m-Y'), // Format dd-mm-yyyy untuk PCare
            "keluhan" => "Demam dan batuk",
            "kunjSakit" => "Kunjungan Sakit",
            "kdTkp" => "10",
            "suhu_tubuh" => "37.5",
            "sistole" => "120",
            "diastole" => "80",
            "heartRate" => "88",
            "respRate" => "20",
            "tinggiBadan" => "170",
            "beratBadan" => "65",
            "lingkarPerut" => "80",
            "spo2" => "98",
            "gcs" => "15",
            "kesadaran" => "Compos Mentis",
            "pemeriksaan" => "Pemeriksaan fisik normal",
            "alergi" => true,
            "alergiMakanan" => "Seafood",
            "alergiUdara" => "Debu",
            "alergiObat" => "Penisilin",
            "terapiObat" => "Paracetamol 3x1",
            "terapiNonObat" => "Istirahat cukup",
            "BMHP" => "Kapas, Perban",
            "prognosa" => "Baik",
            "instruksi" => "Minum obat teratur",
            "evaluasi" => "Kontrol 3 hari lagi",
            "nip" => $nip,
            "kd_dokter" => "D0000001",
            "save_to_db" => true
        ];

        // 2. Buat data untuk pemeriksaan
        $dataPemeriksaan = [
            "no_rawat" => $no_rawat,
            "tgl_perawatan" => date('Y-m-d'),
            "jam_rawat" => date('H:i:s'),
            "suhu_tubuh" => "37.5",
            "tensi" => "120/80",
            "nadi" => "88",
            "respirasi" => "20",
            "tinggi" => "170",
            "berat" => "65",
            "spo2" => "98",
            "gcs" => "15",
            "kesadaran" => "Compos Mentis",
            "keluhan" => "Demam dan batuk",
            "pemeriksaan" => "Pemeriksaan fisik normal",
            "alergi" => "Makanan : Seafood, Udara : Debu, Obat : Penisilin",
            "lingkar_perut" => "80",
            "rtl" => "Terapi Obat : Paracetamol 3x1, Terapi Non Obat : Istirahat cukup, BMHP : Kapas, Perban",
            "penilaian" => "Baik",
            "instruksi" => "Minum obat teratur",
            "evaluasi" => "Kontrol 3 hari lagi",
            "nip" => $nip
        ];

        $this->info("\nLangkah 1: Menyimpan data PCare pendaftaran...");
        
        // 3. Jalankan controller PCare untuk menyimpan pendaftaran
        $pcareController = new PcareController();
        $requestPcare = new Request();
        $requestPcare->merge($dataPcare);
        
        try {
            $resultPcare = $pcareController->addPendaftaran($requestPcare);
            
            // Format hasil untuk ditampilkan
            $statusPcare = isset($resultPcare->original['metaData']) 
                ? $resultPcare->original['metaData']['code'] . ' - ' . $resultPcare->original['metaData']['message']
                : "Gagal: Format response tidak sesuai";
                
            $this->info("Status PCare: $statusPcare");
            
            if (!isset($resultPcare->original['metaData']) || !in_array($resultPcare->original['metaData']['code'], [200, 201])) {
                $this->error("Pendaftaran PCare gagal dengan response: " . json_encode($resultPcare->original));
            }
        } catch (\Exception $e) {
            $this->error("PCare pendaftaran gagal: " . $e->getMessage());
            return 1;
        }
        
        $this->info("\nLangkah 2: Menyimpan data pemeriksaan...");

        // 4. Jalankan controller Pemeriksaan untuk menyimpan pemeriksaan
        $pemeriksaanController = new PemeriksaanController();
        $requestPemeriksaan = new Request();
        $requestPemeriksaan->merge($dataPemeriksaan);
        
        try {
            $resultPemeriksaan = $pemeriksaanController->save($requestPemeriksaan);
            
            // Format hasil untuk ditampilkan
            $statusPemeriksaan = isset($resultPemeriksaan->original['success'])
                ? ($resultPemeriksaan->original['success'] ? "Sukses" : "Gagal")
                : "Gagal: Format response tidak sesuai";
                
            $this->info("Status Pemeriksaan: $statusPemeriksaan");
            
            if (!isset($resultPemeriksaan->original['success']) || !$resultPemeriksaan->original['success']) {
                $this->error("Penyimpanan pemeriksaan gagal dengan response: " . json_encode($resultPemeriksaan->original));
            }
        } catch (\Exception $e) {
            $this->error("Pemeriksaan gagal: " . $e->getMessage());
            return 1;
        }

        $this->info("\nLangkah 3: Verifikasi data di database...");
        
        // 5. Cek data di tabel pcare_pendaftaran
        $pcarePendaftaran = DB::table("pcare_pendaftaran")
            ->where("no_rawat", $no_rawat)
            ->first();
        
        if ($pcarePendaftaran) {
            $this->info("✓ Data di pcare_pendaftaran ditemukan");
            $this->table(['Field', 'Value'], [
                ['no_rawat', $pcarePendaftaran->no_rawat],
                ['noKartu', $pcarePendaftaran->noKartu],
                ['keluhan', $pcarePendaftaran->keluhan],
                ['tglDaftar', $pcarePendaftaran->tglDaftar]
            ]);
        } else {
            $this->error("✗ Data di pcare_pendaftaran tidak ditemukan");
        }

        // 6. Cek data di tabel pemeriksaan_ralan
        $pemeriksaanRalan = DB::table("pemeriksaan_ralan")
            ->where("no_rawat", $no_rawat)
            ->first();
            
        if ($pemeriksaanRalan) {
            $this->info("✓ Data di pemeriksaan_ralan ditemukan");
            $this->table(['Field', 'Value'], [
                ['no_rawat', $pemeriksaanRalan->no_rawat],
                ['tgl_perawatan', $pemeriksaanRalan->tgl_perawatan],
                ['suhu_tubuh', $pemeriksaanRalan->suhu_tubuh],
                ['tensi', $pemeriksaanRalan->tensi],
                ['keluhan', $pemeriksaanRalan->keluhan],
                ['alergi', $pemeriksaanRalan->alergi],
                ['rtl', substr($pemeriksaanRalan->rtl, 0, 50) . (strlen($pemeriksaanRalan->rtl) > 50 ? "..." : "")]
            ]);
        } else {
            $this->error("✗ Data di pemeriksaan_ralan tidak ditemukan");
        }

        $this->info("\nTest selesai.");
        return 0;
    }
} 