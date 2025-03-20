<?php

namespace App\Http\Livewire\Component\IlpDewasa;

use Livewire\Component;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\IlpDewasa;
use App\Helpers\UrlHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Schema;

class Form extends Component
{
    use LivewireAlert;  
    
    // Properti dasar
    public $noRawat;
    public $editMode = false;
    protected $pasien;
    public $ilpDewasa;
    
    // Properti data pasien yang akan diakses di view
    public $no_rkm_medis;
    public $nm_pasien;
    public $no_ktp;
    public $data_posyandu;
    public $jk;
    public $tmp_lahir;
    public $tgl_lahir;
    public $nm_ibu;
    public $alamat;
    public $gol_darah;
    public $pekerjaan;
    public $stts_nikah;
    public $agama;
    public $tgl_daftar;
    public $no_tlp;
    public $umur;
    public $pnd;
    public $keluarga;
    public $namakeluarga;
    public $kd_pj;
    public $no_peserta;
    public $no_kk;
    
    // Properti untuk pencarian posyandu
    public $searchPosyandu = '';
    public $posyanduList = [];
    public $showPosyanduDropdown = false;
    
    // Properti untuk pegawai yang login
    public $nip;
    
    // Properti form ILP Dewasa
    public $riwayat_diri_sendiri, $riwayat_keluarga, $merokok, $konsumsi_tinggi;
    public $berat_badan, $tinggi_badan, $imt, $lp, $td, $gula_darah;
    public $metode_mata, $hasil_mata, $tes_berbisik, $gigi;
    public $kesehatan_jiwa, $tbc, $fungsi_hari;
    public $status_tt, $penyakit_lain_catin, $kanker_payudara, $iva_test;
    public $resiko_jantung, $gds, $asam_urat, $kolesterol, $trigliserida;
    public $charta, $ureum, $kreatinin, $resiko_kanker_usus, $skor_puma, $skilas;
    
    // Listener untuk event
    protected $listeners = [
        'hapusIlpDewasa' => 'hapusIlpDewasa',
        'hapusRalanIlpDewasa' => 'hapusRalanIlpDewasa',
        'setNoRawat' => 'setNoRawat'
    ];

    // Event yang akan dipancarkan
    protected $dispatchBrowserEvents = ['closeModal'];

    /**
     * Menampilkan form ILP Dewasa
     *
     * @param string $noRawat
     * @return \Illuminate\View\View
     */
    public function mount($noRawat = null)
    {
        if (!$noRawat) {
            $this->alert('error', 'Parameter no_rawat tidak ditemukan');
            return;
        }
        
        // Simpan noRawat
        $this->noRawat = $noRawat;
        
        Log::info('Mengakses ILP Dewasa dengan noRawat: ' . $this->noRawat);
        
        // Ambil NIP dari user yang login
        $this->nip = Auth::user()->pegawai->nip ?? null;
        
        $this->loadData();
    }
    
