<?php

namespace App\Http\Livewire\Ralan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class PemeriksaanANC extends Component
{
    // Data input dari form
    public $noRawat;
    public $noRm;
    public $tanggal_anc;
    public $diperiksa_oleh;
    public $usia_kehamilan;
    public $trimester;
    public $kunjungan_ke;
    public $berat_badan;
    public $tinggi_badan;
    public $imt;
    public $kategori_imt;
    public $jumlah_janin;
    public $td_sistole;
    public $td_diastole;
    public $jumlah_fe;
    public $dosis;
    public $pemeriksaan_lab;
    public $jenis_tatalaksana;
    public $materi;
    public $rekomendasi;
    public $konseling_menyusui;
    public $tanda_bahaya_kehamilan;
    public $tanda_bahaya_persalinan;
    public $konseling_phbs;
    public $konseling_gizi;
    public $konseling_ibu_hamil;
    public $konseling_lainnya;
    public $keadaan_pulang;
    
    // Variabel baru untuk anamnesis dan ANC terpadu
    public $keluhan_utama;
    public $gravida;
    public $partus;
    public $abortus;
    public $hidup;
    public $riwayat_penyakit = [];
    public $lila;
    public $status_gizi;
    public $tfu;
    public $taksiran_berat_janin;
    public $djj;
    public $presentasi;
    public $status_tt;
    public $tanggal_imunisasi;
    public $tanggal_lab;
    public $lab = [];
    public $rujukan_ims;
    public $tindak_lanjut;
    public $detail_tindak_lanjut;
    public $tanggal_kunjungan_berikutnya;
    
    // Data ibu hamil dari tabel data_ibu_hamil
    public $id_hamil = null;
    public $nama_ibu = null;
    public $usia_ibu = null;
    public $hpht = null;
    public $hpl = null;
    public $usia_kehamilan_saat_ini = null;
    
    // Properti untuk form Anemia
    public $diberikan_tablet_fe = null;
    public $jumlah_tablet_dikonsumsi = 0;
    public $jumlah_tablet_ditambahkan = 0;
    public $tatalaksana_lainnya = null;
    
    // Properti untuk form Makanan Tambahan Ibu Hamil
    public $pemberian_mt = null;
    public $jumlah_mt = 0;
    
    // Properti untuk form Hipertensi
    public $pantau_tekanan_darah = null;
    public $pantau_protein_urine = null;
    public $pantau_kondisi_janin = null;
    public $hipertensi_lainnya = null;

    // Properti untuk form Eklampsia
    public $pantau_tekanan_darah_eklampsia = null;
    public $pantau_protein_urine_eklampsia = null;
    public $pantau_kondisi_janin_eklampsia = null;
    public $pemberian_antihipertensi = null;
    public $pemberian_mgso4 = null;
    public $pemberian_diazepam = null;
    
    // Properti untuk form KEK
    public $edukasi_gizi = null;
    public $kek_lainnya = null;
    
    // Properti untuk form Obesitas
    public $edukasi_gizi_obesitas = null;
    public $obesitas_lainnya = null;
    
    // Properti untuk form Infeksi
    public $pemberian_antipiretik = null;
    public $pemberian_antibiotik = null;
    public $infeksi_lainnya = null;

    // Properti untuk form Penyakit Jantung
    public $edukasi = null;
    public $jantung_lainnya = null;
    
    // Properti untuk form HIV
    public $datang_dengan_hiv = null;
    public $persalinan_pervaginam = null;
    public $persalinan_perapdoinam = null;
    public $ditawarkan_tes = null;
    public $dilakukan_tes = null;
    public $hasil_tes_hiv = null;
    public $mendapatkan_art = null;
    public $vct_pict = null;
    public $periksa_darah = null;
    public $serologi = null;
    public $arv_profilaksis = null;
    public $hiv_lainnya = null;
    
    // Properti untuk form TB
    public $diperiksa_dahak = null;
    public $tbc = null;
    public $obat_tb = null;
    public $sisa_obat = null;
    public $tb_lainnya = null;
    
    // Properti untuk form Malaria
    public $diberikan_kelambu = null;
    public $darah_malaria_rdt = null;
    public $darah_malaria_mikroskopis = null;
    public $ibu_hamil_malaria_rdt = null;
    public $ibu_hamil_malaria_mikroskopis = null;
    public $hasil_test_malaria = null;
    public $obat_malaria = null;
    public $malaria_lainnya = null;

    // Variabel untuk tracking
    public $pemeriksaanId = null;
    public $isEdit = false;
    public $errorMessage = null;
    public $validIbuHamil = false;

    protected $listeners = ['editPemeriksaanANC' => 'edit'];

