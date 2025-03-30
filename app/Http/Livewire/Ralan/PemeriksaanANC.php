<?php

namespace App\Http\Livewire\Ralan;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

/**
 * Class PemeriksaanANC
 * 
 * Class ini mengelola formulir pemeriksaan Antenatal Care (ANC) untuk ibu hamil
 * dalam aplikasi e-Dokter.
 * 
 * Fitur-fitur utama:
 * - Pencatatan data pemeriksaan ANC berdasarkan 10T (standar pemeriksaan ANC)
 * - Pengelolaan riwayat pemeriksaan
 * - Perhitungan otomatis IMT, taksiran berat janin, status gizi
 * - Manajemen tatalaksana kasus risiko kehamilan
 * 
 * @author edokter Development Team
 * @version 1.0.0
 */
class PemeriksaanANC extends Component
{
    // Data input dari form
    public $noRawat;
    public $noRm;
    public $tanggal_anc;
    public $tanggal_anc_input; // Property baru untuk input tanggal user-friendly
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
    public $tinggi_fundus;
    public $taksiran_berat_janin;
    public $denyut_jantung_janin;
    public $presentasi;
    public $presentasi_janin;
    public $status_tt;
    public $tanggal_imunisasi;
    public $tanggal_lab;
    public $lab = [
        'hb' => ['checked' => false, 'nilai' => null],
        'goldar' => ['checked' => false, 'nilai' => null],
        'protein_urin' => ['checked' => false, 'nilai' => null],
        'hiv' => ['checked' => false, 'nilai' => null],
        'sifilis' => ['checked' => false, 'nilai' => null],
        'hbsag' => ['checked' => false, 'nilai' => null],
        'gula_darah' => ['checked' => false, 'nilai' => null],
        'malaria' => ['checked' => false, 'nilai' => null],
        'lainnya' => ['checked' => false, 'nama' => null, 'nilai' => null]
    ];
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

            // Inisialisasi properti lab
            $this->initLabProperty();

            // Set nilai default
            $this->noRawat = $noRawat;
            $this->noRm = $noRm;
            $this->tanggal_anc = now()->format('Y-m-d H:i:s');
            $this->tanggal_anc_input = now()->format('d/m/Y, H:i'); // Format untuk input user
            
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

