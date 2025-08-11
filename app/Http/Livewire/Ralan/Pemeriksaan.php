<?php

namespace App\Http\Livewire\Ralan;

use App\Traits\SwalResponse;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Traits\EnkripsiData;
use App\Traits\PcareTrait;

class Pemeriksaan extends Component
{
    use LivewireAlert, EnkripsiData, PcareTrait;
    public $listPemeriksaan, $isCollapsed = false, $noRawat, $noRm, $isMaximized = true, $keluhan, $pemeriksaan, $penilaian, $instruksi, $rtl, $alergi, $suhu, $berat, $tinggi, $tensi, $nadi, $respirasi, $evaluasi, $gcs, $kesadaran = 'Compos Mentis', $lingkar, $spo2;
    public $tgl, $jam;
    public $listeners = ['refreshData' => '$refresh', 'hapusPemeriksaan' => 'hapus', 'updateStatus' => 'updateStatusPasien'];

    public function mount($noRawat, $noRm)
    {
        $this->noRawat = $noRawat;
        $this->noRm = $noRm;
        if (!$this->isCollapsed) {
            $this->getPemeriksaan();
            $this->getListPemeriksaan();
        }
        
        Log::info('=== SELESAI PROSES PENDAFTARAN PCARE ===', [
            'no_rawat' => $noRawat,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    public function openModal()
    {
        $this->emit('openModalRehabMedik');
    }

    /**
     * Method untuk menangani kunjungan PCare BPJS
     * Mengirim data kunjungan ke API PCare sesuai katalog BPJS
     */
    public function kunjunganPcare()
    {
        try {
            Log::info('=== MULAI PROSES KUNJUNGAN PCARE ===', [
                'no_rawat' => $this->noRawat,
                'timestamp' => now()->toDateTimeString()
            ]);

            // Dekode no_rawat
            $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
            
            // Ambil data pasien dan pemeriksaan dengan mapping dokter PCare
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('maping_dokter_pcare', 'reg_periksa.kd_dokter', '=', 'maping_dokter_pcare.kd_dokter')
                ->where('reg_periksa.no_rawat', $decodedNoRawat)
                ->select(
                    'reg_periksa.*',
                    'pasien.no_peserta',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli',
                    'dokter.nm_dokter',
                    'dokter.kd_dokter',
                    'maping_dokter_pcare.kd_dokter_pcare'
                )
                ->first();

            if (!$dataPasien) {
                $this->alert('error', 'Data pasien tidak ditemukan');
                return;
            }

            // Cek apakah pasien BPJS
            if (empty($dataPasien->no_peserta)) {
                $this->alert('warning', 'Pasien bukan peserta BPJS, tidak dapat melakukan kunjungan PCare');
                return;
            }

            // Cek mapping dokter PCare
            if (empty($dataPasien->kd_dokter_pcare)) {
                $this->alert('warning', 'Dokter belum dimapping ke PCare. Silakan mapping dokter terlebih dahulu.');
                return;
            }

            // Set variabel pasien untuk digunakan dalam penyimpanan data
            $pasien = $dataPasien;

            // Ambil data pemeriksaan terbaru
            $pemeriksaanData = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $decodedNoRawat)
                ->orderBy('tgl_perawatan', 'desc')
                ->orderBy('jam_rawat', 'desc')
                ->first();

            // Ambil data diagnosa
            $diagnosaData = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $decodedNoRawat)
                ->where('diagnosa_pasien.prioritas', '1')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();

            // Persiapkan data vital signs
            $sistole = 120;
            $diastole = 80;
            if ($pemeriksaanData && !empty($pemeriksaanData->tensi) && strpos($pemeriksaanData->tensi, '/') !== false) {
                $tensiParts = explode('/', $pemeriksaanData->tensi);
                $sistole = (int)trim($tensiParts[0]) ?: 120;
                $diastole = (int)trim($tensiParts[1]) ?: 80;
            }

            // Ambil mapping poli PCare
            $kdPoliPcare = $this->getKdPoliPcare($dataPasien->kd_poli);

            // Ambil data resep obat dari database
            $terapiObatData = DB::table('resep_obat')
                ->join('resep_dokter', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_obat.no_rawat', $decodedNoRawat)
                ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
                ->get();

            // Format data resep obat menjadi string
            $terapiObatString = 'Edukasi Kesehatan'; // Default value
            if ($terapiObatData->isNotEmpty()) {
                $terapiObatArray = [];
                foreach ($terapiObatData as $obat) {
                    $terapiObatArray[] = $obat->nama_brng . ' ' . $obat->jml . ' [' . $obat->aturan_pakai . ']';
                }
                $terapiObatString = implode(', ', $terapiObatArray);
            }

            // Persiapkan data kunjungan sesuai format BPJS
            // Ambil diagnosa tambahan jika ada
            $diagnosaData2 = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $decodedNoRawat)
                ->where('diagnosa_pasien.prioritas', '2')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();

            $diagnosaData3 = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where('diagnosa_pasien.no_rawat', $decodedNoRawat)
                ->where('diagnosa_pasien.prioritas', '3')
                ->select('diagnosa_pasien.kd_penyakit', 'penyakit.nm_penyakit')
                ->first();

            $kunjunganData = [
                'noKartu' => (string)$dataPasien->no_peserta,
                'tglDaftar' => date('d-m-Y', strtotime($dataPasien->tgl_registrasi)),
                'kdPoli' => (string)$kdPoliPcare,
                'keluhan' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'),
                'kdSadar' => '04', // Compos Mentis
                'sistole' => (int)$sistole,
                'diastole' => (int)$diastole,
                'beratBadan' => (float)($pemeriksaanData->berat ?? 50),
                'tinggiBadan' => (float)($pemeriksaanData->tinggi ?? 170),
                'respRate' => (int)($pemeriksaanData->respirasi ?? 20),
                'heartRate' => (int)($pemeriksaanData->nadi ?? 80),
                'lingkarPerut' => (float)($pemeriksaanData->lingkar_perut ?? 0),
                'kdStatusPulang' => '3', // Sesuai format BPJS: "3"
                'tglPulang' => date('d-m-Y'),
                'kdDokter' => (string)($dataPasien->kd_dokter_pcare ?? ''),
                'kdDiag1' => (string)($diagnosaData->kd_penyakit ?? 'Z00.0'),
                'kdTacc' => -1, // Sesuai format BPJS: -1
                'anamnesa' => (string)($pemeriksaanData->keluhan ?? 'Tidak Ada'), // Sama dengan keluhan
                'alergiMakan' => '00',
                'alergiUdara' => '00',
                'alergiObat' => '00',
                'kdPrognosa' => '02', // Baik
                'terapiObat' => (string)($terapiObatString ?: 'Tidak Ada'),
                'terapiNonObat' => (string)($pemeriksaanData->instruksi ?? 'Edukasi Kesehatan'),
                'bmhp' => 'Tidak Ada',
                'suhu' => (string)($pemeriksaanData->suhu_tubuh ?? '36.5')
            ];

            // Tambahkan diagnosa tambahan hanya jika ada
            if ($diagnosaData2) {
                $kunjunganData['kdDiag2'] = (string)$diagnosaData2->kd_penyakit;
            }
            if ($diagnosaData3) {
                $kunjunganData['kdDiag3'] = (string)$diagnosaData3->kd_penyakit;
            }

            Log::info('Data kunjungan PCare telah disiapkan', [
                'kunjungan_data' => $kunjunganData
            ]);

            // Kirim request ke PCare API menggunakan endpoint v1 (sesuai implementasi Java)
            $responseData = $this->requestPcare('kunjungan/v1', 'POST', $kunjunganData, 'text/plain');

            Log::info('Response kunjungan PCare diterima', [
                'response' => $responseData
            ]);

            // Cek response - handle both old format (200) and new format (201)
            $isSuccess = false;
            $noKunjungan = null;
            
            if (isset($responseData['metaData']['code'])) {
                $responseCode = $responseData['metaData']['code'];
                
                // Handle new format (201 CREATED)
                if ($responseCode == '201' || $responseCode == 201) {
                    $isSuccess = true;
                    // Extract noKunjungan from response array in new format
                    $noKunjungan = null;
                    if (isset($responseData['response']) && is_array($responseData['response'])) {
                        foreach ($responseData['response'] as $item) {
                            if (isset($item['field']) && $item['field'] === 'noKunjungan') {
                                $noKunjungan = $item['message'] ?? null;
                                break;
                            }
                        }
                    }
                    Log::info('PCare kunjungan berhasil dengan format baru', [
                        'code' => $responseCode,
                        'message' => $responseData['metaData']['message'] ?? '',
                        'noKunjungan' => $noKunjungan
                    ]);
                }
                // Handle old format (200 OK)
                elseif ($responseCode == '200' || $responseCode == 200) {
                    $isSuccess = true;
                    // Extract noKunjungan from response.noKunjungan in old format
                    $noKunjungan = $responseData['response']['noKunjungan'] ?? null;
                    Log::info('PCare kunjungan berhasil dengan format lama', [
                        'code' => $responseCode,
                        'noKunjungan' => $noKunjungan
                    ]);
                }
            }
            
            if ($isSuccess) {
                // Validasi noKunjungan sebelum menyimpan ke database
                if (empty($noKunjungan) || is_null($noKunjungan) || trim($noKunjungan) === '') {
                    Log::error('noKunjungan kosong atau tidak valid dari respons BPJS PCare', [
                        'no_rawat' => $decodedNoRawat,
                        'noKunjungan_value' => $noKunjungan,
                        'noKunjungan_type' => gettype($noKunjungan),
                        'response_data' => $responseData
                    ]);
                    
                    $this->alert('error', 'Gagal mendapatkan nomor kunjungan yang valid dari PCare BPJS. Silakan coba lagi.');
                    return;
                }
                
                Log::info('noKunjungan berhasil diperoleh dari PCare', [
                    'no_rawat' => $decodedNoRawat,
                    'noKunjungan' => $noKunjungan
                ]);
                
                // Definisikan variabel nama untuk database
                $nmPoli = $dataPasien->nm_poli ?? '';
                $nmSadar = 'Compos Mentis'; // Default untuk kdSadar '04'
                $nmDokter = $dataPasien->nm_dokter ?? '';
                $nmDiag1 = $diagnosaData->nm_penyakit ?? 'General medical examination';
                $nmDiag2 = '';
                $nmDiag3 = '';
                $nmStatusPulang = 'Sehat'; // Default untuk kdStatusPulang '4'
                $nmAlergiMakanan = 'Tidak ada'; // Default untuk kode '00'
                $nmAlergiUdara = 'Tidak ada'; // Default untuk kode '00'
                $nmAlergiObat = 'Tidak ada'; // Default untuk kode '00'
                $nmPrognosa = 'Baik'; // Default untuk kdPrognosa '02'

                // Simpan data kunjungan ke database lokal
                DB::table('pcare_kunjungan_umum')->insert([
                    'no_rawat' => $decodedNoRawat,
                    'noKunjungan' => $noKunjungan,
                    'tglDaftar' => $kunjunganData['tglDaftar'],
                    'no_rkm_medis' => $dataPasien->no_rkm_medis,
                    'nm_pasien' => $dataPasien->nm_pasien,
                    'noKartu' => $kunjunganData['noKartu'],
                    'kdPoli' => $kunjunganData['kdPoli'],
                    'nmPoli' => $nmPoli,
                    'keluhan' => $kunjunganData['keluhan'],
                    'kdSadar' => $kunjunganData['kdSadar'],
                    'nmSadar' => $nmSadar,
                    'sistole' => $kunjunganData['sistole'],
                    'diastole' => $kunjunganData['diastole'],
                    'beratBadan' => $kunjunganData['beratBadan'],
                    'tinggiBadan' => $kunjunganData['tinggiBadan'],
                    'respRate' => $kunjunganData['respRate'],
                    'heartRate' => $kunjunganData['heartRate'],
                    'lingkarPerut' => $kunjunganData['lingkarPerut'],
                    'terapi' => $kunjunganData['terapiObat'] ?? '',
                    'kdStatusPulang' => $kunjunganData['kdStatusPulang'],
                    'nmStatusPulang' => $nmStatusPulang,
                    'tglPulang' => $kunjunganData['tglPulang'],
                    'kdDokter' => $kunjunganData['kdDokter'],
                    'nmDokter' => $nmDokter,
                    'kdDiag1' => $kunjunganData['kdDiag1'],
                    'nmDiag1' => $nmDiag1,
                    'kdDiag2' => $kunjunganData['kdDiag2'] ?? '',
                    'nmDiag2' => $nmDiag2,
                    'kdDiag3' => $kunjunganData['kdDiag3'] ?? '',
                    'nmDiag3' => $nmDiag3,
                    'status' => 'Terkirim',
                    'kdAlergiMakanan' => $kunjunganData['alergiMakan'] ?? '',
                    'nmAlergiMakanan' => $nmAlergiMakanan,
                    'kdAlergiUdara' => $kunjunganData['alergiUdara'] ?? '',
                    'nmAlergiUdara' => $nmAlergiUdara,
                    'kdAlergiObat' => $kunjunganData['alergiObat'] ?? '',
                    'nmAlergiObat' => $nmAlergiObat,
                    'kdPrognosa' => $kunjunganData['kdPrognosa'],
                    'nmPrognosa' => $nmPrognosa,
                    'terapi_non_obat' => $kunjunganData['terapiNonObat'] ?? '',
                    'bmhp' => $kunjunganData['bmhp'] ?? ''
                ]);

                $this->dispatchBrowserEvent('swal:alert', [
                    'type' => 'success',
                    'title' => 'Berhasil',
                    'text' => 'Kunjungan PCare berhasil dikirim',
                    'position' => 'center',
                    'timer' => 3000,
                    'toast' => false,
                ]);

                Log::info('=== SELESAI PROSES KUNJUNGAN PCARE - BERHASIL ===', [
                    'no_rawat' => $decodedNoRawat,
                    'noKunjungan' => $noKunjungan,
                    'response_format' => isset($responseData['response']['message']) ? 'new_format' : 'old_format'
                ]);
            } else {
                $errorMessage = $responseData['metaData']['message'] ?? 'Gagal mengirim kunjungan PCare';
                
                // Definisikan variabel nama untuk database (untuk kasus gagal)
                $nmPoli = $dataPasien->nm_poli ?? '';
                $nmDokter = $dataPasien->nm_dokter ?? '';
                $nmDiag1 = $diagnosaData->nm_penyakit ?? 'General medical examination';

                // Simpan data kunjungan dengan status gagal
                DB::table('pcare_kunjungan_umum')->insert([
                    'no_rawat' => $decodedNoRawat,
                    'tglDaftar' => $kunjunganData['tglDaftar'],
                    'no_rkm_medis' => $dataPasien->no_rkm_medis,
                    'nm_pasien' => $dataPasien->nm_pasien,
                    'noKartu' => $kunjunganData['noKartu'],
                    'kdPoli' => $kunjunganData['kdPoli'],
                    'nmPoli' => $nmPoli,
                    'keluhan' => $kunjunganData['keluhan'],
                    'kdDokter' => $kunjunganData['kdDokter'],
                    'nmDokter' => $nmDokter,
                    'kdDiag1' => $kunjunganData['kdDiag1'],
                    'nmDiag1' => $nmDiag1,
                    'status' => 'Gagal'
                ]);
                
                $this->alert('error', $errorMessage);
                
                Log::error('Gagal mengirim kunjungan PCare', [
                    'response' => $responseData,
                    'error_message' => $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error saat proses kunjungan PCare', [
                'no_rawat' => $this->noRawat,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.ralan.pemeriksaan');
    }

    public function hydrate()
    {
        $this->getPemeriksaan();
        $this->getListPemeriksaan();
    }

    /**
     * Helper untuk mendekode no_rawat
     *
     * @param string $noRawat
     * @return string
     */
    private function decodeNoRawat($noRawat)
    {
        // Pastikan input adalah string
        if (!is_string($noRawat)) {
            $noRawat = (string)$noRawat;
        }
        
        // Bersihkan dari karakter non-printable
        $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
        
        // Jika hasil bersih kosong tapi nilai asli tidak kosong, gunakan nilai asli
        if (empty($cleanNoRawat) && !empty($noRawat)) {
            $cleanNoRawat = $noRawat;
        }
        
        // Jika tidak ada parameter atau tidak ada karakter %, kembalikan nilai yang sudah dibersihkan
        if (!$cleanNoRawat || strpos($cleanNoRawat, '%') === false) {
            return $cleanNoRawat;
        }
        
        $decodedNoRawat = $cleanNoRawat;
        $urlDecoded = urldecode($cleanNoRawat);
        
        // Coba base64 decode
        try {
            $base64Decoded = base64_decode($urlDecoded);
            if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                $decodedNoRawat = $base64Decoded;
                // Debug log dihapus untuk production
                // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode: ' . $decodedNoRawat);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mendekode no_rawat: ' . $e->getMessage());
        }
        
        return $decodedNoRawat;
    }

    public function getListPemeriksaan()
    {
        // Dekode no_rawat
        $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
    
        $this->listPemeriksaan = DB::table('pemeriksaan_ralan')
            ->join('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
            ->where(DB::raw('BINARY pemeriksaan_ralan.no_rawat'), $decodedNoRawat)
            ->select('pemeriksaan_ralan.*', 'pegawai.nama')
            ->get();
    }

    public function collapsed()
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function expanded()
    {
        $this->isMaximized = !$this->isMaximized;
    }

    public function getPemeriksaan()
    {
        // Dekode no_rawat
        $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
    
        // Sanitasi no_rm
        $cleanNoRm = $this->noRm;
        if (!is_string($cleanNoRm)) {
            $cleanNoRm = (string)$cleanNoRm;
        }
        $cleanNoRm = preg_replace('/[[:^print:]]/', '', $cleanNoRm);
    
        $data = DB::table('pasien')
            ->join('pemeriksaan_ralan', 'pasien.no_rkm_medis', '=', 'pemeriksaan_ralan.no_rawat')
            ->where('pasien.no_rkm_medis', $cleanNoRm)
            ->where('pemeriksaan_ralan.alergi', '<>', 'Tidak Ada')
            ->select('pemeriksaan_ralan.alergi')
            ->first();

        $pemeriksaan = DB::table('pemeriksaan_ralan')
            ->where(DB::raw('BINARY no_rawat'), $decodedNoRawat)
            ->orderBy('jam_rawat', 'desc')
            ->first();
            
        // Hanya isi nilai jika ada data pemeriksaan sebelumnya
        if ($pemeriksaan) {
            $this->keluhan = $pemeriksaan->keluhan;
            $this->pemeriksaan = $pemeriksaan->pemeriksaan;
            $this->penilaian = $pemeriksaan->penilaian;
            $this->instruksi = $pemeriksaan->instruksi;
            $this->rtl = $pemeriksaan->rtl;
            $this->alergi = $pemeriksaan->alergi ?? $data->alergi ?? 'Tidak Ada';
            $this->suhu = $pemeriksaan->suhu_tubuh;
            $this->berat = $pemeriksaan->berat;
            $this->tinggi = $pemeriksaan->tinggi;
            $this->tensi = $pemeriksaan->tensi;
            $this->nadi = $pemeriksaan->nadi;
            $this->respirasi = $pemeriksaan->respirasi;
            $this->evaluasi = $pemeriksaan->evaluasi;
            $this->gcs = $pemeriksaan->gcs;
            $this->kesadaran = $pemeriksaan->kesadaran;
            $this->lingkar = $pemeriksaan->lingkar_perut;
            $this->spo2 = $pemeriksaan->spo2;
        } else {
            // Reset semua nilai jika tidak ada pemeriksaan sebelumnya
            $this->keluhan = '';
            $this->pemeriksaan = '';
            $this->penilaian = '';
            $this->instruksi = '';
            $this->rtl = '';
            $this->alergi = '';
            $this->suhu = '';
            $this->berat = '';
            $this->tinggi = '';
            $this->tensi = '';
            $this->nadi = '';
            $this->respirasi = '';
            $this->evaluasi = '';
            $this->gcs = '';
            $this->kesadaran = 'Compos Mentis';
            $this->lingkar = '';
            $this->spo2 = '';
        }
    }

    public function simpanPemeriksaan()
    {
        try {
            DB::beginTransaction();
            
            // Dekode no_rawat jika perlu
            $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
            
            DB::table('pemeriksaan_ralan')
                ->insert([
                    'no_rawat' => $decodedNoRawat, // Gunakan no_rawat yang sudah didekode
                    'keluhan' => $this->keluhan ?? '-',
                    'pemeriksaan' => $this->pemeriksaan ?? '-',
                    'penilaian' => $this->penilaian ?? '-',
                    'instruksi' => $this->instruksi ?? '-',
                    'rtl' => $this->rtl ?? '-',
                    'alergi' => $this->alergi ?? '-',
                    'suhu_tubuh' => $this->suhu,
                    'berat' => $this->berat ?? '0',
                    'tinggi' => $this->tinggi ?? '0',
                    'tensi' => $this->tensi ?? '-',
                    'nadi' => $this->nadi ?? '-',
                    'respirasi' => $this->respirasi ?? '-',
                    'gcs' => $this->gcs ?? '-',
                    'kesadaran' => $this->kesadaran ?? 'Compos Mentis',
                    'lingkar_perut' => $this->lingkar ?? '0',
                    'spo2' => $this->spo2 ?? '-',
                    'evaluasi' => $this->evaluasi ?? '-',
                    'tgl_perawatan' => date('Y-m-d'),
                    'jam_rawat' => date('H:i:s'),
                    'nip' => session()->get('username'),
                ]);
            
            // Update status pasien juga menggunakan no_rawat yang sudah didekode
            DB::table('reg_periksa')
                ->where('no_rawat', $decodedNoRawat)
                ->update(['stts' => 'Sudah']);

            // Coba daftarkan ke PCare BPJS jika pasien adalah peserta BPJS
            $this->daftarPcareBpjs($decodedNoRawat);

            DB::commit();
            $this->getListPemeriksaan();
            
            // Reset form setelah penyimpanan berhasil
            $this->resetForm();
            
            $this->alert('success', 'Pemeriksaan berhasil disimpan dan status pasien telah diupdate', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
            
            $this->emit('refreshData');
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Error QueryException saat simpan pemeriksaan: ' . $ex->getMessage(), [
                'no_rawat_original' => $this->noRawat,
                'decoded' => $decodedNoRawat ?? 'not_decoded',
                'code' => $ex->getCode(),
                'sql' => $ex->getSql() ?? 'undefined'
            ]);
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($ex->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        } catch (\Exception $e) {
            DB::rollback();
            \Illuminate\Support\Facades\Log::error('Error Exception saat simpan pemeriksaan: ' . $e->getMessage());
            $this->dispatchBrowserEvent('swal:pemeriksaan', $this->toastResponse($e->getMessage() ?? 'Pemeriksaan gagal ditambahkan', 'error'));
        }
    }

    // Fungsi untuk reset form
    public function resetForm()
    {
        $this->keluhan = '';
        $this->pemeriksaan = '';
        $this->penilaian = '';
        $this->instruksi = '';
        $this->rtl = '';
        $this->alergi = '';
        $this->suhu = '';
        $this->berat = '';
        $this->tinggi = '';
        $this->tensi = '';
        $this->nadi = '';
        $this->respirasi = '';
        $this->gcs = '';
        $this->kesadaran = 'Compos Mentis';
        $this->lingkar = '';
        $this->spo2 = '';
        $this->evaluasi = '';
        
        // Emit event untuk reset form di JavaScript
        $this->dispatchBrowserEvent('formReset');
    }

    public function confirmHapus($noRawat, $tgl, $jam)
    {
        $this->noRawat = $noRawat;
        $this->tgl = $tgl;
        $this->jam = $jam;
        $this->confirm('Yakin ingin menghapus pemeriksaan ini?', [
            'toast' => false,
            'position' => 'center',
            'showConfirmButton' => true,
            'cancelButtonText' => 'Tidak',
            'onConfirmed' => 'hapusPemeriksaan',
        ]);
    }

    public function hapus()
    {
        try {
            // Dekode no_rawat jika perlu
            $decodedNoRawat = $this->decodeNoRawat($this->noRawat);
            
            DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $decodedNoRawat)
                ->where('tgl_perawatan', $this->tgl)
                ->where('jam_rawat', $this->jam)
                ->delete();
            $this->getListPemeriksaan();
            $this->alert('success', 'Pemeriksaan berhasil dihapus', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saat hapus pemeriksaan: ' . $e->getMessage(), [
                'no_rawat' => $this->noRawat,
                'decoded' => $decodedNoRawat ?? 'not_decoded',
                'tgl' => $this->tgl,
                'jam' => $this->jam
            ]);
            $this->alert('error', 'Gagal', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
                'text' =>  $e->getMessage(),
            ]);
        }
    }

    /**
     * Update status pasien menjadi "Sudah"
     * Dapat dipanggil dari komponen lain
     * 
     * @return void
     */
    public function updateStatusPasien()
    {
        try {
            DB::table('reg_periksa')
                ->where('no_rawat', $this->noRawat)
                ->update(['stts' => 'Sudah']);
            
            $this->alert('success', 'Status pasien berhasil diupdate menjadi Sudah', [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        } catch (\Exception $e) {
            $this->alert('error', 'Gagal mengupdate status pasien: ' . $e->getMessage(), [
                'position' =>  'center',
                'timer' =>  3000,
                'toast' =>  false,
            ]);
        }
    }

    /**
     * Mendaftarkan pasien ke PCare BPJS setelah pemeriksaan disimpan
     *
     * @param string $noRawat
     * @return void
     */
    private function daftarPcareBpjs($noRawat)
    {
        Log::info('=== MULAI PROSES PENDAFTARAN PCARE ===', [
            'no_rawat' => $noRawat,
            'timestamp' => now()->toDateTimeString()
        ]);
        
        try {
            // Ambil data pasien dan registrasi
            Log::info('Mengambil data pasien dari database', ['no_rawat' => $noRawat]);
            
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

            Log::info('Data pasien berhasil diambil', [
                'data_found' => $dataPasien ? 'yes' : 'no',
                'nm_pasien' => $dataPasien->nm_pasien ?? 'null',
                'kd_pj' => $dataPasien->kd_pj ?? 'null',
                'no_peserta' => $dataPasien->no_peserta ?? 'null',
                'kd_poli' => $dataPasien->kd_poli ?? 'null'
            ]);

            // Cek apakah pasien adalah peserta BPJS (BPJ, PBI, NON)
            $validBpjsTypes = ['BPJ', 'PBI', 'NON'];
            if (!$dataPasien || !in_array($dataPasien->kd_pj, $validBpjsTypes) || empty($dataPasien->no_peserta)) {
                Log::info('Pasien tidak memenuhi syarat PCare', [
                    'no_rawat' => $noRawat,
                    'reason' => !$dataPasien ? 'data_not_found' : (!in_array($dataPasien->kd_pj, $validBpjsTypes) ? 'bukan_bpjs' : 'no_peserta_kosong'),
                    'kd_pj' => $dataPasien->kd_pj ?? 'null',
                    'no_peserta' => $dataPasien->no_peserta ?? 'null',
                    'valid_types' => $validBpjsTypes
                ]);
                return;
            }

            // Cek apakah sudah terdaftar di PCare hari ini
            Log::info('Mengecek duplikasi pendaftaran PCare', [
                'no_rawat' => $noRawat,
                'tgl_check' => date('Y-m-d')
            ]);
            
            $cekPcare = DB::table('pcare_pendaftaran')
                ->where('no_rawat', $noRawat)
                ->where('tglDaftar', date('Y-m-d'))
                ->first();

            if ($cekPcare) {
                Log::info('Pasien sudah terdaftar di PCare hari ini', [
                    'no_rawat' => $noRawat,
                    'existing_record' => $cekPcare
                ]);
                return;
            }
            
            Log::info('Tidak ada duplikasi, melanjutkan pendaftaran');

            // Ambil mapping poli dari database
            Log::info('Mengambil mapping poli PCare', ['kd_poli_rs' => $dataPasien->kd_poli]);
            $kdPoliPcare = $this->getKdPoliPcare($dataPasien->kd_poli);
            Log::info('Mapping poli berhasil', ['kd_poli_pcare' => $kdPoliPcare]);

            // Persiapkan vital signs dari hasil pemeriksaan
            Log::info('Memproses vital signs', [
                'tensi_input' => $this->tensi,
                'berat' => $this->berat,
                'tinggi' => $this->tinggi,
                'respirasi' => $this->respirasi,
                'nadi' => $this->nadi,
                'lingkar' => $this->lingkar
            ]);
            
            $sistole = 120;
            $diastole = 80;
            if (!empty($this->tensi) && strpos($this->tensi, '/') !== false) {
                $tensiParts = explode('/', $this->tensi);
                $sistole = (int)trim($tensiParts[0]) ?: 120;
                $diastole = (int)trim($tensiParts[1]) ?: 80;
            }
            
            Log::info('Vital signs diproses', [
                'sistole' => $sistole,
                'diastole' => $diastole
            ]);

            // Persiapkan data untuk PCare sesuai katalog BPJS
            $pcareData = [
                'kdProviderPeserta' => env('BPJS_PCARE_KODE_PPK', '11251919'),
                'tglDaftar' => date('d-m-Y'),
                'noKartu' => $dataPasien->no_peserta,
                'kdPoli' => $kdPoliPcare,
                'keluhan' => $this->keluhan ?: null,
                'kunjSakit' => true,
                'sistole' => $sistole,
                'diastole' => $diastole,
                'beratBadan' => (float)($this->berat ?: 0),
                'tinggiBadan' => (float)($this->tinggi ?: 0),
                'respRate' => (int)($this->respirasi ?: 0),
                'lingkarPerut' => (float)($this->lingkar ?: 0),
                'heartRate' => (int)($this->nadi ?: 0),
                'rujukBalik' => 0,
                'kdTkp' => '10'
            ];
            
            Log::info('Data PCare telah disiapkan', [
                'pcare_data' => $pcareData
            ]);

            // Validasi data sebelum dikirim
            if (empty($pcareData['noKartu']) || empty($pcareData['kdPoli'])) {
                Log::error('Data tidak lengkap untuk pendaftaran PCare', [
                    'no_rawat' => $noRawat,
                    'noKartu' => $pcareData['noKartu'],
                    'kdPoli' => $pcareData['kdPoli'],
                    'validation_failed' => true
                ]);
                return;
            }
            
            Log::info('Validasi data berhasil, melanjutkan ke API call');

            Log::info('Mengirim data pendaftaran ke PCare menggunakan PcareTrait', [
                'no_rawat' => $noRawat,
                'data' => $pcareData
            ]);
            
            // Kirim request menggunakan PcareTrait yang sudah diperbaiki
            $responseData = $this->requestPcare('pendaftaran', 'POST', $pcareData, 'text/plain');

            Log::info('Response dari PCare API diterima', [
                'status_code' => $responseData['metaData']['code'] ?? 'unknown',
                'response_body' => json_encode($responseData),
                'response_headers' => []
            ]);
            
            Log::info('Response data parsed', [
                'response_data' => $responseData,
                'is_successful' => isset($responseData['metaData']['code']) && $responseData['metaData']['code'] == 201,
                'metadata_code' => $responseData['metaData']['code'] ?? 'not_set'
            ]);
            
            if (isset($responseData['metaData']['code']) && $responseData['metaData']['code'] == 201) {
                // Simpan data pendaftaran ke database lokal
                $this->simpanDataPendaftaranPcare($noRawat, $pcareData, $responseData);
                
                // Ambil nomor urut dari response sesuai format BPJS
                $noUrut = $responseData['response']['message'] ?? null;
                
                Log::info('=== PENDAFTARAN PCARE BERHASIL ===', [
                    'no_rawat' => $noRawat,
                    'noUrut' => $noUrut,
                    'full_response' => $responseData,
                    'success' => true
                ]);
                
                // Tampilkan notifikasi sukses ke user
                $this->dispatchBrowserEvent('toast', [
                    'type' => 'success',
                    'message' => 'Pasien berhasil didaftarkan ke PCare BPJS dengan nomor urut: ' . ($noUrut ?? 'N/A')
                ]);
                
            } else {
                // Log error response dari PCare
                $errorMessage = $responseData['metaData']['message'] ?? 'Unknown error';
                Log::error('=== PENDAFTARAN PCARE GAGAL ===', [
                    'no_rawat' => $noRawat,
                    'status_code' => $responseData['metaData']['code'] ?? 'unknown',
                    'error_message' => $errorMessage,
                    'full_response' => $responseData,
                    'request_data' => $pcareData,
                    'success' => false
                ]);
                
                // Tampilkan notifikasi warning ke user
                $this->dispatchBrowserEvent('toast', [
                    'type' => 'warning',
                    'message' => 'Pendaftaran PCare gagal: ' . $errorMessage
                ]);
            }

        } catch (\Exception $e) {
            Log::error('=== EXCEPTION PADA PENDAFTARAN PCARE ===', [
                'no_rawat' => $noRawat,
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'exception_type' => get_class($e)
            ]);
            
            // Tampilkan notifikasi error ke user
            $this->dispatchBrowserEvent('toast', [
                'type' => 'error',
                'message' => 'Terjadi kesalahan saat mendaftarkan ke PCare: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Simpan data pendaftaran PCare ke database lokal
     *
     * @param string $noRawat
     * @param array $pcareData
     * @param array $responseData
     * @return void
     */
    private function simpanDataPendaftaranPcare($noRawat, $pcareData, $responseData)
    {
        try {
            // Ambil data pasien untuk melengkapi informasi
            $dataPasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'poliklinik.nm_poli'
                )
                ->first();

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
                'noUrut' => $responseData['response']['message'] ?? null,
                'status' => 'Terkirim'
            ]);
            
            Log::info('Data pendaftaran PCare berhasil disimpan ke database lokal', [
                'no_rawat' => $noRawat,
                'noUrut' => $responseData['response']['message'] ?? null
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error saat menyimpan data pendaftaran PCare ke database', [
                'no_rawat' => $noRawat,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
     * Ambil kode poli PCare dari database mapping
     *
     * @param string $kdPoli
     * @return string
     */
    private function getKdPoliPcare($kdPoli)
    {
        // Ambil kode poli PCare dari tabel mapping
        $mapping = DB::table('maping_poliklinik_pcare')
            ->where('kd_poli_rs', $kdPoli)
            ->first();

        if ($mapping && !empty($mapping->kd_poli_pcare)) {
            return $mapping->kd_poli_pcare;
        }

        // Default ke '001' jika tidak ditemukan mapping
        Log::warning('Mapping poli PCare tidak ditemukan', [
            'kd_poli' => $kdPoli
        ]);
        
        return '001';
    }
}