    public function mount($noRawat = null, $noRm = null)
    {
        try {
            \Log::info('PemeriksaanANC mount started', [
                'noRawat' => $noRawat,
                'noRm' => $noRm,
                'authCheck' => auth()->check(),
                'userId' => auth()->id()
            ]);

            // Set nilai default
            $this->noRawat = $noRawat;
            $this->noRm = $noRm;
            $this->tanggal_anc = now()->format('Y-m-d H:i:s');
            
            // Ambil nama petugas dari tabel petugas dengan kd_jbtn j008
            $petugas = DB::table('petugas')
            ->where([
                ['kd_jbtn', '=', 'j008'],
                ['status', '=', '1']
            ])
            ->orderBy('nama', 'asc')
            ->get();
            
            // Jika ditemukan petugas, gunakan nama petugas yang login jika ada, atau default ke petugas pertama
            if ($petugas->count() > 0) {
                // Coba cari petugas yang namanya cocok dengan user yang login
                if (auth()->check() && auth()->user()->name) {
                    $petugasLogin = $petugas->first(function($item) {
                        return strtolower($item->nama) == strtolower(auth()->user()->name);
                    });
                    
                    $this->diperiksa_oleh = $petugasLogin ? $petugasLogin->nama : $petugas->first()->nama;
                } else {
                    $this->diperiksa_oleh = $petugas->first()->nama;
                }
            } else {
                // Jika tidak ada petugas dengan kd_jbtn j008, gunakan nama user yang login atau string kosong
                $this->diperiksa_oleh = auth()->check() ? auth()->user()->name : '';
            }
            
            $this->jumlah_fe = 0;
            $this->dosis = 0;
            
            // Nilai default untuk form anemia
            $this->jumlah_tablet_dikonsumsi = 0;
            $this->jumlah_tablet_ditambahkan = 0;

            // Verifikasi data pasien
            if ($this->noRm) {
                try {
                    // Cek apakah pasien terdaftar sebagai ibu hamil aktif
                    $dataIbuHamil = DB::table('data_ibu_hamil')
                        ->where('no_rkm_medis', $this->noRm)
                        ->where('status', 'Hamil')
                        ->first();

                    if ($dataIbuHamil) {
                        $this->validIbuHamil = true;
                        $this->id_hamil = $dataIbuHamil->id_hamil;
                        $this->nama_ibu = $dataIbuHamil->nama;
                        
                        // Konversi usia ibu dari string ke numeric (jika diperlukan)
                        if (isset($dataIbuHamil->usia_ibu)) {
                            $this->usia_ibu = $dataIbuHamil->usia_ibu;
                        } else {
                            // Hitung usia berdasarkan tanggal lahir jika usia_ibu tidak ada
                            if ($dataIbuHamil->tgl_lahir) {
                                $birthDate = new \DateTime($dataIbuHamil->tgl_lahir);
                                $today = new \DateTime('today');
                                $this->usia_ibu = $birthDate->diff($today)->y;
                            }
                        }
                        
                        $this->hpht = $dataIbuHamil->hari_pertama_haid ? date('d-m-Y', strtotime($dataIbuHamil->hari_pertama_haid)) : null;
                        $this->hpl = $dataIbuHamil->hari_perkiraan_lahir ? date('d-m-Y', strtotime($dataIbuHamil->hari_perkiraan_lahir)) : null;
                        
                        // Hitung usia kehamilan saat ini jika HPHT ada
                        if ($dataIbuHamil->hari_pertama_haid) {
                            $hpht = new \DateTime($dataIbuHamil->hari_pertama_haid);
                            $today = new \DateTime('today');
                            $diff = $today->diff($hpht);
                            // Konversi total hari ke minggu
                            $totalDays = $diff->days;
                            $weeks = floor($totalDays / 7);
                            $this->usia_kehamilan_saat_ini = $weeks . ' minggu';
                            $this->usia_kehamilan = $weeks;

                            // Set trimester berdasarkan usia kehamilan
                            if ($weeks <= 12) {
                                $this->trimester = '1';
                            } elseif ($weeks <= 24) {
                                $this->trimester = '2';
                            } else {
                                $this->trimester = '3';
                            }
                        }
                        
                        // Ambil data riwayat obstetri
                        $this->gravida = $dataIbuHamil->kehamilan_ke ?? 0;
                        $this->partus = $dataIbuHamil->jumlah_anak_hidup ?? 0;
                        $this->abortus = $dataIbuHamil->riwayat_keguguran ?? 0;
                        $this->hidup = $dataIbuHamil->jumlah_anak_hidup ?? 0;
                        
                    } else {
                        $this->validIbuHamil = false;
                        $this->errorMessage = "Pasien dengan nomor RM {$this->noRm} tidak terdaftar sebagai ibu hamil aktif di sistem. Silakan daftarkan terlebih dahulu.";
                    }
                } catch (\Exception $e) {
                    \Log::error('Error checking patient registration as pregnant', [
                        'noRm' => $this->noRm,
                        'error' => $e->getMessage()
                    ]);
                    $this->validIbuHamil = false;
                    $this->errorMessage = "Terjadi kesalahan saat memeriksa data ibu hamil. " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error in PemeriksaanANC mount', [
                'noRawat' => $noRawat,
                'noRm' => $noRm,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
        
        \Log::info('PemeriksaanANC mount completed', [
            'noRawat' => $noRawat,
            'noRm' => $noRm,
            'diperiksa_oleh' => $this->diperiksa_oleh,
            'valid_ibu_hamil' => $this->validIbuHamil
        ]);
    }

    /**
     * Hook Livewire untuk memproses nilai sebelum validasi
     */
    public function prepareForValidation($attributes)
    {
        $this->formatTanggalANC();
        
        // Selalu tambahkan tanggal_anc ke atribut yang akan divalidasi
        if (isset($this->tanggal_anc)) {
            $attributes['tanggal_anc'] = $this->tanggal_anc;
        }
        
        return $attributes;
    }
    
    /**
     * Property untuk validasi
     */
    protected $rules = [
        'tanggal_anc' => 'required|date',
        'diperiksa_oleh' => 'required|string',
        'usia_kehamilan' => 'required|numeric|min:1|max:45',
        'trimester' => 'required|in:1,2,3',
        'kunjungan_ke' => 'required|in:1,2,3,4,5,6',
        'berat_badan' => 'required|numeric|min:20|max:200',
        'tinggi_badan' => 'required|numeric|min:100|max:200',
        'imt' => 'nullable|numeric',
        'kategori_imt' => 'nullable|string',
        'jumlah_janin' => 'required|string',
        'td_sistole' => 'required|numeric|min:50|max:200',
        'td_diastole' => 'required|numeric|min:30|max:150',
        'lila' => 'required|numeric|min:10|max:50',
        'status_gizi' => 'nullable|string',
        'tfu' => 'nullable|numeric|min:0|max:40',
        'taksiran_berat_janin' => 'nullable|numeric',
        'djj' => 'nullable|numeric|min:100|max:200',
        'presentasi' => 'nullable|string',
        'status_tt' => 'nullable|string',
        'tanggal_imunisasi' => 'nullable|date',
        'jumlah_fe' => 'nullable|numeric|min:0',
        'dosis' => 'nullable|numeric|min:0',
        'tanggal_lab' => 'nullable|date',
        'lab' => 'nullable',
        'rujukan_ims' => 'nullable|string',
        'jenis_tatalaksana' => 'nullable|string',
        'materi' => 'required|string',
        'rekomendasi' => 'required|string',
        'konseling_menyusui' => 'required|string',
        'tanda_bahaya_kehamilan' => 'required|string',
        'tanda_bahaya_persalinan' => 'nullable|string',
        'konseling_phbs' => 'nullable|string',
        'konseling_gizi' => 'nullable|string',
        'konseling_ibu_hamil' => 'nullable|string',
        'konseling_lainnya' => 'nullable|string',
        'keadaan_pulang' => 'required|string',
        'tindak_lanjut' => 'nullable|string',
        'detail_tindak_lanjut' => 'nullable|string',
        'tanggal_kunjungan_berikutnya' => 'nullable|date',
    ];
    
    /**
     * Format tanggal ANC ke format yang benar
     */
    protected function formatTanggalANC()
    {
        if (!empty($this->tanggal_anc)) {
            try {
                // Mencoba parse tanggal ke format yang diharapkan oleh sistem
                $date = \Carbon\Carbon::parse($this->tanggal_anc);
                
                // Jika berhasil diparse, simpan dalam format ISO
                $this->tanggal_anc = $date->format('Y-m-d H:i:s');
                
                // Log untuk debugging
                \Log::info('Tanggal ANC diformat ke: ' . $this->tanggal_anc);
                
            } catch (\Exception $e) {
                \Log::error('Error formatting tanggal_anc: ' . $e->getMessage(), [
                    'input' => $this->tanggal_anc
                ]);
            }
        }
    }
    
    /**
     * Hook saat ada nilai yang diupdate
     */
    public function updated($propertyName)
    {
        // Jika tanggal ANC yang diupdate, format nilai tersebut
        if ($propertyName === 'tanggal_anc') {
            \Log::info('Tanggal ANC diupdate dengan nilai: ' . $this->tanggal_anc);
            $this->formatTanggalANC();
        }
        
        // Validasi properti yang diupdate
        $this->validateOnly($propertyName);
    }
    
    public function hitungIMT()
    {
        if ($this->berat_badan && $this->tinggi_badan) {
            // Konversi tinggi badan dari cm ke m
            $tinggi_m = $this->tinggi_badan / 100;
            
            // Hitung IMT
            $imt = $this->berat_badan / ($tinggi_m * $tinggi_m);
            $this->imt = round($imt, 2);
            
            // Tentukan kategori IMT
            if ($this->imt < 18.5) {
                $this->kategori_imt = 'KURUS';
            } elseif ($this->imt >= 18.5 && $this->imt <= 24.9) {
                $this->kategori_imt = 'NORMAL';
            } elseif ($this->imt >= 25 && $this->imt <= 29.9) {
                $this->kategori_imt = 'GEMUK';
            } else {
                $this->kategori_imt = 'OBESITAS';
            }
        }
    }

    public function save()
    {
        try {
            // Validasi jika pasien terdaftar sebagai ibu hamil
            if (!$this->validIbuHamil) {
                session()->flash('error', 'Pasien belum terdaftar sebagai ibu hamil aktif. Data tidak dapat disimpan.');
                return;
            }
            
            $validatedData = $this->validate();
            
            if (empty($this->noRawat) || empty($this->noRm)) {
                session()->flash('error', 'No Rawat dan No RM tidak boleh kosong');
                return;
            }
            
            DB::beginTransaction();
            
            // Persiapkan data untuk disimpan
            $data = [
                'no_rawat' => $this->noRawat,
                'no_rkm_medis' => $this->noRm,
                'id_hamil' => $this->id_hamil,
                'tanggal_anc' => $this->tanggal_anc,
                'diperiksa_oleh' => $this->diperiksa_oleh,
                'usia_kehamilan' => $this->usia_kehamilan,
                'trimester' => $this->trimester,
                'kunjungan_ke' => $this->kunjungan_ke,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'imt' => $this->imt,
                'kategori_imt' => $this->kategori_imt,
                'jumlah_janin' => $this->jumlah_janin,
                'td_sistole' => $this->td_sistole,
                'td_diastole' => $this->td_diastole,
                'jumlah_fe' => $this->jumlah_fe,
                'dosis' => $this->dosis,
                'pemeriksaan_lab' => $this->pemeriksaan_lab,
                'jenis_tatalaksana' => $this->jenis_tatalaksana,
                'materi' => $this->materi,
                'rekomendasi' => $this->rekomendasi,
                'konseling_menyusui' => $this->konseling_menyusui,
                'tanda_bahaya_kehamilan' => $this->tanda_bahaya_kehamilan,
                'tanda_bahaya_persalinan' => $this->tanda_bahaya_persalinan,
                'konseling_phbs' => $this->konseling_phbs,
                'konseling_gizi' => $this->konseling_gizi,
                'konseling_ibu_hamil' => $this->konseling_ibu_hamil,
                'konseling_lainnya' => $this->konseling_lainnya,
                'keadaan_pulang' => $this->keadaan_pulang,
                // Data baru untuk anamnesis dan ANC terpadu
                'keluhan_utama' => $this->keluhan_utama,
                'gravida' => $this->gravida,
                'partus' => $this->partus,
                'abortus' => $this->abortus,
                'hidup' => $this->hidup,
                'riwayat_penyakit' => $this->riwayat_penyakit,
                'lila' => $this->lila,
                'status_gizi' => $this->status_gizi,
                'tfu' => $this->tfu,
                'taksiran_berat_janin' => $this->taksiran_berat_janin,
                'djj' => $this->djj,
                'presentasi' => $this->presentasi,
                'status_tt' => $this->status_tt,
                'tanggal_imunisasi' => $this->tanggal_imunisasi,
                'tanggal_lab' => $this->tanggal_lab,
                'lab' => $this->lab,
                'rujukan_ims' => $this->rujukan_ims,
                'tindak_lanjut' => $this->tindak_lanjut,
                'detail_tindak_lanjut' => $this->detail_tindak_lanjut,
                'tanggal_kunjungan_berikutnya' => $this->tanggal_kunjungan_berikutnya,
            ];
            
            // Tambahkan data anemia jika jenis tatalaksana adalah Anemia
            if ($this->jenis_tatalaksana === 'Anemia') {
                $data['diberikan_tablet_fe'] = $this->diberikan_tablet_fe;
                $data['jumlah_tablet_dikonsumsi'] = $this->jumlah_tablet_dikonsumsi;
                $data['jumlah_tablet_ditambahkan'] = $this->jumlah_tablet_ditambahkan;
                $data['tatalaksana_lainnya'] = $this->tatalaksana_lainnya;
            }
            
            // Tambahkan data MT jika jenis tatalaksana adalah MT
            if ($this->jenis_tatalaksana === 'Makanan Tambahan Ibu Hamil') {
                $data['pemberian_mt'] = $this->pemberian_mt;
                $data['jumlah_mt'] = $this->jumlah_mt;
            }
            
            // Tambahkan data Hipertensi jika jenis tatalaksana adalah Hipertensi
            if ($this->jenis_tatalaksana === 'Hipertensi') {
                $data['pantau_tekanan_darah'] = $this->pantau_tekanan_darah;
                $data['pantau_protein_urine'] = $this->pantau_protein_urine;
                $data['pantau_kondisi_janin'] = $this->pantau_kondisi_janin;
                $data['hipertensi_lainnya'] = $this->hipertensi_lainnya;
            }
            
            // Tambahkan data Eklampsia jika jenis tatalaksana adalah Eklampsia
            if ($this->jenis_tatalaksana === 'Eklampsia') {
                $data['pantau_tekanan_darah_eklampsia'] = $this->pantau_tekanan_darah_eklampsia;
                $data['pantau_protein_urine_eklampsia'] = $this->pantau_protein_urine_eklampsia;
                $data['pantau_kondisi_janin_eklampsia'] = $this->pantau_kondisi_janin_eklampsia;
                $data['pemberian_antihipertensi'] = $this->pemberian_antihipertensi;
                $data['pemberian_mgso4'] = $this->pemberian_mgso4;
                $data['pemberian_diazepam'] = $this->pemberian_diazepam;
            }
            
            // Tambahkan data KEK jika jenis tatalaksana adalah KEK
            if ($this->jenis_tatalaksana === 'KEK') {
                $data['edukasi_gizi'] = $this->edukasi_gizi;
                $data['kek_lainnya'] = $this->kek_lainnya;
            }
            
            // Tambahkan data Obesitas jika jenis tatalaksana adalah Obesitas
            if ($this->jenis_tatalaksana === 'Obesitas') {
                $data['edukasi_gizi_obesitas'] = $this->edukasi_gizi_obesitas;
                $data['obesitas_lainnya'] = $this->obesitas_lainnya;
            }
            
            // Tambahkan data Infeksi jika jenis tatalaksana adalah Infeksi
            if ($this->jenis_tatalaksana === 'Infeksi') {
                $data['pemberian_antipiretik'] = $this->pemberian_antipiretik;
                $data['pemberian_antibiotik'] = $this->pemberian_antibiotik;
                $data['infeksi_lainnya'] = $this->infeksi_lainnya;
            }
            
            // Tambahkan data Penyakit Jantung jika jenis tatalaksana adalah Penyakit Jantung
            if ($this->jenis_tatalaksana === 'Penyakit Jantung') {
                $data['edukasi'] = $this->edukasi;
                $data['jantung_lainnya'] = $this->jantung_lainnya;
            }
            
            // Tambahkan data HIV jika jenis tatalaksana adalah HIV
            if ($this->jenis_tatalaksana === 'HIV') {
                $data['datang_dengan_hiv'] = $this->datang_dengan_hiv;
                $data['persalinan_pervaginam'] = $this->persalinan_pervaginam;
                $data['persalinan_perapdoinam'] = $this->persalinan_perapdoinam;
                $data['ditawarkan_tes'] = $this->ditawarkan_tes;
                $data['dilakukan_tes'] = $this->dilakukan_tes;
                $data['hasil_tes_hiv'] = $this->hasil_tes_hiv;
                $data['mendapatkan_art'] = $this->mendapatkan_art;
                $data['vct_pict'] = $this->vct_pict;
                $data['periksa_darah'] = $this->periksa_darah;
                $data['serologi'] = $this->serologi;
                $data['arv_profilaksis'] = $this->arv_profilaksis;
                $data['hiv_lainnya'] = $this->hiv_lainnya;
            }
            
            // Tambahkan data TB jika jenis tatalaksana adalah TB
            if ($this->jenis_tatalaksana === 'TB') {
                $data['diperiksa_dahak'] = $this->diperiksa_dahak;
                $data['tbc'] = $this->tbc;
                $data['obat_tb'] = $this->obat_tb;
                $data['sisa_obat'] = $this->sisa_obat;
                $data['tb_lainnya'] = $this->tb_lainnya;
            }
            
            // Tambahkan data Malaria jika jenis tatalaksana adalah Malaria
            if ($this->jenis_tatalaksana === 'Malaria') {
                $data['diberikan_kelambu'] = $this->diberikan_kelambu;
                $data['darah_malaria_rdt'] = $this->darah_malaria_rdt;
                $data['darah_malaria_mikroskopis'] = $this->darah_malaria_mikroskopis;
                $data['ibu_hamil_malaria_rdt'] = $this->ibu_hamil_malaria_rdt;
                $data['ibu_hamil_malaria_mikroskopis'] = $this->ibu_hamil_malaria_mikroskopis;
                $data['hasil_test_malaria'] = $this->hasil_test_malaria;
                $data['obat_malaria'] = $this->obat_malaria;
                $data['malaria_lainnya'] = $this->malaria_lainnya;
            }
            
            // Gunakan model PemeriksaanAnc untuk save/update
            if ($this->isEdit && $this->pemeriksaanId) {
                // Update data
                $pemeriksaan = \App\Models\PemeriksaanAnc::findOrFail($this->pemeriksaanId);
                $pemeriksaan->update($data);
                
                $message = 'Pemeriksaan ANC berhasil diperbarui';
            } else {
                // Buat data baru
                \App\Models\PemeriksaanAnc::create($data);
                
                $message = 'Pemeriksaan ANC berhasil disimpan';
            }
            
            DB::commit();
            
            $this->resetForm();
            session()->flash('success', $message);
            $this->emit('formSaved');
            
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving ANC data', [
                'error' => $e->getMessage(),
                'noRawat' => $this->noRawat
            ]);
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
            $this->emit('showError', $e->getMessage());
        }
    }
    
    public function delete($id)
    {
        try {
            // Gunakan model untuk menghapus data berdasarkan id_anc
            \App\Models\PemeriksaanAnc::where('id_anc', $id)->delete();
                
            session()->flash('success', 'Pemeriksaan ANC berhasil dihapus');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    public function resetForm()
    {
        // Reset semua field
        $this->reset([
            'tanggal_anc', 
            'usia_kehamilan', 
            'trimester', 
            'kunjungan_ke', 
            'berat_badan', 
            'tinggi_badan', 
            'imt', 
            'kategori_imt', 
            'jumlah_janin',
            'td_sistole', 
            'td_diastole', 
            'jumlah_fe', 
            'dosis',
            'pemeriksaan_lab', 
            'jenis_tatalaksana', 
            'materi', 
            'rekomendasi',
            'konseling_menyusui', 
            'tanda_bahaya_kehamilan', 
            'tanda_bahaya_persalinan',
            'konseling_phbs', 
            'konseling_gizi', 
            'konseling_ibu_hamil',
            'konseling_lainnya', 
            'keadaan_pulang',
            // Variabel baru untuk anamnesis dan ANC terpadu
            'keluhan_utama',
            'gravida',
            'partus',
            'abortus',
            'hidup',
            'riwayat_penyakit',
            'lila',
            'status_gizi',
            'tfu',
            'taksiran_berat_janin',
            'djj',
            'presentasi',
            'status_tt',
            'tanggal_imunisasi',
            'tanggal_lab',
            'lab',
            'rujukan_ims',
            'tindak_lanjut',
            'detail_tindak_lanjut',
            'tanggal_kunjungan_berikutnya',
            // Reset form anemia
            'diberikan_tablet_fe',
            'jumlah_tablet_dikonsumsi',
            'jumlah_tablet_ditambahkan',
            'tatalaksana_lainnya',
            // Reset form MT
            'pemberian_mt',
            'jumlah_mt',
            // Reset form Hipertensi
            'pantau_tekanan_darah',
            'pantau_protein_urine',
            'pantau_kondisi_janin',
            'hipertensi_lainnya',
            
            // Reset form Eklampsia
            'pantau_tekanan_darah_eklampsia',
            'pantau_protein_urine_eklampsia',
            'pantau_kondisi_janin_eklampsia',
            'pemberian_antihipertensi',
            'pemberian_mgso4',
            'pemberian_diazepam',
            
            // Reset form KEK
            'edukasi_gizi',
            'kek_lainnya',
            
            // Reset form Obesitas
            'edukasi_gizi_obesitas',
            'obesitas_lainnya',
            
            // Reset form Infeksi
            'pemberian_antipiretik',
            'pemberian_antibiotik',
            'infeksi_lainnya',
            
            // Reset form Penyakit Jantung
            'edukasi',
            'jantung_lainnya',
            
            // Reset form HIV
            'datang_dengan_hiv',
            'persalinan_pervaginam',
            'persalinan_perapdoinam',
            'ditawarkan_tes',
            'dilakukan_tes',
            'hasil_tes_hiv',
            'mendapatkan_art',
            'vct_pict',
            'periksa_darah',
            'serologi',
            'arv_profilaksis',
            'hiv_lainnya',
            
            // Reset form TB
            'diperiksa_dahak',
            'tbc',
            'obat_tb',
            'sisa_obat',
            'tb_lainnya',
            
            // Reset form Malaria
            'diberikan_kelambu',
            'darah_malaria_rdt',
            'darah_malaria_mikroskopis',
            'ibu_hamil_malaria_rdt',
            'ibu_hamil_malaria_mikroskopis',
            'hasil_test_malaria',
            'obat_malaria',
            'malaria_lainnya'
        ]);
        
        $this->isEdit = false;
        $this->pemeriksaanId = null;
        
        // Reset kembali nilai default
        $this->tanggal_anc = Carbon::now()->format('Y-m-d H:i:s');
        $this->jumlah_tablet_dikonsumsi = 0;
        $this->jumlah_tablet_ditambahkan = 0;
        
        // Ambil kembali nama petugas dari database
        try {
            $petugas = DB::table('petugas')
                ->where([
                    ['kd_jbtn', '=', 'j008'],
                    ['status', '=', '1']
                ])
                ->orderBy('nama', 'asc')
                ->get();
            
            if ($petugas->count() > 0) {
                if (auth()->check() && auth()->user()->name) {
                    $petugasLogin = $petugas->first(function($item) {
                        return strtolower($item->nama) == strtolower(auth()->user()->name);
                    });
                    
                    $this->diperiksa_oleh = $petugasLogin ? $petugasLogin->nama : $petugas->first()->nama;
                } else {
                    $this->diperiksa_oleh = $petugas->first()->nama;
                }
            } else {
                $this->diperiksa_oleh = auth()->check() ? auth()->user()->name : '';
            }
        } catch (\Exception $e) {
            \Log::error('Error loading petugas data in resetForm', [
                'error' => $e->getMessage()
            ]);
            $this->diperiksa_oleh = auth()->check() ? auth()->user()->name : '';
        }
        
        $this->jumlah_fe = 0;
        $this->dosis = 0;
    }
    
    public function batal()
    {
        $this->resetForm();
    }
    
    public function render()
    {
        $riwayat = collect([]);
        $petugas = collect([]);
        $riwayatByIdHamil = collect([]);
        
        try {
            // Ambil data petugas dengan kd_jbtn j008
            $petugas = DB::table('petugas')
                ->where([
                    ['kd_jbtn', '=', 'j008'],
                    ['status', '=', '1']
                ])
                ->orderBy('nama', 'asc')
                ->get();
            
            if ($this->noRawat) {
                $riwayat = \App\Models\PemeriksaanAnc::where('no_rawat', $this->noRawat)
                    ->orderBy('tanggal_anc', 'desc')
                    ->get();
            }
            
            // Jika id_hamil tersedia, ambil riwayat berdasarkan id_hamil
            if ($this->id_hamil) {
                $riwayatByIdHamil = \App\Models\PemeriksaanAnc::where('id_hamil', $this->id_hamil)
                    ->orderBy('tanggal_anc', 'desc')
                    ->get();
                
                // Kelompokkan riwayat berdasarkan bulan dari tanggal_anc
                $riwayatByMonth = $riwayatByIdHamil->groupBy(function($item) {
                    return Carbon::parse($item->tanggal_anc)->format('Y-m');
                });
            }
        } catch (\Exception $e) {
            \Log::error('Error loading data', [
                'error' => $e->getMessage(),
                'noRawat' => $this->noRawat
            ]);
        }
        
        return view('livewire.ralan.pemeriksaan-anc', [
            'riwayat' => $riwayat,
            'petugas' => $petugas,
            'validIbuHamil' => $this->validIbuHamil,
            'errorMessage' => $this->errorMessage,
            'dataIbuHamil' => [
                'nama' => $this->nama_ibu,
                'usia' => $this->usia_ibu,
                'hpht' => $this->hpht,
                'hpl' => $this->hpl,
                'usia_kehamilan' => $this->usia_kehamilan_saat_ini,
            ],
            'riwayatByIdHamil' => $riwayatByIdHamil,
        ]);
    }

    // Fungsi untuk menangani perubahan pada jenis tatalaksana
    public function onChangeTatalaksana()
    {
        // Reset form anemia jika tatalaksana bukan anemia
        if ($this->jenis_tatalaksana != 'Anemia') {
            $this->diberikan_tablet_fe = null;
            $this->jumlah_tablet_dikonsumsi = 0;
            $this->jumlah_tablet_ditambahkan = 0;
            $this->tatalaksana_lainnya = null;
        }
        
        // Reset form Makanan Tambahan Ibu Hamil jika tatalaksana bukan MT
        if ($this->jenis_tatalaksana != 'Makanan Tambahan Ibu Hamil') {
            $this->pemberian_mt = null;
            $this->jumlah_mt = 0;
        }
        
        // Reset form Hipertensi jika tatalaksana bukan Hipertensi
        if ($this->jenis_tatalaksana != 'Hipertensi') {
            $this->pantau_tekanan_darah = null;
            $this->pantau_protein_urine = null;
            $this->pantau_kondisi_janin = null;
            $this->hipertensi_lainnya = null;
        }
        
        // Reset form Eklampsia jika tatalaksana bukan Eklampsia
        if ($this->jenis_tatalaksana != 'Eklampsia') {
            $this->pantau_tekanan_darah_eklampsia = null;
            $this->pantau_protein_urine_eklampsia = null;
            $this->pantau_kondisi_janin_eklampsia = null;
            $this->pemberian_antihipertensi = null;
            $this->pemberian_mgso4 = null;
            $this->pemberian_diazepam = null;
        }
        
        // Reset form KEK jika tatalaksana bukan KEK
        if ($this->jenis_tatalaksana != 'KEK') {
            $this->edukasi_gizi = null;
            $this->kek_lainnya = null;
        }
        
        // Reset form Obesitas jika tatalaksana bukan Obesitas
        if ($this->jenis_tatalaksana != 'Obesitas') {
            $this->edukasi_gizi_obesitas = null;
            $this->obesitas_lainnya = null;
        }
        
        // Reset form Infeksi jika tatalaksana bukan Infeksi
        if ($this->jenis_tatalaksana != 'Infeksi') {
            $this->pemberian_antipiretik = null;
            $this->pemberian_antibiotik = null;
            $this->infeksi_lainnya = null;
        }
    }
    
    // Fungsi untuk menghapus form anemia
    public function hapusFormAnemia()
    {
        $this->jenis_tatalaksana = '';
        $this->diberikan_tablet_fe = null;
        $this->jumlah_tablet_dikonsumsi = 0;
        $this->jumlah_tablet_ditambahkan = 0;
        $this->tatalaksana_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Makanan Tambahan Ibu Hamil
    public function hapusFormMT()
    {
        $this->jenis_tatalaksana = '';
        $this->pemberian_mt = null;
        $this->jumlah_mt = 0;
    }
    
    // Fungsi untuk menghapus form Hipertensi
    public function hapusFormHipertensi()
    {
        $this->jenis_tatalaksana = '';
        $this->pantau_tekanan_darah = null;
        $this->pantau_protein_urine = null;
        $this->pantau_kondisi_janin = null;
        $this->hipertensi_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Eklampsia
    public function hapusFormEklampsia()
    {
        $this->jenis_tatalaksana = '';
        $this->pantau_tekanan_darah_eklampsia = null;
        $this->pantau_protein_urine_eklampsia = null;
        $this->pantau_kondisi_janin_eklampsia = null;
        $this->pemberian_antihipertensi = null;
        $this->pemberian_mgso4 = null;
        $this->pemberian_diazepam = null;
    }
    
    // Fungsi untuk menghapus form KEK
    public function hapusFormKEK()
    {
        $this->jenis_tatalaksana = '';
        $this->edukasi_gizi = null;
        $this->kek_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Obesitas
    public function hapusFormObesitas()
    {
        $this->jenis_tatalaksana = '';
        $this->edukasi_gizi_obesitas = null;
        $this->obesitas_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Infeksi
    public function hapusFormInfeksi()
    {
        $this->jenis_tatalaksana = '';
        $this->pemberian_antipiretik = null;
        $this->pemberian_antibiotik = null;
        $this->infeksi_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Penyakit Jantung
    public function hapusFormJantung()
    {
        $this->jenis_tatalaksana = '';
        $this->edukasi = null;
        $this->jantung_lainnya = null;
    }
    
    // Fungsi untuk menghapus form HIV
    public function hapusFormHIV()
    {
        $this->jenis_tatalaksana = '';
        $this->datang_dengan_hiv = null;
        $this->persalinan_pervaginam = null;
        $this->persalinan_perapdoinam = null;
        $this->ditawarkan_tes = null;
        $this->dilakukan_tes = null;
        $this->hasil_tes_hiv = null;
        $this->mendapatkan_art = null;
        $this->vct_pict = null;
        $this->periksa_darah = null;
        $this->serologi = null;
        $this->arv_profilaksis = null;
        $this->hiv_lainnya = null;
    }
    
    // Fungsi untuk menghapus form TB
    public function hapusFormTB()
    {
        $this->jenis_tatalaksana = '';
        $this->diperiksa_dahak = null;
        $this->tbc = null;
        $this->obat_tb = null;
        $this->sisa_obat = null;
        $this->tb_lainnya = null;
    }
    
    // Fungsi untuk menghapus form Malaria
    public function hapusFormMalaria()
    {
        $this->jenis_tatalaksana = '';
        $this->diberikan_kelambu = null;
        $this->darah_malaria_rdt = null;
        $this->darah_malaria_mikroskopis = null;
        $this->ibu_hamil_malaria_rdt = null;
        $this->ibu_hamil_malaria_mikroskopis = null;
        $this->hasil_test_malaria = null;
        $this->obat_malaria = null;
    }

    // Fungsi untuk menghitung taksiran berat janin
    public function hitungTaksiranBeratJanin()
    {
        if (!empty($this->tfu) && is_numeric($this->tfu)) {
            // Rumus Johnson-Toshach: BB (gram) = 155 x (tinggi fundus dalam cm - n)
            // Dimana n=13 jika kepala belum masuk PAP, n=12 jika kepala sudah masuk PAP
            $n = 13; // Asumsi kepala belum masuk PAP
            
            $tfu = (float) $this->tfu;
            $beratJanin = 155 * ($tfu - $n);
            
            // Pastikan nilainya tidak negatif
            if ($beratJanin > 0) {
                $this->taksiran_berat_janin = round($beratJanin);
            } else {
                $this->taksiran_berat_janin = 0;
            }
        } else {
            $this->taksiran_berat_janin = 0;
        }
    }

    public function update()
    {
        try {
            if (!$this->isEdit || !$this->pemeriksaanId) {
                session()->flash('error', 'Tidak dalam mode edit');
                return;
            }
            
            DB::beginTransaction();
            
            // Persiapkan data untuk update
            $data = [
                'no_rawat' => $this->noRawat,
                'no_rkm_medis' => $this->noRm,
                'id_hamil' => $this->id_hamil,
                'tanggal_anc' => $this->tanggal_anc,
                'diperiksa_oleh' => $this->diperiksa_oleh,
                'usia_kehamilan' => $this->usia_kehamilan,
                'trimester' => $this->trimester,
                'kunjungan_ke' => $this->kunjungan_ke,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'imt' => $this->imt,
                'kategori_imt' => $this->kategori_imt,
                'jumlah_janin' => $this->jumlah_janin,
                'td_sistole' => $this->td_sistole,
                'td_diastole' => $this->td_diastole,
                'jumlah_fe' => $this->jumlah_fe,
                'dosis' => $this->dosis,
                'pemeriksaan_lab' => $this->pemeriksaan_lab,
                'jenis_tatalaksana' => $this->jenis_tatalaksana,
                'materi' => $this->materi,
                'rekomendasi' => $this->rekomendasi,
                'konseling_menyusui' => $this->konseling_menyusui,
                'tanda_bahaya_kehamilan' => $this->tanda_bahaya_kehamilan,
                'tanda_bahaya_persalinan' => $this->tanda_bahaya_persalinan,
                'konseling_phbs' => $this->konseling_phbs,
                'konseling_gizi' => $this->konseling_gizi,
                'konseling_ibu_hamil' => $this->konseling_ibu_hamil,
                'konseling_lainnya' => $this->konseling_lainnya,
                'keadaan_pulang' => $this->keadaan_pulang,
                // Data baru untuk anamnesis dan ANC terpadu
                'keluhan_utama' => $this->keluhan_utama,
                'gravida' => $this->gravida,
                'partus' => $this->partus,
                'abortus' => $this->abortus,
                'hidup' => $this->hidup,
                'riwayat_penyakit' => $this->riwayat_penyakit,
                'lila' => $this->lila,
                'status_gizi' => $this->status_gizi,
                'tfu' => $this->tfu,
                'taksiran_berat_janin' => $this->taksiran_berat_janin,
                'djj' => $this->djj,
                'presentasi' => $this->presentasi,
                'status_tt' => $this->status_tt,
                'tanggal_imunisasi' => $this->tanggal_imunisasi,
                'tanggal_lab' => $this->tanggal_lab,
                'lab' => $this->lab,
                'rujukan_ims' => $this->rujukan_ims,
                'tindak_lanjut' => $this->tindak_lanjut,
                'detail_tindak_lanjut' => $this->detail_tindak_lanjut,
                'tanggal_kunjungan_berikutnya' => $this->tanggal_kunjungan_berikutnya,
                'updated_at' => now()
            ];
            
            // Tambahkan data anemia jika jenis tatalaksana adalah Anemia
            if ($this->jenis_tatalaksana === 'Anemia') {
                $data['diberikan_tablet_fe'] = $this->diberikan_tablet_fe;
                $data['jumlah_tablet_dikonsumsi'] = $this->jumlah_tablet_dikonsumsi;
                $data['jumlah_tablet_ditambahkan'] = $this->jumlah_tablet_ditambahkan;
                $data['tatalaksana_lainnya'] = $this->tatalaksana_lainnya;
            }
            
            // Tambahkan data MT jika jenis tatalaksana adalah MT
            if ($this->jenis_tatalaksana === 'Makanan Tambahan Ibu Hamil') {
                $data['pemberian_mt'] = $this->pemberian_mt;
                $data['jumlah_mt'] = $this->jumlah_mt;
            }
            
            // Tambahkan data Hipertensi jika jenis tatalaksana adalah Hipertensi
            if ($this->jenis_tatalaksana === 'Hipertensi') {
                $data['pantau_tekanan_darah'] = $this->pantau_tekanan_darah;
                $data['pantau_protein_urine'] = $this->pantau_protein_urine;
                $data['pantau_kondisi_janin'] = $this->pantau_kondisi_janin;
                $data['hipertensi_lainnya'] = $this->hipertensi_lainnya;
            }
            
            // Tambahkan data Eklampsia jika jenis tatalaksana adalah Eklampsia
            if ($this->jenis_tatalaksana === 'Eklampsia') {
                $data['pantau_tekanan_darah_eklampsia'] = $this->pantau_tekanan_darah_eklampsia;
                $data['pantau_protein_urine_eklampsia'] = $this->pantau_protein_urine_eklampsia;
                $data['pantau_kondisi_janin_eklampsia'] = $this->pantau_kondisi_janin_eklampsia;
                $data['pemberian_antihipertensi'] = $this->pemberian_antihipertensi;
                $data['pemberian_mgso4'] = $this->pemberian_mgso4;
                $data['pemberian_diazepam'] = $this->pemberian_diazepam;
            }
            
            // Tambahkan data KEK jika jenis tatalaksana adalah KEK
            if ($this->jenis_tatalaksana === 'KEK') {
                $data['edukasi_gizi'] = $this->edukasi_gizi;
                $data['kek_lainnya'] = $this->kek_lainnya;
            }
            
            // Tambahkan data Obesitas jika jenis tatalaksana adalah Obesitas
            if ($this->jenis_tatalaksana === 'Obesitas') {
                $data['edukasi_gizi_obesitas'] = $this->edukasi_gizi_obesitas;
                $data['obesitas_lainnya'] = $this->obesitas_lainnya;
            }
            
            // Tambahkan data Infeksi jika jenis tatalaksana adalah Infeksi
            if ($this->jenis_tatalaksana === 'Infeksi') {
                $data['pemberian_antipiretik'] = $this->pemberian_antipiretik;
                $data['pemberian_antibiotik'] = $this->pemberian_antibiotik;
                $data['infeksi_lainnya'] = $this->infeksi_lainnya;
            }
            
            // Tambahkan data Penyakit Jantung jika jenis tatalaksana adalah Penyakit Jantung
            if ($this->jenis_tatalaksana === 'Penyakit Jantung') {
                $data['edukasi'] = $this->edukasi;
                $data['jantung_lainnya'] = $this->jantung_lainnya;
            }
            
            // Tambahkan data HIV jika jenis tatalaksana adalah HIV
            if ($this->jenis_tatalaksana === 'HIV') {
                $data['datang_dengan_hiv'] = $this->datang_dengan_hiv;
                $data['persalinan_pervaginam'] = $this->persalinan_pervaginam;
                $data['persalinan_perapdoinam'] = $this->persalinan_perapdoinam;
                $data['ditawarkan_tes'] = $this->ditawarkan_tes;
                $data['dilakukan_tes'] = $this->dilakukan_tes;
                $data['hasil_tes_hiv'] = $this->hasil_tes_hiv;
                $data['mendapatkan_art'] = $this->mendapatkan_art;
                $data['vct_pict'] = $this->vct_pict;
                $data['periksa_darah'] = $this->periksa_darah;
                $data['serologi'] = $this->serologi;
                $data['arv_profilaksis'] = $this->arv_profilaksis;
                $data['hiv_lainnya'] = $this->hiv_lainnya;
            }
            
            // Tambahkan data TB jika jenis tatalaksana adalah TB
            if ($this->jenis_tatalaksana === 'TB') {
                $data['diperiksa_dahak'] = $this->diperiksa_dahak;
                $data['tbc'] = $this->tbc;
                $data['obat_tb'] = $this->obat_tb;
                $data['sisa_obat'] = $this->sisa_obat;
                $data['tb_lainnya'] = $this->tb_lainnya;
            }
            
            // Tambahkan data Malaria jika jenis tatalaksana adalah Malaria
            if ($this->jenis_tatalaksana === 'Malaria') {
                $data['diberikan_kelambu'] = $this->diberikan_kelambu;
                $data['darah_malaria_rdt'] = $this->darah_malaria_rdt;
                $data['darah_malaria_mikroskopis'] = $this->darah_malaria_mikroskopis;
                $data['ibu_hamil_malaria_rdt'] = $this->ibu_hamil_malaria_rdt;
                $data['ibu_hamil_malaria_mikroskopis'] = $this->ibu_hamil_malaria_mikroskopis;
                $data['hasil_test_malaria'] = $this->hasil_test_malaria;
                $data['obat_malaria'] = $this->obat_malaria;
                $data['malaria_lainnya'] = $this->malaria_lainnya;
            }
            
            $pemeriksaan = \App\Models\PemeriksaanAnc::where('id_anc', $this->pemeriksaanId)->first();
            
            if ($pemeriksaan) {
                $pemeriksaan->update($data);
                DB::commit();
                session()->flash('success', 'Data pemeriksaan ANC berhasil diperbarui');
                $this->resetForm();
                $this->isEdit = false;
                $this->pemeriksaanId = null;
            } else {
                DB::rollBack();
                session()->flash('error', 'Data pemeriksaan ANC tidak ditemukan');
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error updating data: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan detail pemeriksaan ANC berdasarkan id_anc
     */
    public function showHistoriANC($id)
    {
        $this->resetValidation();
        $this->resetForm();
        $pemeriksaan = \App\Models\PemeriksaanAnc::where('id_anc', $id)->first();
        
        if (!$pemeriksaan) {
            session()->flash('error', 'Data pemeriksaan ANC tidak ditemukan.');
            return;
        }
        
        // Dapatkan data pasien
        $pasien = DB::table('pasien')
            ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
            ->first();
            
        if (!$pasien) {
            session()->flash('error', 'Data pasien tidak ditemukan.');
            return;
        }
        
        // Dapatkan data ibu hamil
        $dataIbuHamil = DB::table('data_ibu_hamil')
            ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
            ->first();
            
        if ($dataIbuHamil) {
            $this->validIbuHamil = true;
            $this->id_hamil = $dataIbuHamil->id_hamil;
            $this->nama_ibu = $dataIbuHamil->nama;
            
            if (isset($dataIbuHamil->usia_ibu)) {
                $this->usia_ibu = $dataIbuHamil->usia_ibu;
            } else {
                // Hitung usia berdasarkan tanggal lahir jika usia_ibu tidak ada
                if (isset($dataIbuHamil->tgl_lahir)) {
                    $birthDate = new \DateTime($dataIbuHamil->tgl_lahir);
                    $today = new \DateTime('today');
                    $this->usia_ibu = $birthDate->diff($today)->y;
                }
            }
            
            $this->hpht = $dataIbuHamil->hari_pertama_haid ? date('d-m-Y', strtotime($dataIbuHamil->hari_pertama_haid)) : null;
            $this->hpl = $dataIbuHamil->hari_perkiraan_lahir ? date('d-m-Y', strtotime($dataIbuHamil->hari_perkiraan_lahir)) : null;
            
            // Hitung usia kehamilan saat ini jika HPHT ada
            if ($dataIbuHamil->hari_pertama_haid) {
                $hpht = Carbon::parse($dataIbuHamil->hari_pertama_haid);
                $now = Carbon::now();
                
                // Hitung selisih dalam minggu
                $diffInWeeks = $now->diffInWeeks($hpht);
                $this->usia_kehamilan_saat_ini = $diffInWeeks;
            }
        } else {
            session()->flash('warning', 'Pasien tidak terdaftar sebagai ibu hamil aktif.');
        }
        
        session()->flash('info', 'Menampilkan riwayat pemeriksaan ANC untuk pasien ' . $pasien->nm_pasien);
        
        // Tampilkan informasi pemeriksaan
        $this->tanggal_anc = Carbon::parse($pemeriksaan->tanggal_anc)->format('Y-m-d\TH:i:s');
        $this->diperiksa_oleh = $pemeriksaan->diperiksa_oleh;
        $this->usia_kehamilan = $pemeriksaan->usia_kehamilan;
        $this->trimester = $pemeriksaan->trimester;
        $this->kunjungan_ke = $pemeriksaan->kunjungan_ke;
        $this->berat_badan = $pemeriksaan->berat_badan;
        $this->tinggi_badan = $pemeriksaan->tinggi_badan;
        $this->imt = $pemeriksaan->imt;
        $this->kategori_imt = $pemeriksaan->kategori_imt;
        $this->jumlah_janin = $pemeriksaan->jumlah_janin;
        $this->td_sistole = $pemeriksaan->td_sistole;
        $this->td_diastole = $pemeriksaan->td_diastole;
        $this->jumlah_fe = $pemeriksaan->jumlah_fe;
        $this->dosis = $pemeriksaan->dosis;
        $this->pemeriksaan_lab = $pemeriksaan->pemeriksaan_lab;
        $this->jenis_tatalaksana = $pemeriksaan->jenis_tatalaksana;
        $this->materi = $pemeriksaan->materi;
        $this->rekomendasi = $pemeriksaan->rekomendasi;
        $this->konseling_menyusui = $pemeriksaan->konseling_menyusui;
        $this->tanda_bahaya_kehamilan = $pemeriksaan->tanda_bahaya_kehamilan;
        $this->tanda_bahaya_persalinan = $pemeriksaan->tanda_bahaya_persalinan;
        $this->konseling_phbs = $pemeriksaan->konseling_phbs;
        $this->konseling_gizi = $pemeriksaan->konseling_gizi;
        $this->konseling_ibu_hamil = $pemeriksaan->konseling_ibu_hamil;
        $this->konseling_lainnya = $pemeriksaan->konseling_lainnya;
        $this->keadaan_pulang = $pemeriksaan->keadaan_pulang;
        
        // Data anamnesis dan ANC terpadu
        $this->keluhan_utama = $pemeriksaan->keluhan_utama;
        $this->gravida = $pemeriksaan->gravida;
        $this->partus = $pemeriksaan->partus;
        $this->abortus = $pemeriksaan->abortus;
        $this->hidup = $pemeriksaan->hidup;
        $this->riwayat_penyakit = $pemeriksaan->riwayat_penyakit;
        $this->lila = $pemeriksaan->lila;
        $this->status_gizi = $pemeriksaan->status_gizi;
        $this->tfu = $pemeriksaan->tfu;
        $this->taksiran_berat_janin = $pemeriksaan->taksiran_berat_janin;
        $this->djj = $pemeriksaan->djj;
        $this->presentasi = $pemeriksaan->presentasi;
        $this->status_tt = $pemeriksaan->status_tt;
        $this->tanggal_imunisasi = $pemeriksaan->tanggal_imunisasi;
        $this->tanggal_lab = $pemeriksaan->tanggal_lab;
        $this->lab = $pemeriksaan->lab;
        $this->rujukan_ims = $pemeriksaan->rujukan_ims;
        $this->tindak_lanjut = $pemeriksaan->tindak_lanjut;
        $this->detail_tindak_lanjut = $pemeriksaan->detail_tindak_lanjut;
        $this->tanggal_kunjungan_berikutnya = $pemeriksaan->tanggal_kunjungan_berikutnya;
        
        // Tatalaksana - Anemia
        $this->diberikan_tablet_fe = $pemeriksaan->diberikan_tablet_fe;
        $this->jumlah_tablet_dikonsumsi = $pemeriksaan->jumlah_tablet_dikonsumsi;
        $this->jumlah_tablet_ditambahkan = $pemeriksaan->jumlah_tablet_ditambahkan;
        $this->tatalaksana_lainnya = $pemeriksaan->tatalaksana_lainnya;
        
        // Tatalaksana - Makanan Tambahan Ibu Hamil
        $this->pemberian_mt = $pemeriksaan->pemberian_mt;
        $this->jumlah_mt = $pemeriksaan->jumlah_mt;
        
        // Tatalaksana - Hipertensi
        $this->pantau_tekanan_darah = $pemeriksaan->pantau_tekanan_darah;
        $this->pantau_protein_urine = $pemeriksaan->pantau_protein_urine;
        $this->pantau_kondisi_janin = $pemeriksaan->pantau_kondisi_janin;
        $this->hipertensi_lainnya = $pemeriksaan->hipertensi_lainnya;
        
        // Tatalaksana - Eklampsia
        $this->pantau_tekanan_darah_eklampsia = $pemeriksaan->pantau_tekanan_darah_eklampsia;
        $this->pantau_protein_urine_eklampsia = $pemeriksaan->pantau_protein_urine_eklampsia;
        $this->pantau_kondisi_janin_eklampsia = $pemeriksaan->pantau_kondisi_janin_eklampsia;
        $this->pemberian_antihipertensi = $pemeriksaan->pemberian_antihipertensi;
        $this->pemberian_mgso4 = $pemeriksaan->pemberian_mgso4;
        $this->pemberian_diazepam = $pemeriksaan->pemberian_diazepam;
        
        // Tatalaksana - KEK
        $this->edukasi_gizi = $pemeriksaan->edukasi_gizi;
        $this->kek_lainnya = $pemeriksaan->kek_lainnya;
        
        // Tatalaksana - Obesitas
        $this->edukasi_gizi_obesitas = $pemeriksaan->edukasi_gizi_obesitas;
        $this->obesitas_lainnya = $pemeriksaan->obesitas_lainnya;
        
        // Tatalaksana - Infeksi
        $this->pemberian_antipiretik = $pemeriksaan->pemberian_antipiretik;
        $this->pemberian_antibiotik = $pemeriksaan->pemberian_antibiotik;
        $this->infeksi_lainnya = $pemeriksaan->infeksi_lainnya;
        
        // Tatalaksana - Penyakit Jantung
        $this->edukasi = $pemeriksaan->edukasi;
        $this->jantung_lainnya = $pemeriksaan->jantung_lainnya;
        
        // Tatalaksana - HIV
        $this->datang_dengan_hiv = $pemeriksaan->datang_dengan_hiv;
        $this->persalinan_pervaginam = $pemeriksaan->persalinan_pervaginam;
        $this->persalinan_perapdoinam = $pemeriksaan->persalinan_perapdoinam;
        $this->ditawarkan_tes = $pemeriksaan->ditawarkan_tes;
        $this->dilakukan_tes = $pemeriksaan->dilakukan_tes;
        $this->hasil_tes_hiv = $pemeriksaan->hasil_tes_hiv;
        $this->mendapatkan_art = $pemeriksaan->mendapatkan_art;
        $this->vct_pict = $pemeriksaan->vct_pict;
        $this->periksa_darah = $pemeriksaan->periksa_darah;
        $this->serologi = $pemeriksaan->serologi;
        $this->arv_profilaksis = $pemeriksaan->arv_profilaksis;
        $this->hiv_lainnya = $pemeriksaan->hiv_lainnya;
        
        // Tatalaksana - TB
        $this->diperiksa_dahak = $pemeriksaan->diperiksa_dahak;
        $this->tbc = $pemeriksaan->tbc;
        $this->obat_tb = $pemeriksaan->obat_tb;
        $this->sisa_obat = $pemeriksaan->sisa_obat;
        $this->tb_lainnya = $pemeriksaan->tb_lainnya;
        
        // Tatalaksana - Malaria
        $this->diberikan_kelambu = $pemeriksaan->diberikan_kelambu;
        $this->darah_malaria_rdt = $pemeriksaan->darah_malaria_rdt;
        $this->darah_malaria_mikroskopis = $pemeriksaan->darah_malaria_mikroskopis;
        $this->ibu_hamil_malaria_rdt = $pemeriksaan->ibu_hamil_malaria_rdt;
        $this->ibu_hamil_malaria_mikroskopis = $pemeriksaan->ibu_hamil_malaria_mikroskopis;
        $this->hasil_test_malaria = $pemeriksaan->hasil_test_malaria;
        $this->obat_malaria = $pemeriksaan->obat_malaria;
        $this->malaria_lainnya = $pemeriksaan->malaria_lainnya;
    }
    
    /**
     * Menampilkan riwayat pemeriksaan ANC berdasarkan id_hamil
     */
    public function showHistoriByIdHamil($id_hamil)
    {
        try {
            // Cari data ibu hamil
            $dataIbuHamil = DB::table('data_ibu_hamil')
                ->where('id_hamil', $id_hamil)
                ->first();
            
            if (!$dataIbuHamil) {
                session()->flash('error', 'Data ibu hamil tidak ditemukan.');
                return;
            }
            
            // Set data ibu hamil ke property
            $this->validIbuHamil = true;
            $this->id_hamil = $dataIbuHamil->id_hamil;
            $this->nama_ibu = $dataIbuHamil->nama;
            
            if (isset($dataIbuHamil->usia_ibu)) {
                $this->usia_ibu = $dataIbuHamil->usia_ibu;
            } else {
                // Hitung usia berdasarkan tanggal lahir jika usia_ibu tidak ada
                if (isset($dataIbuHamil->tgl_lahir)) {
                    $birthDate = new \DateTime($dataIbuHamil->tgl_lahir);
                    $today = new \DateTime('today');
                    $this->usia_ibu = $birthDate->diff($today)->y;
                }
            }
            
            $this->hpht = $dataIbuHamil->hari_pertama_haid ? date('d-m-Y', strtotime($dataIbuHamil->hari_pertama_haid)) : null;
            $this->hpl = $dataIbuHamil->hari_perkiraan_lahir ? date('d-m-Y', strtotime($dataIbuHamil->hari_perkiraan_lahir)) : null;
            
            // Hitung usia kehamilan saat ini jika HPHT ada
            if ($dataIbuHamil->hari_pertama_haid) {
                $hpht = Carbon::parse($dataIbuHamil->hari_pertama_haid);
                $now = Carbon::now();
                
                // Hitung selisih dalam minggu
                $diffInWeeks = $now->diffInWeeks($hpht);
                $this->usia_kehamilan_saat_ini = $diffInWeeks;
            }
            
            // Atur no_rkm_medis dari data ibu hamil untuk mencari riwayat
            $this->noRm = $dataIbuHamil->no_rkm_medis;
            
            // Temukan pemeriksaan terbaru jika ada
            $latestPemeriksaan = \App\Models\PemeriksaanAnc::where('id_hamil', $id_hamil)
                ->orderBy('tanggal_anc', 'desc')
                ->first();
            
            // Jika ada pemeriksaan terbaru, tampilkan detail
            if ($latestPemeriksaan) {
                $this->showHistoriANC($latestPemeriksaan->id_anc);
            } else {
                session()->flash('info', 'Belum ada riwayat pemeriksaan ANC untuk kehamilan ini.');
                
                // Reset form ke default untuk pemeriksaan baru
                $this->resetForm();
                
                // Tetap isi id_hamil dan informasi ibu hamil
                $this->id_hamil = $id_hamil;
            }
            
        } catch (\Exception $e) {
            \Log::error('Error showing history by id_hamil: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Getter untuk tanggal_anc yang akan digunakan oleh front-end
     * Ini memastikan format yang benar untuk input datetime-local
     */
    public function getTanggalAncFormattedProperty()
    {
        if ($this->tanggal_anc) {
            try {
                return \Carbon\Carbon::parse($this->tanggal_anc)->format('Y-m-d\TH:i');
            } catch (\Exception $e) {
                \Log::error('Error formatting tanggal_anc for input: ' . $e->getMessage(), [
                    'tanggal_anc' => $this->tanggal_anc
                ]);
                return '';
            }
        }
        
        return '';
    }
}
