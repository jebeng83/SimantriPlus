<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DataIbuHamil;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

class TestIbuHamilData extends Command
{
    use EnkripsiData;
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:ibu-hamil {id_hamil?} {no_rkm_medis?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menguji pencarian data ibu hamil dan dekripsi nomor rekam medis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('===== Menguji Data Ibu Hamil =====');
        
        // Ambil parameter dari command atau gunakan nilai default
        $idHamil = $this->argument('id_hamil') ?? 'IH-0001';
        $noRkmMedis = $this->argument('no_rkm_medis') ?? 'MTAxNDBLUi41';
        
        $this->info("Mencari data dengan ID: $idHamil");
        $this->info("Mencari data dengan No. RM Terenkripsi: $noRkmMedis");
        
        // 1. Cek apakah tabel data_ibu_hamil memiliki data
        $totalData = DataIbuHamil::count();
        $this->info("Total data di tabel data_ibu_hamil: $totalData");
        
        // 2. Cari data berdasarkan ID Hamil
        $dataByIdHamil = DataIbuHamil::where('id_hamil', $idHamil)->first();
        if ($dataByIdHamil) {
            $this->info("Data dengan ID $idHamil ditemukan!");
            $this->table(['ID Hamil', 'No RM', 'Nama', 'Status'], [
                [$dataByIdHamil->id_hamil, $dataByIdHamil->no_rkm_medis, $dataByIdHamil->pasien->nm_pasien, $dataByIdHamil->status]
            ]);
        } else {
            $this->error("Data dengan ID $idHamil tidak ditemukan!");
        }
        
        // 3. Uji dekripsi nomor RM
        $this->info("\nMenguji dekripsi nomor RM: $noRkmMedis");
        $decryptedNoRm = $this->decryptData($noRkmMedis);
        $this->info("Hasil dekripsi: $decryptedNoRm");
        
        // 4. Cari data berdasarkan nomor RM yang sudah didekripsi
        $this->info("\nMencari data dengan nomor RM hasil dekripsi: $decryptedNoRm");
        $dataByNoRm = DataIbuHamil::where('no_rkm_medis', $decryptedNoRm)->first();
        if ($dataByNoRm) {
            $this->info("Data dengan No RM $decryptedNoRm ditemukan!");
            $this->table(['ID Hamil', 'No RM', 'Nama', 'Status'], [
                [$dataByNoRm->id_hamil, $dataByNoRm->no_rkm_medis, $dataByNoRm->pasien->nm_pasien, $dataByNoRm->status]
            ]);
        } else {
            $this->error("Data dengan No RM $decryptedNoRm tidak ditemukan!");
        }
        
        // 5. Cari data dengan nomor RM terenkripsi (tanpa dekripsi)
        $this->info("\nMencari data dengan nomor RM terenkripsi langsung: $noRkmMedis");
        $dataByEncodedNoRm = DataIbuHamil::where('no_rkm_medis', $noRkmMedis)->first();
        if ($dataByEncodedNoRm) {
            $this->info("Data dengan No RM terenkripsi $noRkmMedis ditemukan!");
            $this->table(['ID Hamil', 'No RM', 'Nama', 'Status'], [
                [$dataByEncodedNoRm->id_hamil, $dataByEncodedNoRm->no_rkm_medis, $dataByEncodedNoRm->pasien->nm_pasien, $dataByEncodedNoRm->status]
            ]);
        } else {
            $this->error("Data dengan No RM terenkripsi $noRkmMedis tidak ditemukan!");
        }
        
        // 6. Simulasi pencarian di PemeriksaanANC
        $this->info("\n===== Simulasi Pencarian di PemeriksaanANC =====");
        
        // Strategi 1: Cari dengan nomor RM yang sudah didekripsi
        $this->info("Strategi 1: Cari dengan nomor RM yang sudah didekripsi");
        $dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $decryptedNoRm)
            ->where('status', 'Hamil')
            ->first();
        
        if ($dataIbuHamil) {
            $this->info("Strategi 1: Berhasil! Data ditemukan dengan ID: " . $dataIbuHamil->id_hamil);
        } else {
            $this->error("Strategi 1: Gagal! Data tidak ditemukan");
            
            // Strategi 2: Jika tidak ditemukan, coba dengan nomor RM asli
            $this->info("Strategi 2: Cari dengan nomor RM asli");
            $dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $noRkmMedis)
                ->where('status', 'Hamil')
                ->first();
            
            if ($dataIbuHamil) {
                $this->info("Strategi 2: Berhasil! Data ditemukan dengan ID: " . $dataIbuHamil->id_hamil);
            } else {
                $this->error("Strategi 2: Gagal! Data tidak ditemukan");
                
                // Strategi 3: Cek ID Hamil yang spesifik
                $this->info("Strategi 3: Cek ID Hamil yang spesifik");
                $dataIbuHamil = DataIbuHamil::where('id_hamil', $idHamil)
                    ->where('status', 'Hamil')
                    ->first();
                
                if ($dataIbuHamil) {
                    $this->info("Strategi 3: Berhasil! Data ditemukan dengan ID: " . $dataIbuHamil->id_hamil);
                } else {
                    $this->error("Strategi 3: Gagal! Data tidak ditemukan");
                    
                    // Strategi 4: Coba tanpa batasan status "Hamil"
                    $this->info("Strategi 4: Coba tanpa batasan status Hamil");
                    // Coba dengan no_rkm_medis yang sudah didekripsi tanpa batasan status
                    $dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $decryptedNoRm)->first();
                    
                    if ($dataIbuHamil) {
                        $this->info("Strategi 4a: Berhasil! Data ditemukan dengan ID: " . $dataIbuHamil->id_hamil);
                    } else {
                        // Jika masih tidak ditemukan, coba dengan no_rkm_medis asli tanpa batasan status
                        $dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $noRkmMedis)->first();
                        
                        if ($dataIbuHamil) {
                            $this->info("Strategi 4b: Berhasil! Data ditemukan dengan ID: " . $dataIbuHamil->id_hamil);
                        } else {
                            $this->error("Strategi 4: Gagal! Data tidak ditemukan dengan metode apapun");
                            
                            // Cek data di tabel secara umum
                            $this->info("\nMencoba query langsung ke database:");
                            $firstRecord = DataIbuHamil::first();
                            if ($firstRecord) {
                                $this->info("Contoh data pertama di tabel:");
                                $this->table(['ID Hamil', 'No RM', 'Status'], [
                                    [$firstRecord->id_hamil, $firstRecord->no_rkm_medis, $firstRecord->status]
                                ]);
                            } else {
                                $this->error("Tidak ada data sama sekali di tabel data_ibu_hamil");
                            }
                        }
                    }
                }
            }
        }
        
        $this->info("\n===== Pengujian Selesai =====");
    }
}