    /**
     * Load data pasien dan ILP Dewasa
     *
     * @return void
     */
    public function loadData()
    {
        try {
            // Ambil data pasien dan reg_periksa
            $this->pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.nm_ibu',
                    'pasien.alamat',
                    'pasien.gol_darah',
                    'pasien.pekerjaan',
                    'pasien.stts_nikah',
                    'pasien.agama',
                    'pasien.tgl_daftar',
                    'pasien.no_tlp',
                    'pasien.umur',
                    'pasien.pnd',
                    'pasien.keluarga',
                    'pasien.namakeluarga',
                    'pasien.kd_pj',
                    'pasien.no_peserta',
                    'pasien.no_kk',
                    'pasien.data_posyandu'
                )
                ->where('reg_periksa.no_rawat', $this->noRawat)
                ->first();
                
            if (!$this->pasien) {
                $this->alert('error', 'Data pasien tidak ditemukan');
                Log::warning('Data pasien tidak ditemukan untuk no_rawat: ' . $this->noRawat);
                return;
            }
            
            // Log untuk debugging
            Log::info('Data pasien ditemukan: ' . $this->pasien->nm_pasien . ', No KTP: ' . $this->pasien->no_ktp);
            
            // Set properti publik untuk data pasien
            $this->no_rkm_medis = $this->pasien->no_rkm_medis ?? '';
            $this->nm_pasien = $this->pasien->nm_pasien ?? '';
            $this->no_ktp = $this->pasien->no_ktp ?? '';
            $this->jk = $this->pasien->jk ?? '';
            $this->tmp_lahir = $this->pasien->tmp_lahir ?? '';
            $this->tgl_lahir = $this->pasien->tgl_lahir ?? '';
            $this->nm_ibu = $this->pasien->nm_ibu ?? '';
            $this->alamat = $this->pasien->alamat ?? '';
            $this->gol_darah = $this->pasien->gol_darah ?? '';
            $this->pekerjaan = $this->pasien->pekerjaan ?? '';
            $this->stts_nikah = $this->pasien->stts_nikah ?? '';
            $this->agama = $this->pasien->agama ?? '';
            $this->tgl_daftar = $this->pasien->tgl_daftar ?? '';
            $this->no_tlp = $this->pasien->no_tlp ?? '';
            $this->umur = $this->pasien->umur ?? '';
            $this->pnd = $this->pasien->pnd ?? '';
            $this->keluarga = $this->pasien->keluarga ?? '';
            $this->namakeluarga = $this->pasien->namakeluarga ?? '';
            $this->kd_pj = $this->pasien->kd_pj ?? '';
            $this->no_peserta = $this->pasien->no_peserta ?? '';
            $this->no_kk = $this->pasien->no_kk ?? '';
            $this->data_posyandu = $this->pasien->data_posyandu ?? '';
            
            // Cek apakah data ILP Dewasa sudah ada
            $this->ilpDewasa = IlpDewasa::where('no_rawat', $this->noRawat)->first();
            
            // Jika data sudah ada, isi properti dengan data yang ada
            if ($this->ilpDewasa) {
                Log::info('Data ILP Dewasa ditemukan untuk no_rawat: ' . $this->noRawat);
                
                // Tetap gunakan no_ktp dari pasien
                $this->no_ktp = $this->pasien->no_ktp ?? '';
                
                // Gunakan data_posyandu dari ILP Dewasa jika ada, jika tidak gunakan dari pasien
                $this->data_posyandu = $this->ilpDewasa->data_posyandu ?? $this->data_posyandu;
                
                $this->riwayat_diri_sendiri = $this->ilpDewasa->riwayat_diri_sendiri;
                $this->riwayat_keluarga = $this->ilpDewasa->riwayat_keluarga;
                $this->merokok = $this->ilpDewasa->merokok;
                $this->konsumsi_tinggi = $this->ilpDewasa->konsumsi_tinggi;
                $this->berat_badan = $this->ilpDewasa->berat_badan;
                $this->tinggi_badan = $this->ilpDewasa->tinggi_badan;
                $this->imt = $this->ilpDewasa->imt;
                $this->lp = $this->ilpDewasa->lp;
                $this->td = $this->ilpDewasa->td;
                $this->gula_darah = $this->ilpDewasa->gula_darah;
                $this->metode_mata = $this->ilpDewasa->metode_mata;
                $this->hasil_mata = $this->ilpDewasa->hasil_mata;
                $this->tes_berbisik = $this->ilpDewasa->tes_berbisik;
                $this->gigi = $this->ilpDewasa->gigi;
                $this->kesehatan_jiwa = $this->ilpDewasa->kesehatan_jiwa;
                $this->tbc = $this->ilpDewasa->tbc;
                $this->fungsi_hari = $this->ilpDewasa->fungsi_hari;
                $this->status_tt = $this->ilpDewasa->status_tt;
                $this->penyakit_lain_catin = $this->ilpDewasa->penyakit_lain_catin;
                $this->kanker_payudara = $this->ilpDewasa->kanker_payudara;
                $this->iva_test = $this->ilpDewasa->iva_test;
                $this->resiko_jantung = $this->ilpDewasa->resiko_jantung;
                $this->gds = $this->ilpDewasa->gds;
                $this->asam_urat = $this->ilpDewasa->asam_urat;
                $this->kolesterol = $this->ilpDewasa->kolesterol;
                $this->trigliserida = $this->ilpDewasa->trigliserida;
                $this->charta = $this->ilpDewasa->charta;
                $this->ureum = $this->ilpDewasa->ureum;
                $this->kreatinin = $this->ilpDewasa->kreatinin;
                $this->resiko_kanker_usus = $this->ilpDewasa->resiko_kanker_usus;
                $this->skor_puma = $this->ilpDewasa->skor_puma;
                $this->skilas = $this->ilpDewasa->skilas;
                $this->nip = $this->ilpDewasa->nip ?? $this->nip;
            } else {
                Log::info('Data ILP Dewasa belum ada untuk no_rawat: ' . $this->noRawat);
                
                // Set default value untuk riwayat
                $this->riwayat_diri_sendiri = 'Normal';
                $this->riwayat_keluarga = 'Normal';
            }
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasa Form Livewire: ' . $e->getMessage());
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Pencarian data posyandu
     */
    public function updatedSearchPosyandu()
    {
        Log::info('Memulai pencarian posyandu dengan kata kunci: ' . $this->searchPosyandu);
        
        if (strlen($this->searchPosyandu) >= 2) {
            try {
                // Cek apakah tabel data_posyandu ada
                if (Schema::hasTable('data_posyandu')) {
                    // Gunakan query sederhana untuk debug
                    $query = "SELECT kode_posyandu, nama_posyandu, alamat, desa FROM data_posyandu 
                             WHERE nama_posyandu LIKE '%{$this->searchPosyandu}%' 
                             OR alamat LIKE '%{$this->searchPosyandu}%' 
                             OR desa LIKE '%{$this->searchPosyandu}%' 
                             LIMIT 8";
                    
                    Log::info('SQL Query: ' . $query);
                    
                    $this->posyanduList = DB::select($query);
                    
                    // Debugging
                    Log::info('Jumlah hasil pencarian posyandu: ' . count($this->posyanduList));
                    foreach ($this->posyanduList as $key => $item) {
                        Log::info("Item {$key}: " . json_encode($item));
                    }
                    
                    // Force re-render
                    $this->emit('refreshComponent');
                } else {
                    // Jika tabel tidak ada, gunakan array kosong
                    $this->posyanduList = [];
                    Log::warning('Tabel data_posyandu tidak ditemukan');
                }
            } catch (\Exception $e) {
                Log::error('Error pada pencarian posyandu: ' . $e->getMessage());
                $this->posyanduList = [];
            }
        } else {
            $this->posyanduList = [];
        }
    }
    
    /**
     * Pilih data posyandu
     */
    public function selectPosyandu($nama, $alamat, $desa = null)
    {
        try {
            // Log untuk debugging
            Log::info('Memilih posyandu dengan parameter: ' . 
                     'Nama: ' . $nama . 
                     ', Alamat: ' . $alamat . 
                     ', Desa: ' . ($desa ?? 'null'));
            
            // Bersihkan data dari karakter yang mungkin menyebabkan masalah
            $nama = str_replace("'", "", $nama);
            $alamat = str_replace("'", "", $alamat);
            $desa = str_replace("'", "", $desa);
            
            // Gunakan desa jika tersedia, jika tidak gunakan alamat
            $lokasi = !empty($desa) && $desa != '-' ? $desa : $alamat;
            
            // Set data posyandu dengan format yang benar
            $this->data_posyandu = $nama . ' - ' . $lokasi;
            
            // Reset pencarian dan list
            $this->searchPosyandu = '';
            $this->posyanduList = [];
            
            // Log hasil
            Log::info('Data posyandu yang dipilih dan diset ke property: ' . $this->data_posyandu);
            
            // Emit event untuk menutup modal dari JavaScript
            $this->dispatchBrowserEvent('closeModal', ['modalId' => 'posyanduModal']);
            
            // Force re-render untuk memastikan tampilan terupdate
            $this->emit('refreshComponent');
            
            // Perbarui data pasien jika diperlukan
            if ($this->pasien && property_exists($this->pasien, 'no_rkm_medis')) {
                try {
                    $affected = DB::table('pasien')
                        ->where('no_rkm_medis', $this->pasien->no_rkm_medis)
                        ->update(['data_posyandu' => $this->data_posyandu]);
                    
                    Log::info('Data posyandu pasien berhasil diperbarui. Affected rows: ' . $affected);
                } catch (\Exception $e) {
                    Log::error('Gagal memperbarui data posyandu pasien: ' . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Error pada selectPosyandu: ' . $e->getMessage());
            // Alert user
            $this->alert('error', 'Terjadi kesalahan saat memilih posyandu: ' . $e->getMessage());
        }
    }
    
    /**
     * Menyimpan data ILP Dewasa
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'no_rawat' => 'required|string|max:17',
                'berat_badan' => 'nullable|string|max:5',
                'tinggi_badan' => 'nullable|string|max:5',
                'imt' => 'nullable|string|max:5',
                'lp' => 'nullable|string|max:4',
                'td' => 'nullable|string|max:8',
                'gula_darah' => 'nullable|string|max:4',
                'metode_mata' => 'nullable|string',
                'hasil_mata' => 'nullable|string',
                'tes_berbisik' => 'nullable|string',
                'gigi' => 'nullable|string',
                'kesehatan_jiwa' => 'nullable|string',
                'tbc' => 'nullable|string|max:50',
                'fungsi_hari' => 'nullable|string',
                'status_tt' => 'nullable|string',
                'penyakit_lain_catin' => 'nullable|string',
                'kanker_payudara' => 'nullable|string',
                'iva_test' => 'nullable|string',
                'resiko_jantung' => 'nullable|string',
                'gds' => 'nullable|string|max:5',
                'asam_urat' => 'nullable|string|max:5',
                'kolesterol' => 'nullable|string|max:5',
                'trigliserida' => 'nullable|string|max:5',
                'charta' => 'nullable|string',
                'ureum' => 'nullable|string|max:6',
                'kreatinin' => 'nullable|string|max:6',
                'resiko_kanker_usus' => 'nullable|string',
                'skor_puma' => 'nullable|string',
                'skilas' => 'nullable|string|max:100',
                'riwayat_diri_sendiri' => 'nullable|string',
                'riwayat_keluarga' => 'nullable|string',
                'merokok' => 'nullable|string',
                'konsumsi_tinggi' => 'nullable|string|max:25',
            ]);
            
            // Ambil data pasien untuk field yang tidak diinput
            $pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'pasien.tgl_lahir',
                    'pasien.stts_nikah',
                    'pasien.jk',
                    'pasien.no_kk',
                    'pasien.no_tlp',
                    'pasien.pekerjaan',
                    'pasien.no_ktp'
                )
                ->where('reg_periksa.no_rawat', $request->no_rawat)
                ->first();
            
            // Cek apakah data sudah ada
            $ilpDewasa = IlpDewasa::where('no_rawat', $request->no_rawat)->first();
            
            if ($ilpDewasa) {
                // Update data yang sudah ada
                $ilpDewasa->update(array_merge($validated, [
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'no_ktp' => $pasien->no_ktp ?? null,
                ]));
            } else {
                // Buat data baru
                $ilpDewasa = IlpDewasa::create(array_merge($validated, [
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'tgl_lahir' => $pasien->tgl_lahir ?? null,
                    'stts_nikah' => $pasien->stts_nikah ?? '',
                    'jk' => $pasien->jk ?? '',
                    'no_kk' => $pasien->no_kk ?? '',
                    'no_tlp' => $pasien->no_tlp ?? '',
                    'pekerjaan' => $pasien->pekerjaan ?? '',
                    'no_ktp' => $pasien->no_ktp ?? null,
                ]));
            }
            
            return redirect()->route('ralan.pasien')->with('success', 'Data ILP Dewasa berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasaController@store: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menghapus data ILP Dewasa
     *
     * @param string $noRawat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($noRawat)
    {
        try {
            $ilpDewasa = IlpDewasa::where('no_rawat', $noRawat)->first();
            
            if ($ilpDewasa) {
                $ilpDewasa->delete();
                return redirect()->route('ralan.pasien')->with('success', 'Data ILP Dewasa berhasil dihapus');
            }
            
            return redirect()->route('ralan.pasien')->with('error', 'Data ILP Dewasa tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasaController@destroy: ' . $e->getMessage());
            return redirect()->route('ralan.pasien')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Hitung IMT otomatis
     */
    public function hitungIMT()
    {
        if ($this->berat_badan && $this->tinggi_badan) {
            $tb = $this->tinggi_badan / 100; // Konversi ke meter
            $this->imt = (string) round($this->berat_badan / ($tb * $tb), 2);
        }
    }
    
    /**
     * Simpan data ILP Dewasa
     */
    public function simpan()
    {
        try {
            // Pastikan noRawat tidak null
            if (empty($this->noRawat)) {
                $this->alert('error', 'No Rawat tidak boleh kosong');
                return;
            }
            
            // Pastikan semua nilai numerik dikonversi ke string
            $numericFields = ['berat_badan', 'tinggi_badan', 'imt', 'lp', 'gula_darah', 'gds', 'asam_urat', 'kolesterol', 'trigliserida', 'ureum', 'kreatinin'];
            
            foreach ($numericFields as $field) {
                if ($this->$field !== null && !is_string($this->$field)) {
                    $this->$field = (string) $this->$field;
                }
            }
            
            // Mengatur nilai default untuk field enum agar tidak null saat disimpan
            if (empty($this->riwayat_diri_sendiri)) {
                $this->riwayat_diri_sendiri = 'Normal';
            }
            
            if (empty($this->riwayat_keluarga)) {
                $this->riwayat_keluarga = 'Normal';
            }
            
            if (empty($this->merokok)) {
                $this->merokok = 'Tidak';
            }
            
            if (empty($this->metode_mata)) {
                $this->metode_mata = 'snelen card';
            }
            
            if (empty($this->hasil_mata)) {
                $this->hasil_mata = 'normal';
            }
            
            if (empty($this->tes_berbisik)) {
                $this->tes_berbisik = 'normal';
            }
            
            if (empty($this->gigi)) {
                $this->gigi = 'normal';
            }
            
            if (empty($this->kesehatan_jiwa)) {
                $this->kesehatan_jiwa = 'normal';
            }
            
            if (empty($this->fungsi_hari)) {
                $this->fungsi_hari = 'Normal';
            }
            
            if (empty($this->status_tt)) {
                $this->status_tt = '-';
            }
            
            if (empty($this->penyakit_lain_catin)) {
                $this->penyakit_lain_catin = 'Normal';
            }
            
            if (empty($this->kanker_payudara)) {
                $this->kanker_payudara = 'Normal';
            }
            
            if (empty($this->iva_test)) {
                $this->iva_test = 'Negatif';
            }
            
            if (empty($this->resiko_jantung)) {
                $this->resiko_jantung = 'Tidak';
            }
            
            if (empty($this->charta)) {
                $this->charta = '<10%';
            }
            
            if (empty($this->resiko_kanker_usus)) {
                $this->resiko_kanker_usus = 'Tidak';
            }
            
            if (empty($this->skor_puma)) {
                $this->skor_puma = '< 6';
            }
            
            // Validasi input
            $this->validate([
                'no_ktp' => 'nullable|string|max:20',
                'data_posyandu' => 'nullable|string|max:100',
                'berat_badan' => 'nullable|string|max:5',
                'tinggi_badan' => 'nullable|string|max:5',
                'imt' => 'nullable|string|max:5',
                'lp' => 'nullable|string|max:4',
                'td' => 'nullable|string|max:8',
                'gula_darah' => 'nullable|string|max:4',
                'metode_mata' => 'nullable|in:hitungjari,visus,pinhole,snelen card',
                'hasil_mata' => 'nullable|in:normal,tidak normal',
                'tes_berbisik' => 'nullable|in:normal,tidak normal',
                'gigi' => 'nullable|in:normal,caries,jaringan Periodental,goyang',
                'kesehatan_jiwa' => 'nullable|in:normal,gangguan emosional,gangguan perilaku',
                'tbc' => 'nullable|string|max:50',
                'fungsi_hari' => 'nullable|in:Normal,Hepatitis B,Hepatitis C,Sirosis',
                'status_tt' => 'nullable|in:-,1,2,3,4,5',
                'penyakit_lain_catin' => 'nullable|in:Normal,Anemia,HIV,Sifilis,Napza',
                'kanker_payudara' => 'nullable|in:Normal,ada benjolan',
                'iva_test' => 'nullable|in:Negatif,Positif',
                'resiko_jantung' => 'nullable|in:Ya,Tidak',
                'gds' => 'nullable|string|max:5',
                'asam_urat' => 'nullable|string|max:5',
                'kolesterol' => 'nullable|string|max:5',
                'trigliserida' => 'nullable|string|max:5',
                'charta' => 'nullable|in:<10%,10% - 20%,20% - 30%,30% - 40%,> 40%',
                'ureum' => 'nullable|string|max:6',
                'kreatinin' => 'nullable|string|max:6',
                'resiko_kanker_usus' => 'nullable|in:Ya,Tidak',
                'skor_puma' => 'nullable|in:< 6,> 6',
                'skilas' => 'nullable|string|max:100',
                'riwayat_diri_sendiri' => 'nullable|in:Hipertensi,Diabetes militus,Stroke,Jantung,Asma,Kanker,Kolesterol,Hepatitis,Normal',
                'riwayat_keluarga' => 'nullable|in:Hipertensi,Diabetes militus,Stroke,Jantung,Asma,Kanker,Kolesterol,Hepatitis,Normal',
                'merokok' => 'nullable|in:Ya,Tidak',
                'konsumsi_tinggi' => 'nullable|string|max:25',
            ]);
            
            // Ambil data pasien untuk memastikan no_ktp valid
            $pasienData = DB::table('pasien')
                ->join('reg_periksa', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
                ->where('reg_periksa.no_rawat', $this->noRawat)
                ->select('pasien.no_ktp', 'pasien.no_rkm_medis', 'pasien.tgl_lahir', 'pasien.stts_nikah', 
                         'pasien.jk', 'pasien.no_kk', 'pasien.no_tlp', 'pasien.pekerjaan')
                ->first();
            
            if (!$pasienData) {
                $this->alert('error', 'Data pasien tidak ditemukan');
                return;
            }
            
            // Gunakan no_ktp dari data pasien untuk menghindari foreign key constraint error
            $data = [
                'no_rawat' => $this->noRawat,
                'no_rkm_medis' => $pasienData->no_rkm_medis,
                'no_ktp' => $pasienData->no_ktp, // Gunakan no_ktp dari database
                'data_posyandu' => $this->data_posyandu,
                'nip' => $this->nip,
                'tanggal' => Carbon::now()->format('Y-m-d'),
                'tgl_lahir' => $pasienData->tgl_lahir ?? null,
                'stts_nikah' => $pasienData->stts_nikah ?? '',
                'jk' => $pasienData->jk ?? '',
                'no_kk' => $pasienData->no_kk ?? '',
                'no_tlp' => $pasienData->no_tlp ?? '',
                'pekerjaan' => $pasienData->pekerjaan ?? '',
                'riwayat_diri_sendiri' => $this->riwayat_diri_sendiri,
                'riwayat_keluarga' => $this->riwayat_keluarga,
                'merokok' => $this->merokok,
                'konsumsi_tinggi' => $this->konsumsi_tinggi,
                'berat_badan' => $this->berat_badan,
                'tinggi_badan' => $this->tinggi_badan,
                'imt' => $this->imt,
                'lp' => $this->lp,
                'td' => $this->td,
                'gula_darah' => $this->gula_darah,
                'metode_mata' => $this->metode_mata,
                'hasil_mata' => $this->hasil_mata,
                'tes_berbisik' => $this->tes_berbisik,
                'gigi' => $this->gigi,
                'kesehatan_jiwa' => $this->kesehatan_jiwa,
                'tbc' => $this->tbc,
                'fungsi_hari' => $this->fungsi_hari,
                'status_tt' => $this->status_tt,
                'penyakit_lain_catin' => $this->penyakit_lain_catin,
                'kanker_payudara' => $this->kanker_payudara,
                'iva_test' => $this->iva_test,
                'resiko_jantung' => $this->resiko_jantung,
                'gds' => $this->gds,
                'asam_urat' => $this->asam_urat,
                'kolesterol' => $this->kolesterol,
                'trigliserida' => $this->trigliserida,
                'charta' => $this->charta,
                'ureum' => $this->ureum,
                'kreatinin' => $this->kreatinin,
                'resiko_kanker_usus' => $this->resiko_kanker_usus,
                'skor_puma' => $this->skor_puma,
                'skilas' => $this->skilas,
            ];
            
            // Debug untuk memeriksa nilai noRawat dan no_ktp
            Log::info('Nilai noRawat saat simpan: ' . $this->noRawat);
            Log::info('Nilai no_ktp dari database: ' . $pasienData->no_ktp);
            
            // Cek apakah data sudah ada
            if ($this->ilpDewasa) {
                // Update data yang sudah ada
                $this->ilpDewasa->update($data);
                $message = 'Data ILP Dewasa berhasil diperbarui';
            } else {
                // Buat data baru
                IlpDewasa::create($data);
                $message = 'Data ILP Dewasa berhasil disimpan';
            }
            
            // Update status di tabel reg_periksa menjadi 'Sudah'
            try {
                DB::table('reg_periksa')
                    ->where('no_rawat', $this->noRawat)
                    ->update(['stts' => 'Sudah']);
                
                Log::info('Status reg_periksa berhasil diupdate menjadi Sudah untuk no_rawat: ' . $this->noRawat);
            } catch (\Exception $e) {
                Log::error('Gagal mengupdate status reg_periksa: ' . $e->getMessage());
                // Tidak perlu throw exception di sini, karena kita masih ingin melanjutkan proses
            }
            
            $this->alert('success', $message);
            $this->emit('ilpDewasaSaved');
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasa Form Livewire simpan: ' . $e->getMessage());
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Hapus data ILP Dewasa
     */
    public function hapusIlpDewasa()
    {
        try {
            // Log untuk debugging
            Log::info('Menghapus ILP Dewasa dengan no_rawat: ' . $this->noRawat);
            
            $ilpDewasa = IlpDewasa::where('no_rawat', $this->noRawat)->first();
            
            if ($ilpDewasa) {
                $ilpDewasa->delete();
                
                // Update status di tabel reg_periksa menjadi 'Belum'
                try {
                    DB::table('reg_periksa')
                        ->where('no_rawat', $this->noRawat)
                        ->update(['stts' => 'Belum']);
                    
                    Log::info('Status reg_periksa berhasil diupdate menjadi Belum untuk no_rawat: ' . $this->noRawat);
                } catch (\Exception $e) {
                    Log::error('Gagal mengupdate status reg_periksa: ' . $e->getMessage());
                    // Tidak perlu throw exception di sini, karena kita masih ingin melanjutkan proses
                }
                
                $this->alert('success', 'Data ILP Dewasa berhasil dihapus');
                $this->emit('ilpDewasaDeleted');
                Log::info('ILP Dewasa berhasil dihapus');
                return redirect()->route('ralan.pasien');
            }
            
            $this->alert('error', 'Data ILP Dewasa tidak ditemukan');
            Log::warning('ILP Dewasa tidak ditemukan untuk no_rawat: ' . $this->noRawat);
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasa Form Livewire hapus: ' . $e->getMessage());
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Set no_rawat dari event
     */
    public function setNoRawat($noRawat)
    {
        // Simpan noRawat
        $this->noRawat = $noRawat;
        
        Log::info('setNoRawat dipanggil dengan nilai: ' . $this->noRawat);
        
        $this->loadData();
    }
    
    /**
     * Hapus data ILP Dewasa dari event
     */
    public function hapusRalanIlpDewasa()
    {
        try {
            // Log untuk debugging
            Log::info('Menghapus ILP Dewasa dengan no_rawat: ' . $this->noRawat);
            
            $ilpDewasa = IlpDewasa::where('no_rawat', $this->noRawat)->first();
            
            if ($ilpDewasa) {
                $ilpDewasa->delete();
                
                // Update status di tabel reg_periksa menjadi 'Belum'
                try {
                    DB::table('reg_periksa')
                        ->where('no_rawat', $this->noRawat)
                        ->update(['stts' => 'Belum']);
                    
                    Log::info('Status reg_periksa berhasil diupdate menjadi Belum untuk no_rawat: ' . $this->noRawat);
                } catch (\Exception $e) {
                    Log::error('Gagal mengupdate status reg_periksa: ' . $e->getMessage());
                    // Tidak perlu throw exception di sini, karena kita masih ingin melanjutkan proses
                }
                
                $this->emit('ilpDewasaDeleted');
                Log::info('ILP Dewasa berhasil dihapus');
            } else {
                $this->alert('error', 'Data ILP Dewasa tidak ditemukan');
                Log::warning('ILP Dewasa tidak ditemukan untuk no_rawat: ' . $this->noRawat);
            }
        } catch (\Exception $e) {
            Log::error('Error pada hapusRalanIlpDewasa: ' . $e->getMessage());
            $this->alert('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.component.ilp-dewasa.form');
    }
}
