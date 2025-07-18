<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Http\Controllers\API\PcareController;
use App\Http\Controllers\API\PemeriksaanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PcareTest extends TestCase
{
    /**
     * Test penyimpanan data ke PCare dan tabel pemeriksaan_ralan.
     *
     * @return void
     */
    public function test_save_pcare_and_pemeriksaan_data()
    {
        $this->markTestSkipped('Uji manual saja karena berinteraksi dengan database produksi.');
        
        // 1. Buat data PCare tes
        $dataPcare = [
            "no_rawat" => "2024/06/26/000001",
            "no_rkm_medis" => "000001",
            "tgl_registrasi" => "2024-06-26",
            "tgl_perawatan" => "2024-06-26",
            "jam_rawat" => "09:30:00",
            "noKunjungan" => "012345678901",
            "noKartu" => "0001112223334",
            "nm_pasien" => "Pasien Test",
            "kdProviderPeserta" => "0001",
            "kdPoli" => "001",
            "nmPoli" => "POLI UMUM",
            "tglDaftar" => "26-06-2024", // Format dd-mm-yyyy untuk PCare
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
            "nip" => "D0000001",
            "kd_dokter" => "D0000001",
            "save_to_db" => true
        ];

        // 2. Buat data untuk pemeriksaan
        $dataPemeriksaan = [
            "no_rawat" => "2024/06/26/000001",
            "tgl_perawatan" => "2024-06-26",
            "jam_rawat" => "09:30:00",
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
            "nip" => "102" // Menggunakan NIP yang valid dari tabel pegawai
        ];

        // 3. Jalankan controller PCare untuk menyimpan pendaftaran
        $pcareController = new PcareController();
        $requestPcare = new Request();
        $requestPcare->merge($dataPcare);
        
        try {
            $resultPcare = $pcareController->addPendaftaran($requestPcare);
            $this->assertTrue(
                isset($resultPcare->original['metaData']) && 
                in_array($resultPcare->original['metaData']['code'], [200, 201])
            );
        } catch (\Exception $e) {
            $this->fail("PCare pendaftaran gagal: " . $e->getMessage());
        }

        // 4. Jalankan controller Pemeriksaan untuk menyimpan pemeriksaan
        $pemeriksaanController = new PemeriksaanController();
        $requestPemeriksaan = new Request();
        $requestPemeriksaan->merge($dataPemeriksaan);
        
        try {
            $resultPemeriksaan = $pemeriksaanController->save($requestPemeriksaan);
            $this->assertTrue(
                isset($resultPemeriksaan->original['success']) && 
                $resultPemeriksaan->original['success'] === true
            );
        } catch (\Exception $e) {
            $this->fail("Pemeriksaan gagal: " . $e->getMessage());
        }

        // 5. Cek data di tabel pcare_pendaftaran
        $pcarePendaftaran = DB::table("pcare_pendaftaran")
            ->where("no_rawat", $dataPcare["no_rawat"])
            ->first();
        
        $this->assertNotNull($pcarePendaftaran);
        $this->assertEquals($dataPcare["no_rawat"], $pcarePendaftaran->no_rawat);

        // 6. Cek data di tabel pemeriksaan_ralan
        $pemeriksaanRalan = DB::table("pemeriksaan_ralan")
            ->where("no_rawat", $dataPcare["no_rawat"])
            ->first();
            
        $this->assertNotNull($pemeriksaanRalan);
        $this->assertEquals($dataPemeriksaan["no_rawat"], $pemeriksaanRalan->no_rawat);
        $this->assertEquals($dataPemeriksaan["suhu_tubuh"], $pemeriksaanRalan->suhu_tubuh);
        $this->assertEquals($dataPemeriksaan["tensi"], $pemeriksaanRalan->tensi);
    }

    /**
     * Test validasi data PCare.
     * 
     * @return void
     */
    public function test_validasi_data_pcare()
    {
        // Data PCare yang tidak lengkap (tanpa field wajib)
        $invalidData = [
            "no_rawat" => "2024/06/26/000002",
            "keluhan" => "Demam"
            // Field wajib lainnya sengaja dihilangkan
        ];

        $pcareController = new PcareController();
        $request = new Request();
        $request->merge($invalidData);
        
        $response = $pcareController->addPendaftaran($request);
        
        $this->assertEquals(400, $response->getStatusCode());
        $this->assertArrayHasKey('metaData', $response->original);
        $this->assertEquals(400, $response->original['metaData']['code']);
    }

    /**
     * Test penyimpanan pemeriksaan dengan NIP yang tidak valid.
     * 
     * @return void
     */
    public function test_pemeriksaan_dengan_nip_tidak_valid()
    {
        $this->markTestSkipped('Uji manual saja karena berinteraksi dengan database produksi.');
        
        // Data pemeriksaan dengan NIP yang tidak valid
        $invalidData = [
            "no_rawat" => "2024/06/26/000003",
            "tgl_perawatan" => "2024-06-26",
            "jam_rawat" => "10:30:00",
            "suhu_tubuh" => "37.5",
            "tensi" => "120/80",
            "nadi" => "88",
            "respirasi" => "20",
            "kesadaran" => "Compos Mentis",
            "keluhan" => "Demam",
            "nip" => "INVALID_NIP" // NIP yang tidak valid
        ];

        $pemeriksaanController = new PemeriksaanController();
        $request = new Request();
        $request->merge($invalidData);
        
        $response = $pemeriksaanController->save($request);
        
        $this->assertEquals(500, $response->getStatusCode());
        $this->assertArrayHasKey('success', $response->original);
        $this->assertFalse($response->original['success']);
    }
} 