        // Inisialisasi riwayat penyakit sebagai array dengan nilai default
        $this->riwayat_penyakit = [
            'hipertensi' => false,
            'diabetes' => false,
            'jantung' => false,
            'asma' => false,
            'lainnya_check' => false,
            'lainnya' => null
        ];
    }

    /**
     * Hook Livewire untuk memproses nilai sebelum validasi
     */
    public function prepareForValidation($attributes)
    {
        try {
            \Log::info('Mempersiapkan data untuk validasi', [
                'tanggal_anc_input' => $this->tanggal_anc_input,
                'tanggal_anc' => $this->tanggal_anc
            ]);
            
            // Proses tanggal input jika ada
            $this->processDateInput();
            
            // Pastikan tanggal_anc selalu tersedia untuk validasi
            if (!empty($this->tanggal_anc)) {
                $attributes['tanggal_anc'] = $this->tanggal_anc;
                \Log::info('Tanggal ANC diset untuk validasi', ['tanggal_anc' => $this->tanggal_anc]);
            } else {
                // Fallback ke waktu saat ini jika masih kosong
                $this->tanggal_anc = now()->format('Y-m-d H:i:s');
                $attributes['tanggal_anc'] = $this->tanggal_anc;
                \Log::info('Tanggal ANC fallback ke waktu saat ini', ['tanggal_anc' => $this->tanggal_anc]);
            }
            
            return $attributes;
        } catch (\Exception $e) {
            \Log::error('Error di prepareForValidation', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Dalam kasus error, tetap sertakan tanggal_anc
            $attributes['tanggal_anc'] = now()->format('Y-m-d H:i:s');
            \Log::info('Error, fallback ke waktu saat ini di prepareForValidation', ['tanggal_anc' => $attributes['tanggal_anc']]);
            
            return $attributes;
        }
    }
    
    /**
     * Property untuk validasi
     */
    protected $rules = [
        'tanggal_anc' => 'required',  // Ubah dari 'required|date' menjadi 'required' saja
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
        'tinggi_fundus' => 'nullable|numeric|min:0|max:40',
        'taksiran_berat_janin' => 'nullable|numeric',
        'denyut_jantung_janin' => 'nullable|numeric|min:100|max:200',
        'presentasi' => 'nullable|string',
        'presentasi_janin' => 'nullable|string',
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
     * Hook saat ada nilai yang diupdate
     */
    public function updated($propertyName)
    {
        \Log::info('Property updated: ' . $propertyName, ['value' => $this->{$propertyName} ?? 'null']);
        
        // Jika tanggal ANC input yang diupdate
        if ($propertyName === 'tanggal_anc_input') {
            $this->processDateInput();
        }
        
        // Jika properti yang berkaitan dengan IMT diupdate
        if (in_array($propertyName, ['berat_badan', 'tinggi_badan'])) {
            $this->hitungIMT();
        }
        
        // Jika tinggi_fundus diupdate, hitung taksiran berat janin
        if ($propertyName === 'tinggi_fundus') {
            $this->hitungTaksiranBeratJanin();
        }
        
        // Jika lila diupdate, tentukan status gizi
        if ($propertyName === 'lila') {
            $this->tentukanStatusGizi();
        }
        
        // Jika properti lab yang diupdate, handle dengan metode khusus
        if (strpos($propertyName, 'lab.') === 0) {
            $this->handleLabPropertyUpdate($propertyName);
            return; // Skip validasi untuk properti lab
        }
        
        // Validasi properti yang diupdate
        $this->validateOnly($propertyName);
    }

    /**
     * Metode khusus untuk menangani update properti lab
     */
    protected function handleLabPropertyUpdate($propertyName)
    {
        \Log::info('Handling lab property update', ['property' => $propertyName]);
        
        // Parse properti lab dengan format lab.jenis.atribut
        $parts = explode('.', $propertyName);
        
        if (count($parts) === 3) {
            $jenis = $parts[1];
            $atribut = $parts[2];
            
            // Pastikan struktur lab sudah diinisialisasi dengan benar
            if (!isset($this->lab)) {
                $this->initLabProperty();
            }
            
            // Pastikan jenis pemeriksaan lab sudah diinisialisasi
            if (!isset($this->lab[$jenis])) {
                $this->lab[$jenis] = ['checked' => false, 'nilai' => null];
                if ($jenis === 'lainnya') {
                    $this->lab[$jenis]['nama'] = null;
                }
            }
            
            // Ambil nilai dari $this->{$propertyName} dan simpan ke array lab
            $value = null;
            
            // Gunakan registry untuk mengakses nilai karena dot notation tidak akan bekerja
            if (isset($this->getPublicPropertiesDefinedBySubClass()[$propertyName])) {
                $value = $this->getPublicPropertiesDefinedBySubClass()[$propertyName];
            }
            
            // Update nilai berdasarkan atribut
            if ($atribut === 'checked') {
                $this->lab[$jenis]['checked'] = (bool) $value;
            } else if ($atribut === 'nilai') {
                $this->lab[$jenis]['nilai'] = $value;
            } else if ($atribut === 'nama' && $jenis === 'lainnya') {
                $this->lab[$jenis]['nama'] = $value;
            }
            
            \Log::info('Updated lab property', [
                'jenis' => $jenis, 
                'atribut' => $atribut, 
                'value' => $value,
                'lab_array' => $this->lab
            ]);
        }
    }

    /**
     * Inisialisasi properti lab
     */
    protected function initLabProperty()
    {
        $this->lab = [
            'hb' => ['checked' => false, 'nilai' => null],
            'goldar' => ['checked' => false, 'nilai' => null],
            'protein_urin' => ['checked' => false, 'nilai' => null],
            'hiv' => ['checked' => false, 'nilai' => null],
            'sifilis' => ['checked' => false, 'nilai' => null],
            'hbsag' => ['checked' => false, 'nilai' => null],
            'gula_darah' => ['checked' => false, 'nilai' => null],
            'malaria' => ['checked' => false, 'nilai' => null],
            'lainnya' => ['checked' => false, 'nama' => null, 'nilai' => null]
        ];
    }
    
    /**
     * Proses input tanggal dari format user-friendly ke format database
     */
    public function processDateInput()
    {
        try {
            // Log input awal
            \Log::info('Memproses input tanggal', [
                'input' => $this->tanggal_anc_input,
                'tanggal_anc_sebelumnya' => $this->tanggal_anc
            ]);

            // Jika input tanggal kosong, gunakan waktu saat ini
            if (empty($this->tanggal_anc_input)) {
                $this->tanggal_anc = now()->format('Y-m-d H:i:s');
                $this->tanggal_anc_input = now()->format('d/m/Y, H:i');
                \Log::info('Tanggal input kosong, menggunakan waktu saat ini', [
                    'tanggal_anc' => $this->tanggal_anc,
                    'tanggal_anc_input' => $this->tanggal_anc_input
                ]);
                return;
            }

            // Coba parse dengan format-format yang berbeda
            $formattedDate = $this->tryDifferentDateFormats($this->tanggal_anc_input);
            if ($formattedDate) {
                $this->tanggal_anc = $formattedDate;
                \Log::info('Tanggal berhasil diparse dengan format alternatif', ['result' => $this->tanggal_anc]);
                return;
            }
            
            // Jika masih gagal, gunakan waktu saat ini
            $this->tanggal_anc = now()->format('Y-m-d H:i:s');
            \Log::info('Fallback ke waktu saat ini', ['tanggal_anc' => $this->tanggal_anc]);
            
        } catch (\Exception $e) {
            \Log::error('Error processing date input', [
                'input' => $this->tanggal_anc_input,
                'error' => $e->getMessage()
            ]);
            
            // Jika gagal, gunakan waktu saat ini
            $this->tanggal_anc = now()->format('Y-m-d H:i:s');
            \Log::info('Fallback ke waktu saat ini', ['tanggal_anc' => $this->tanggal_anc]);
        }
    }
    
    /**
     * Mencoba berbagai format tanggal untuk memparsing input pengguna
     */
    private function tryDifferentDateFormats($dateInput)
    {
        // Daftar format yang akan dicoba
        $formats = [
            'd/m/Y, H:i',
            'd/m/Y H:i',
            'd-m-Y, H:i',
            'd-m-Y H:i',
            'Y-m-d H:i',
            'Y-m-d H:i:s',
            'd/m/Y',
            'd-m-Y',
            'Y-m-d',
        ];
        
        // Format tanggal dari "dd/mm/yyyy, hh:mm" ke "Y-m-d H:i:s" (regex khusus)
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4}),?\s*(\d{2}):(\d{2})$/', $dateInput, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            $hour = $matches[4];
            $minute = $matches[5];
            
            return "$year-$month-$day $hour:$minute:00";
        }
        
        // Coba format sederhana dd/mm/yyyy 
        if (preg_match('/^(\d{2})\/(\d{2})\/(\d{4})$/', $dateInput, $matches)) {
            $day = $matches[1];
            $month = $matches[2];
            $year = $matches[3];
            
            // Gunakan waktu saat ini
            $hour = now()->format('H');
            $minute = now()->format('i');
            
            return "$year-$month-$day $hour:$minute:00";
        }
        
        foreach ($formats as $format) {
            try {
                $date = \Carbon\Carbon::createFromFormat($format, $dateInput);
                if ($date) {
                    // Jika format hanya tanggal tanpa waktu, tambahkan waktu saat ini
                    if (!strpos($format, 'H:i')) {
                        $date->hour(now()->hour)->minute(now()->minute)->second(now()->second);
                    }
                    return $date->format('Y-m-d H:i:s');
                }
            } catch (\Exception $e) {
                // Lanjut ke format berikutnya
                continue;
            }
        }
        
        // Terakhir, coba dengan Carbon::parse
        try {
            $date = \Carbon\Carbon::parse($dateInput);
            return $date->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            // Jika semua gagal
            return null;
        }
    }
    
    /**
     * Hitung IMT berdasarkan berat dan tinggi badan
     */
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
    
    /**
     * Tentukan status gizi berdasarkan LILA
     */
    public function tentukanStatusGizi()
    {
        if ($this->lila) {
            $lila = (float) $this->lila;
            if ($lila < 23.5) {
                $this->status_gizi = 'KEK (Kurang Energi Kronis)';
            } else {
                $this->status_gizi = 'Normal';
            }
        }
    }

    /**
     * Hitung taksiran berat janin berdasarkan TFU
     */
    public function hitungTaksiranBeratJanin()
    {
        if (!empty($this->tinggi_fundus) && is_numeric($this->tinggi_fundus)) {
            // Rumus Johnson-Toshach: BB (gram) = 155 x (tinggi fundus dalam cm - n)
            // Dimana n=13 jika kepala belum masuk PAP, n=12 jika kepala sudah masuk PAP
            $n = 13; // Asumsi kepala belum masuk PAP
            
            $tfu = (float) $this->tinggi_fundus;
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

    public function save()
    {
        try {
            // Validasi jika pasien terdaftar sebagai ibu hamil
            if (!$this->validIbuHamil) {
                session()->flash('error', 'Pasien belum terdaftar sebagai ibu hamil aktif. Data tidak dapat disimpan.');
                return;
            }
            
            // Proses input tanggal terlebih dahulu
            $this->processDateInput();
            
            // Log data sebelum validasi
            \Log::info('Trying to save ANC data', [
                'tanggal_anc_input' => $this->tanggal_anc_input,
                'tanggal_anc' => $this->tanggal_anc,
                'no_rawat' => $this->noRawat,
                'no_rm' => $this->noRm
            ]);
            
            $validatedData = $this->validate();
            
            if (empty($this->noRawat) || empty($this->noRm)) {
                session()->flash('error', 'No Rawat dan No RM tidak boleh kosong');
                return;
            }
            
            DB::beginTransaction();
            
            // Persiapkan data untuk disimpan menggunakan fungsi bantuan
            $data = $this->prepareDataForSave();
            
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
    
    /**
     * Persiapkan data untuk disimpan atau diupdate
     */
    private function prepareDataForSave()
    {
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
            'lila' => $this->lila,
            'imt' => $this->imt,
            'kategori_imt' => $this->kategori_imt,
            'jumlah_janin' => $this->jumlah_janin ?? 'Tunggal',
            'tinggi_fundus' => $this->tinggi_fundus,
            'taksiran_berat_janin' => $this->taksiran_berat_janin,
            'denyut_jantung_janin' => $this->denyut_jantung_janin,
            'presentasi' => $this->presentasi,
            'presentasi_janin' => $this->presentasi_janin ?? 'Normal',
            'status_tt' => $this->status_tt,
            'tanggal_imunisasi' => $this->tanggal_imunisasi,
            'td_sistole' => $this->td_sistole,
            'td_diastole' => $this->td_diastole,
            'jumlah_fe' => $this->jumlah_fe,
            'dosis' => $this->dosis,
            'materi' => $this->materi,
            'rekomendasi' => $this->rekomendasi,
            'konseling_menyusui' => $this->konseling_menyusui ?? 'Ya',
            'tanda_bahaya_kehamilan' => $this->tanda_bahaya_kehamilan ?? 'Ya',
            'tanda_bahaya_persalinan' => $this->tanda_bahaya_persalinan ?? 'Ya',
            'konseling_phbs' => $this->konseling_phbs ?? 'Ya',
            'konseling_gizi' => $this->konseling_gizi ?? 'Ya',
            'konseling_ibu_hamil' => $this->konseling_ibu_hamil ?? 'Ya',
            'konseling_lainnya' => $this->konseling_lainnya,
            'keadaan_pulang' => $this->keadaan_pulang ?? 'Baik',
            'keluhan_utama' => $this->keluhan_utama,
            'gravida' => $this->gravida,
            'partus' => $this->partus,
            'abortus' => $this->abortus,
            'hidup' => $this->hidup,
            'riwayat_penyakit' => is_array($this->riwayat_penyakit) ? json_encode($this->riwayat_penyakit) : $this->riwayat_penyakit,
            'status_gizi' => $this->status_gizi,
            'tanggal_lab' => $this->tanggal_lab,
            'lab' => is_array($this->lab) ? json_encode($this->lab) : $this->lab,
            'rujukan_ims' => $this->rujukan_ims ?? '-',
            'tindak_lanjut' => $this->tindak_lanjut,
            'detail_tindak_lanjut' => $this->detail_tindak_lanjut ?? '-',
            'tanggal_kunjungan_berikutnya' => $this->tanggal_kunjungan_berikutnya,
            'jenis_tatalaksana' => $this->jenis_tatalaksana,
            'hasil_pemeriksaan_hb' => isset($this->lab['hb']['nilai']) ? $this->lab['hb']['nilai'] : null,
            'hasil_pemeriksaan_urine_protein' => isset($this->lab['protein_urin']['nilai']) ? $this->lab['protein_urin']['nilai'] : null,
            'imunisasi_tt' => $this->status_tt, // Memetakan status_tt ke imunisasi_tt
            'pemberian_mt' => $this->pemberian_mt ?? '-',
            'jumlah_mt' => $this->jumlah_mt ?? 0,
        ];
        
        // Tambahkan data tatalaksana ke array data
        $this->addTatalaksanaDataToArray($data);
        
        return $data;
    }
    
    /**
     * Tambahkan data berdasarkan jenis tatalaksana
     */
    private function addTatalaksanaDataToArray(&$data)
    {
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
    
    /**
     * Update data pemeriksaan ANC yang ada
     */
    public function update()
    {
        try {
            if (!$this->isEdit || !$this->pemeriksaanId) {
                session()->flash('error', 'Tidak dalam mode edit');
                return;
            }
            
            // Proses input tanggal terlebih dahulu
            $this->processDateInput();
            
            $validatedData = $this->validate();
            
            DB::beginTransaction();
            
            // Persiapkan data untuk update menggunakan fungsi yang sama dengan save
            $data = $this->prepareDataForSave();
            $data['updated_at'] = now(); // Tambahkan timestamp update
            
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
     * Reset semua field formulir ke nilai default
     */
    public function resetForm()
    {
        // Reset semua field ke nilai default
        $this->tanggal_anc = now()->format('Y-m-d H:i:s');
        $this->tanggal_anc_input = now()->format('d/m/Y, H:i');
        $this->usia_kehamilan = null;
        $this->trimester = null;
        $this->kunjungan_ke = null;
        $this->berat_badan = null;
        $this->tinggi_badan = null;
        $this->imt = null;
        $this->kategori_imt = null;
        $this->jumlah_janin = null;
        $this->td_sistole = null;
        $this->td_diastole = null;
        $this->jumlah_fe = 0;
        $this->dosis = 0;
        $this->pemeriksaan_lab = null;
        $this->jenis_tatalaksana = null;
        $this->materi = null;
        $this->rekomendasi = null;
        $this->konseling_menyusui = null;
        $this->tanda_bahaya_kehamilan = null;
        $this->tanda_bahaya_persalinan = null;
        $this->konseling_phbs = null;
        $this->konseling_gizi = null;
        $this->konseling_ibu_hamil = null;
        $this->konseling_lainnya = null;
        $this->keadaan_pulang = null;
        $this->keluhan_utama = null;
        $this->gravida = null;
        $this->partus = null;
        $this->abortus = null;
        $this->hidup = null;
        $this->riwayat_penyakit = [];
        $this->lila = null;
        $this->status_gizi = null;
        $this->tinggi_fundus = null;
        $this->taksiran_berat_janin = null;
        $this->denyut_jantung_janin = null;
        $this->presentasi = null;
        $this->presentasi_janin = null;
        $this->status_tt = null;
        $this->tanggal_imunisasi = null;
        $this->tanggal_lab = null;
        
        // Reset property $lab dengan benar
        $this->lab = [
            'hb' => ['checked' => false, 'nilai' => null],
            'goldar' => ['checked' => false, 'nilai' => null],
            'protein_urin' => ['checked' => false, 'nilai' => null],
            'hiv' => ['checked' => false, 'nilai' => null],
            'sifilis' => ['checked' => false, 'nilai' => null],
            'hbsag' => ['checked' => false, 'nilai' => null],
            'gula_darah' => ['checked' => false, 'nilai' => null],
            'malaria' => ['checked' => false, 'nilai' => null],
            'lainnya' => ['checked' => false, 'nama' => null, 'nilai' => null]
        ];
        
        $this->rujukan_ims = null;
        $this->tindak_lanjut = null;
        $this->detail_tindak_lanjut = null;
        $this->tanggal_kunjungan_berikutnya = null;
        
        // Reset anemia form
        $this->diberikan_tablet_fe = null;
        $this->jumlah_tablet_dikonsumsi = 0;
        $this->jumlah_tablet_ditambahkan = 0;
        $this->tatalaksana_lainnya = null;
        
        // Reset MT form
        $this->pemberian_mt = null;
        $this->jumlah_mt = 0;
        
        // Reset hipertensi form
        $this->pantau_tekanan_darah = null;
        $this->pantau_protein_urine = null;
        $this->pantau_kondisi_janin = null;
        $this->hipertensi_lainnya = null;
        
        // Reset eklampsia form
        $this->pantau_tekanan_darah_eklampsia = null;
        $this->pantau_protein_urine_eklampsia = null;
        $this->pantau_kondisi_janin_eklampsia = null;
        $this->pemberian_antihipertensi = null;
        $this->pemberian_mgso4 = null;
        $this->pemberian_diazepam = null;
        
        // Reset KEK form
        $this->edukasi_gizi = null;
        $this->kek_lainnya = null;
        
        // Reset obesitas form
        $this->edukasi_gizi_obesitas = null;
        $this->obesitas_lainnya = null;
        
        // Reset infeksi form
        $this->pemberian_antipiretik = null;
        $this->pemberian_antibiotik = null;
        $this->infeksi_lainnya = null;
        
        // Reset jantung form
        $this->edukasi = null;
        $this->jantung_lainnya = null;
        
        // Reset HIV form
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
        
        // Reset TB form
        $this->diperiksa_dahak = null;
        $this->tbc = null;
        $this->obat_tb = null;
        $this->sisa_obat = null;
        $this->tb_lainnya = null;
        
        // Reset malaria form
        $this->diberikan_kelambu = null;
        $this->darah_malaria_rdt = null;
        $this->darah_malaria_mikroskopis = null;
        $this->ibu_hamil_malaria_rdt = null;
        $this->ibu_hamil_malaria_mikroskopis = null;
        $this->hasil_test_malaria = null;
        $this->obat_malaria = null;
        $this->malaria_lainnya = null;
        
        // Reset flags/status
        $this->isEdit = false;
        $this->pemeriksaanId = null;
        $this->errorMessage = null;
    }
    
    /**
     * Batalkan perubahan dan reset form
     */
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
            }
        } catch (\Exception $e) {
            \Log::error('Error loading data for render', [
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
        // Reset semua formulir tatalaksana
        $this->resetTatalaksanaForms($this->jenis_tatalaksana);
    }
    
    /**
     * Reset semua form tatalaksana kecuali yang dipilih
     */
    private function resetTatalaksanaForms($kecualiJenis = null)
    {
        // Reset form anemia jika tatalaksana bukan anemia
        if ($kecualiJenis != 'Anemia') {
            $this->diberikan_tablet_fe = null;
            $this->jumlah_tablet_dikonsumsi = 0;
            $this->jumlah_tablet_ditambahkan = 0;
            $this->tatalaksana_lainnya = null;
        }
        
        // Reset form Makanan Tambahan Ibu Hamil jika tatalaksana bukan MT
        if ($kecualiJenis != 'Makanan Tambahan Ibu Hamil') {
            $this->pemberian_mt = null;
            $this->jumlah_mt = 0;
        }
        
        // Reset form Hipertensi jika tatalaksana bukan Hipertensi
        if ($kecualiJenis != 'Hipertensi') {
            $this->pantau_tekanan_darah = null;
            $this->pantau_protein_urine = null;
            $this->pantau_kondisi_janin = null;
            $this->hipertensi_lainnya = null;
        }
        
        // Reset form Eklampsia jika tatalaksana bukan Eklampsia
        if ($kecualiJenis != 'Eklampsia') {
            $this->pantau_tekanan_darah_eklampsia = null;
            $this->pantau_protein_urine_eklampsia = null;
            $this->pantau_kondisi_janin_eklampsia = null;
            $this->pemberian_antihipertensi = null;
            $this->pemberian_mgso4 = null;
            $this->pemberian_diazepam = null;
        }
        
        // Reset form KEK jika tatalaksana bukan KEK
        if ($kecualiJenis != 'KEK') {
            $this->edukasi_gizi = null;
            $this->kek_lainnya = null;
        }
        
        // Reset form Obesitas jika tatalaksana bukan Obesitas
        if ($kecualiJenis != 'Obesitas') {
            $this->edukasi_gizi_obesitas = null;
            $this->obesitas_lainnya = null;
        }
        
        // Reset form Infeksi jika tatalaksana bukan Infeksi
        if ($kecualiJenis != 'Infeksi') {
            $this->pemberian_antipiretik = null;
            $this->pemberian_antibiotik = null;
            $this->infeksi_lainnya = null;
        }
        
        // Reset form Penyakit Jantung jika tatalaksana bukan Penyakit Jantung
        if ($kecualiJenis != 'Penyakit Jantung') {
            $this->edukasi = null;
            $this->jantung_lainnya = null;
        }
        
        // Reset form HIV jika tatalaksana bukan HIV
        if ($kecualiJenis != 'HIV') {
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
        
        // Reset form TB jika tatalaksana bukan TB
        if ($kecualiJenis != 'TB') {
            $this->diperiksa_dahak = null;
            $this->tbc = null;
            $this->obat_tb = null;
            $this->sisa_obat = null;
            $this->tb_lainnya = null;
        }
        
        // Reset form Malaria jika tatalaksana bukan Malaria
        if ($kecualiJenis != 'Malaria') {
            $this->diberikan_kelambu = null;
            $this->darah_malaria_rdt = null;
            $this->darah_malaria_mikroskopis = null;
            $this->ibu_hamil_malaria_rdt = null;
            $this->ibu_hamil_malaria_mikroskopis = null;
            $this->hasil_test_malaria = null;
            $this->obat_malaria = null;
            $this->malaria_lainnya = null;
        }
    }
    
    /**
     * Hapus form tatalaksana yang dipilih
     */
    public function hapusFormTatalaksana($jenis)
    {
        if (empty($jenis)) {
            $jenis = $this->jenis_tatalaksana;
        }
        
        $this->jenis_tatalaksana = '';
        
        // Reset formulir sesuai jenis
        switch ($jenis) {
            case 'Anemia':
                $this->diberikan_tablet_fe = null;
                $this->jumlah_tablet_dikonsumsi = 0;
                $this->jumlah_tablet_ditambahkan = 0;
                $this->tatalaksana_lainnya = null;
                break;
                
            case 'Makanan Tambahan Ibu Hamil':
                $this->pemberian_mt = null;
                $this->jumlah_mt = 0;
                break;
                
            case 'Hipertensi':
                $this->pantau_tekanan_darah = null;
                $this->pantau_protein_urine = null;
                $this->pantau_kondisi_janin = null;
                $this->hipertensi_lainnya = null;
                break;
                
            case 'Eklampsia':
                $this->pantau_tekanan_darah_eklampsia = null;
                $this->pantau_protein_urine_eklampsia = null;
                $this->pantau_kondisi_janin_eklampsia = null;
                $this->pemberian_antihipertensi = null;
                $this->pemberian_mgso4 = null;
                $this->pemberian_diazepam = null;
                break;
                
            case 'KEK':
                $this->edukasi_gizi = null;
                $this->kek_lainnya = null;
                break;
                
            case 'Obesitas':
                $this->edukasi_gizi_obesitas = null;
                $this->obesitas_lainnya = null;
                break;
                
            case 'Infeksi':
                $this->pemberian_antipiretik = null;
                $this->pemberian_antibiotik = null;
                $this->infeksi_lainnya = null;
                break;
                
            case 'Penyakit Jantung':
                $this->edukasi = null;
                $this->jantung_lainnya = null;
                break;
                
            case 'HIV':
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
                break;
                
            case 'TB':
                $this->diperiksa_dahak = null;
                $this->tbc = null;
                $this->obat_tb = null;
                $this->sisa_obat = null;
                $this->tb_lainnya = null;
                break;
                
            case 'Malaria':
                $this->diberikan_kelambu = null;
                $this->darah_malaria_rdt = null;
                $this->darah_malaria_mikroskopis = null;
                $this->ibu_hamil_malaria_rdt = null;
                $this->ibu_hamil_malaria_mikroskopis = null;
                $this->hasil_test_malaria = null;
                $this->obat_malaria = null;
                $this->malaria_lainnya = null;
                break;
        }
    }
    
    // Fungsi-fungsi hapusForm spesifik yang memanggil fungsi umum
    public function hapusFormAnemia()
    {
        $this->hapusFormTatalaksana('Anemia');
    }
    
    public function hapusFormMT()
    {
        $this->hapusFormTatalaksana('Makanan Tambahan Ibu Hamil');
    }
    
    public function hapusFormHipertensi()
    {
        $this->hapusFormTatalaksana('Hipertensi');
    }
    
    public function hapusFormEklampsia()
    {
        $this->hapusFormTatalaksana('Eklampsia');
    }
    
    public function hapusFormKEK()
    {
        $this->hapusFormTatalaksana('KEK');
    }
    
    public function hapusFormObesitas()
    {
        $this->hapusFormTatalaksana('Obesitas');
    }
    
    public function hapusFormInfeksi()
    {
        $this->hapusFormTatalaksana('Infeksi');
    }
    
    public function hapusFormJantung()
    {
        $this->hapusFormTatalaksana('Penyakit Jantung');
    }
    
    public function hapusFormHIV()
    {
        $this->hapusFormTatalaksana('HIV');
    }
    
    public function hapusFormTB()
    {
        $this->hapusFormTatalaksana('TB');
    }
    
    public function hapusFormMalaria()
    {
        $this->hapusFormTatalaksana('Malaria');
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
        
        // Muat data pemeriksaan
        $this->loadExistingData($pemeriksaan);
    }
    
    /**
     * Helper function untuk memuat data yang sudah ada
     */
    private function loadExistingData($pemeriksaan)
    {
        $this->noRawat = $pemeriksaan->no_rawat;
        $this->noRm = $pemeriksaan->no_rkm_medis;
        
        // Tampilkan informasi pemeriksaan
        $this->tanggal_anc = $pemeriksaan->tanggal_anc;
        // Set tanggal_anc_input dalam format yang benar
        try {
            $this->tanggal_anc_input = Carbon::parse($pemeriksaan->tanggal_anc)->format('d/m/Y, H:i');
        } catch (\Exception $e) {
            // Fallback jika parse gagal
            $this->tanggal_anc_input = Carbon::now()->format('d/m/Y, H:i');
            \Log::error('Error formatting tanggal_anc_input in loadExistingData', [
                'input' => $pemeriksaan->tanggal_anc,
                'error' => $e->getMessage()
            ]);
        }
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
        
        // Menangani riwayat penyakit (bisa saja tersimpan sebagai JSON)
        if (is_string($pemeriksaan->riwayat_penyakit) && !empty($pemeriksaan->riwayat_penyakit)) {
            try {
                $this->riwayat_penyakit = json_decode($pemeriksaan->riwayat_penyakit, true);
            } catch (\Exception $e) {
                // Jika gagal parsing, inisialisasi ulang array
                $this->riwayat_penyakit = [
                    'hipertensi' => false,
                    'diabetes' => false,
                    'jantung' => false,
                    'asma' => false,
                    'lainnya_check' => false,
                    'lainnya' => null
                ];
                \Log::error('Error decoding riwayat_penyakit', [
                    'data' => $pemeriksaan->riwayat_penyakit,
                    'error' => $e->getMessage()
                ]);
            }
        } else if (is_array($pemeriksaan->riwayat_penyakit)) {
            $this->riwayat_penyakit = $pemeriksaan->riwayat_penyakit;
        } else {
            // Inisialisasi ulang array jika data tidak valid
            $this->riwayat_penyakit = [
                'hipertensi' => false,
                'diabetes' => false,
                'jantung' => false,
                'asma' => false,
                'lainnya_check' => false,
                'lainnya' => null
            ];
        }
        
        $this->lila = $pemeriksaan->lila;
        $this->status_gizi = $pemeriksaan->status_gizi;
        $this->tinggi_fundus = $pemeriksaan->tinggi_fundus;
        $this->taksiran_berat_janin = $pemeriksaan->taksiran_berat_janin;
        $this->denyut_jantung_janin = $pemeriksaan->denyut_jantung_janin;
        $this->presentasi = $pemeriksaan->presentasi;
        $this->presentasi_janin = $pemeriksaan->presentasi_janin;
        $this->status_tt = $pemeriksaan->status_tt;
        $this->tanggal_imunisasi = $pemeriksaan->tanggal_imunisasi;
        $this->tanggal_lab = $pemeriksaan->tanggal_lab;
        $this->lab = $pemeriksaan->lab;
        $this->rujukan_ims = $pemeriksaan->rujukan_ims;
        $this->tindak_lanjut = $pemeriksaan->tindak_lanjut;
        $this->detail_tindak_lanjut = $pemeriksaan->detail_tindak_lanjut;
        $this->tanggal_kunjungan_berikutnya = $pemeriksaan->tanggal_kunjungan_berikutnya;
        
        // Reset semua form tatalaksana terlebih dahulu
        $this->resetTatalaksanaForms($pemeriksaan->jenis_tatalaksana);
        
        // Data untuk tatalaksana spesifik
        switch($pemeriksaan->jenis_tatalaksana) {
            case 'Anemia':
                $this->diberikan_tablet_fe = $pemeriksaan->diberikan_tablet_fe;
                $this->jumlah_tablet_dikonsumsi = $pemeriksaan->jumlah_tablet_dikonsumsi;
                $this->jumlah_tablet_ditambahkan = $pemeriksaan->jumlah_tablet_ditambahkan;
                $this->tatalaksana_lainnya = $pemeriksaan->tatalaksana_lainnya;
                break;
                
            case 'Makanan Tambahan Ibu Hamil':
                $this->pemberian_mt = $pemeriksaan->pemberian_mt;
                $this->jumlah_mt = $pemeriksaan->jumlah_mt;
                break;
                
            case 'Hipertensi':
                $this->pantau_tekanan_darah = $pemeriksaan->pantau_tekanan_darah;
                $this->pantau_protein_urine = $pemeriksaan->pantau_protein_urine;
                $this->pantau_kondisi_janin = $pemeriksaan->pantau_kondisi_janin;
                $this->hipertensi_lainnya = $pemeriksaan->hipertensi_lainnya;
                break;
                
            case 'Eklampsia':
                $this->pantau_tekanan_darah_eklampsia = $pemeriksaan->pantau_tekanan_darah_eklampsia;
                $this->pantau_protein_urine_eklampsia = $pemeriksaan->pantau_protein_urine_eklampsia;
                $this->pantau_kondisi_janin_eklampsia = $pemeriksaan->pantau_kondisi_janin_eklampsia;
                $this->pemberian_antihipertensi = $pemeriksaan->pemberian_antihipertensi;
                $this->pemberian_mgso4 = $pemeriksaan->pemberian_mgso4;
                $this->pemberian_diazepam = $pemeriksaan->pemberian_diazepam;
                break;
                
            case 'KEK':
                $this->edukasi_gizi = $pemeriksaan->edukasi_gizi;
                $this->kek_lainnya = $pemeriksaan->kek_lainnya;
                break;
                
            case 'Obesitas':
                $this->edukasi_gizi_obesitas = $pemeriksaan->edukasi_gizi_obesitas;
                $this->obesitas_lainnya = $pemeriksaan->obesitas_lainnya;
                break;
                
            case 'Infeksi':
                $this->pemberian_antipiretik = $pemeriksaan->pemberian_antipiretik;
                $this->pemberian_antibiotik = $pemeriksaan->pemberian_antibiotik;
                $this->infeksi_lainnya = $pemeriksaan->infeksi_lainnya;
                break;
                
            case 'Penyakit Jantung':
                $this->edukasi = $pemeriksaan->edukasi;
                $this->jantung_lainnya = $pemeriksaan->jantung_lainnya;
                break;
                
            case 'HIV':
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
                break;
                
            case 'TB':
                $this->diperiksa_dahak = $pemeriksaan->diperiksa_dahak;
                $this->tbc = $pemeriksaan->tbc;
                $this->obat_tb = $pemeriksaan->obat_tb;
                $this->sisa_obat = $pemeriksaan->sisa_obat;
                $this->tb_lainnya = $pemeriksaan->tb_lainnya;
                break;
                
            case 'Malaria':
                $this->diberikan_kelambu = $pemeriksaan->diberikan_kelambu;
                $this->darah_malaria_rdt = $pemeriksaan->darah_malaria_rdt;
                $this->darah_malaria_mikroskopis = $pemeriksaan->darah_malaria_mikroskopis;
                $this->ibu_hamil_malaria_rdt = $pemeriksaan->ibu_hamil_malaria_rdt;
                $this->ibu_hamil_malaria_mikroskopis = $pemeriksaan->ibu_hamil_malaria_mikroskopis;
                $this->hasil_test_malaria = $pemeriksaan->hasil_test_malaria;
                $this->obat_malaria = $pemeriksaan->obat_malaria;
                $this->malaria_lainnya = $pemeriksaan->malaria_lainnya;
                break;
        }
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
                $this->usia_kehamilan_saat_ini = $diffInWeeks . ' minggu';
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

    /**
     * Memuat data untuk diedit
     */
    public function edit($id)
    {
        $this->resetValidation();
        $this->resetForm();
        
        $pemeriksaan = \App\Models\PemeriksaanAnc::where('id_anc', $id)->first();
        
        if (!$pemeriksaan) {
            session()->flash('error', 'Data pemeriksaan ANC tidak ditemukan.');
            return;
        }
        
        $this->isEdit = true;
        $this->pemeriksaanId = $pemeriksaan->id_anc;
        
        // Set semua properti dari data yang ada
        $this->loadExistingData($pemeriksaan);
        
        session()->flash('info', 'Mode edit aktif untuk pemeriksaan ANC dengan ID: ' . $id);
    }

    /**
     * Method untuk mengupdate array riwayat_penyakit
     */
    public function updateRiwayatPenyakit($key, $value)
    {
        $this->riwayat_penyakit[$key] = $value;
    }
}
