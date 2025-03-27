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
            $this->tanggal_anc = now()->format('Y-m-d\TH:i:s');
            
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
            $regPeriksa = DB::table('reg_periksa')
                ->where('no_rawat', $this->noRawat)
                ->first();

            if ($regPeriksa && empty($this->noRm)) {
                $this->noRm = $regPeriksa->no_rkm_medis;
            }

            \Log::info('PemeriksaanANC mount completed', [
                'noRawat' => $this->noRawat,
                'noRm' => $this->noRm,
                'diperiksa_oleh' => $this->diperiksa_oleh
            ]);
        } catch (\Exception $e) {
            \Log::error('PemeriksaanANC mount error', [
                'error' => $e->getMessage(),
                'noRawat' => $noRawat,
                'noRm' => $noRm
            ]);
        }
    }

    protected function rules()
    {
        $rules = [
            'tanggal_anc' => 'required|date',
            'diperiksa_oleh' => 'required|string|max:255',
            'usia_kehamilan' => 'required|integer|min:1',
            'trimester' => 'required|integer|in:1,2,3',
            'kunjungan_ke' => 'nullable|integer',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'imt' => 'required|numeric',
            'kategori_imt' => 'required|string|max:255',
            'jumlah_janin' => 'nullable|string',
            'td_sistole' => 'required|integer',
            'td_diastole' => 'required|integer',
            'jumlah_fe' => 'required|integer',
            'dosis' => 'required|integer',
            'pemeriksaan_lab' => 'nullable|string|max:255',
            'jenis_tatalaksana' => 'nullable|string|max:255',
            'materi' => 'required|string',
            'rekomendasi' => 'required|string',
            'konseling_menyusui' => 'required|in:Ya,Tidak',
            'tanda_bahaya_kehamilan' => 'required|in:Ya,Tidak',
            'tanda_bahaya_persalinan' => 'required|in:Ya,Tidak',
            'konseling_phbs' => 'required|in:Ya,Tidak',
            'konseling_gizi' => 'required|in:Ya,Tidak',
            'konseling_ibu_hamil' => 'required|in:Ya,Tidak',
            'konseling_lainnya' => 'nullable|string|max:255',
            'keadaan_pulang' => 'required|string|max:255',
            'diberikan_tablet_fe' => 'nullable|in:Ya,Tidak',
            'jumlah_tablet_dikonsumsi' => 'nullable|integer|min:0',
            'jumlah_tablet_ditambahkan' => 'nullable|integer|min:0',
            'tatalaksana_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Makanan Tambahan Ibu Hamil
            'pemberian_mt' => 'nullable|in:MT Lokal,MT Pabrikan',
            'jumlah_mt' => 'nullable|integer|min:0',
            
            // Aturan validasi untuk form Hipertensi
            'pantau_tekanan_darah' => 'nullable|in:Ya,Tidak',
            'pantau_protein_urine' => 'nullable|in:Ya,Tidak',
            'pantau_kondisi_janin' => 'nullable|in:Ya,Tidak',
            'hipertensi_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Eklampsia
            'pantau_tekanan_darah_eklampsia' => 'nullable|in:Ya,Tidak',
            'pantau_protein_urine_eklampsia' => 'nullable|in:Ya,Tidak',
            'pantau_kondisi_janin_eklampsia' => 'nullable|in:Ya,Tidak',
            'pemberian_antihipertensi' => 'nullable|in:Ya,Tidak',
            'pemberian_mgso4' => 'nullable|in:Ya,Tidak',
            'pemberian_diazepam' => 'nullable|in:Ya,Tidak',
            
            // Aturan validasi untuk form KEK
            'edukasi_gizi' => 'nullable|in:Ya,Tidak',
            'kek_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Obesitas
            'edukasi_gizi_obesitas' => 'nullable|in:Ya,Tidak',
            'obesitas_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Infeksi
            'pemberian_antipiretik' => 'nullable|in:Ya,Tidak',
            'pemberian_antibiotik' => 'nullable|in:Ya,Tidak',
            'infeksi_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Penyakit Jantung
            'edukasi' => 'nullable|in:Ya,Tidak',
            'jantung_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form HIV
            'datang_dengan_hiv' => 'nullable|in:Negatif (-),Positif (+)',
            'persalinan_pervaginam' => 'nullable|in:Negatif (-),Positif (+)',
            'persalinan_perapdoinam' => 'nullable|in:Negatif (-),Positif (+)',
            'ditawarkan_tes' => 'nullable|in:Ya,Tidak',
            'dilakukan_tes' => 'nullable|in:Ya,Tidak',
            'hasil_tes_hiv' => 'nullable|in:Negatif (-),Positif (+)',
            'mendapatkan_art' => 'nullable|in:Ya,Tidak',
            'vct_pict' => 'nullable|in:Ya,Tidak',
            'periksa_darah' => 'nullable|in:Ya,Tidak',
            'serologi' => 'nullable|in:Negatif (-),Positif (+)',
            'arv_profilaksis' => 'nullable|string',
            'hiv_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form TB
            'diperiksa_dahak' => 'nullable|in:Ya,Tidak',
            'tbc' => 'nullable|in:Negatif (-),Positif (+)',
            'obat_tb' => 'nullable|string',
            'sisa_obat' => 'nullable|string|max:255',
            'tb_lainnya' => 'nullable|string|max:255',
            
            // Aturan validasi untuk form Malaria
            'diberikan_kelambu' => 'nullable|in:Ya,Tidak',
            'darah_malaria_rdt' => 'nullable|in:Ya,Tidak',
            'darah_malaria_mikroskopis' => 'nullable|in:Ya,Tidak',
            'ibu_hamil_malaria_rdt' => 'nullable|in:Ya,Tidak',
            'ibu_hamil_malaria_mikroskopis' => 'nullable|in:Ya,Tidak',
            'hasil_test_malaria' => 'nullable|in:Negatif (-),Positif (+)',
            'obat_malaria' => 'nullable|string',
            'malaria_lainnya' => 'nullable|string|max:255',
        ];
        
        return $rules;
    }

    protected $messages = [
        'tanggal_anc.required' => 'Tanggal ANC wajib diisi',
        'diperiksa_oleh.required' => 'Nama pemeriksa wajib diisi',
        'usia_kehamilan.required' => 'Usia kehamilan wajib diisi',
        'usia_kehamilan.integer' => 'Usia kehamilan harus berupa angka',
        'trimester.required' => 'Trimester wajib diisi',
        'berat_badan.required' => 'Berat badan wajib diisi',
        'tinggi_badan.required' => 'Tinggi badan wajib diisi',
        'imt.required' => 'IMT wajib diisi',
        'kategori_imt.required' => 'Kategori IMT wajib diisi',
        'td_sistole.required' => 'TD Sistole wajib diisi',
        'td_diastole.required' => 'TD Diastole wajib diisi',
        'materi.required' => 'Materi wajib diisi',
        'rekomendasi.required' => 'Rekomendasi wajib diisi',
        'konseling_menyusui.required' => 'Konseling menyusui wajib diisi',
        'tanda_bahaya_kehamilan.required' => 'Tanda bahaya kehamilan wajib diisi',
        'tanda_bahaya_persalinan.required' => 'Tanda bahaya persalinan wajib diisi',
        'konseling_phbs.required' => 'Konseling PHBS wajib diisi',
        'konseling_gizi.required' => 'Konseling gizi ibu hamil wajib diisi',
        'konseling_ibu_hamil.required' => 'Konseling ibu hamil wajib diisi',
        'keadaan_pulang.required' => 'Keadaan pulang wajib diisi',
        
        // Pesan validasi untuk form anemia
        'diberikan_tablet_fe.required' => 'Harap pilih Ya atau Tidak',
        'jumlah_tablet_dikonsumsi.required' => 'Jumlah tablet yang dikonsumsi wajib diisi',
        'jumlah_tablet_dikonsumsi.integer' => 'Jumlah tablet harus berupa angka',
        'jumlah_tablet_ditambahkan.required' => 'Jumlah tablet yang ditambahkan wajib diisi',
        'jumlah_tablet_ditambahkan.integer' => 'Jumlah tablet harus berupa angka',
        
        // Pesan validasi untuk form Makanan Tambahan Ibu Hamil
        'pemberian_mt.required' => 'Jenis pemberian MT wajib dipilih',
        'jumlah_mt.required' => 'Jumlah MT wajib diisi',
        'jumlah_mt.integer' => 'Jumlah MT harus berupa angka',
        
        // Pesan validasi untuk form Hipertensi
        'pantau_tekanan_darah.required' => 'Pilihan pantau tekanan darah wajib diisi',
        'pantau_protein_urine.required' => 'Pilihan pantau protein urine wajib diisi',
        'pantau_kondisi_janin.required' => 'Pilihan pantau kondisi janin wajib diisi',
        
        // Pesan validasi untuk form Eklampsia
        'pantau_tekanan_darah_eklampsia.required' => 'Pilihan pantau tekanan darah wajib diisi',
        'pantau_protein_urine_eklampsia.required' => 'Pilihan pantau protein urine wajib diisi',
        'pantau_kondisi_janin_eklampsia.required' => 'Pilihan pantau kondisi janin wajib diisi',
        'pemberian_antihipertensi.required' => 'Pilihan pemberian antihipertensi wajib diisi',
        'pemberian_mgso4.required' => 'Pilihan pemberian MgSO4 wajib diisi',
        'pemberian_diazepam.required' => 'Pilihan pemberian Diazepam wajib diisi',
        
        // Pesan validasi untuk form KEK
        'edukasi_gizi.required' => 'Pilihan edukasi gizi wajib diisi',
        
        // Pesan validasi untuk form Obesitas
        'edukasi_gizi_obesitas.required' => 'Pilihan edukasi gizi wajib diisi',
        
        // Pesan validasi untuk form Infeksi
        'pemberian_antipiretik.required' => 'Pilihan pemberian antipiretik wajib diisi',
        'pemberian_antibiotik.required' => 'Pilihan pemberian antibiotik wajib diisi',
        
        // Pesan validasi untuk form Penyakit Jantung
        'edukasi.required' => 'Pilihan edukasi wajib diisi',
        
        // Pesan validasi untuk form HIV
        'datang_dengan_hiv.required' => 'Status HIV saat kedatangan wajib diisi',
        'persalinan_pervaginam.required' => 'Status persalinan pervaginam wajib diisi',
        'persalinan_perapdoinam.required' => 'Status persalinan perapdoinam wajib diisi',
        'ditawarkan_tes.required' => 'Pilihan ditawarkan tes wajib diisi',
        'dilakukan_tes.required' => 'Pilihan dilakukan tes wajib diisi',
        'hasil_tes_hiv.required' => 'Hasil tes HIV wajib diisi',
        'mendapatkan_art.required' => 'Pilihan mendapatkan ART wajib diisi',
        'vct_pict.required' => 'Pilihan VCT (PICT) wajib diisi',
        'periksa_darah.required' => 'Pilihan periksa darah wajib diisi',
        'serologi.required' => 'Hasil serologi wajib diisi',
        
        // Pesan validasi untuk form TB
        'diperiksa_dahak.required' => 'Pilihan diperiksa dahak wajib diisi',
        'tbc.required' => 'Status TBC wajib diisi',
        
        // Pesan validasi untuk form Malaria
        'diberikan_kelambu.required' => 'Pilihan diberikan kelambu wajib diisi',
        'darah_malaria_rdt.required' => 'Pilihan pemeriksaan darah malaria RDT wajib diisi',
        'darah_malaria_mikroskopis.required' => 'Pilihan pemeriksaan darah malaria mikroskopis wajib diisi',
        'ibu_hamil_malaria_rdt.required' => 'Status ibu hamil malaria RDT wajib diisi',
        'ibu_hamil_malaria_mikroskopis.required' => 'Status ibu hamil malaria mikroskopis wajib diisi',
        'hasil_test_malaria.required' => 'Hasil test darah malaria wajib diisi',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        
        // Jika berat_badan atau tinggi_badan diupdate, hitung IMT
        if ($propertyName === 'berat_badan' || $propertyName === 'tinggi_badan') {
            $this->hitungIMT();
        }
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
            $validatedData = $this->validate();
            
            if (empty($this->noRawat) || empty($this->noRm)) {
                session()->flash('error', 'No Rawat dan No RM tidak boleh kosong');
                return;
            }
            
            DB::beginTransaction();
            
            $data = [
                'no_rawat' => $this->noRawat,
                'no_rkm_medis' => $this->noRm,
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
                'created_at' => now(),
                'updated_at' => now(),
            ];
            
            // Tambahkan data anemia jika jenis tatalaksana adalah Anemia
            if ($this->jenis_tatalaksana === 'Anemia') {
                $data['diberikan_tablet_fe'] = $this->diberikan_tablet_fe;
                $data['jumlah_tablet_dikonsumsi'] = $this->jumlah_tablet_dikonsumsi;
                $data['jumlah_tablet_ditambahkan'] = $this->jumlah_tablet_ditambahkan;
                $data['tatalaksana_lainnya'] = $this->tatalaksana_lainnya;
            }
            
            // Tambahkan data Makanan Tambahan Ibu Hamil jika jenis tatalaksana adalah MT
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
            
            if ($this->isEdit && $this->pemeriksaanId) {
                // Update data
                DB::table('pemeriksaan_anc')
                    ->where('id', $this->pemeriksaanId)
                    ->update($data);
                
                $message = 'Pemeriksaan ANC berhasil diperbarui';
            } else {
                // Insert data baru
                DB::table('pemeriksaan_anc')->insert($data);
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
    
    public function edit($id)
    {
        $this->isEdit = true;
        $this->pemeriksaanId = $id;
        
        $pemeriksaan = DB::table('pemeriksaan_anc')
            ->where('id', $id)
            ->first();
            
        if ($pemeriksaan) {
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
            
            // Isi nilai form anemia jika ada
            if (isset($pemeriksaan->diberikan_tablet_fe)) {
                $this->diberikan_tablet_fe = $pemeriksaan->diberikan_tablet_fe;
            }
            if (isset($pemeriksaan->jumlah_tablet_dikonsumsi)) {
                $this->jumlah_tablet_dikonsumsi = $pemeriksaan->jumlah_tablet_dikonsumsi;
            }
            if (isset($pemeriksaan->jumlah_tablet_ditambahkan)) {
                $this->jumlah_tablet_ditambahkan = $pemeriksaan->jumlah_tablet_ditambahkan;
            }
            if (isset($pemeriksaan->tatalaksana_lainnya)) {
                $this->tatalaksana_lainnya = $pemeriksaan->tatalaksana_lainnya;
            }
            
            // Isi nilai form Makanan Tambahan Ibu Hamil jika ada
            if (isset($pemeriksaan->pemberian_mt)) {
                $this->pemberian_mt = $pemeriksaan->pemberian_mt;
            }
            if (isset($pemeriksaan->jumlah_mt)) {
                $this->jumlah_mt = $pemeriksaan->jumlah_mt;
            }
            
            // Isi nilai form Hipertensi jika ada
            if (isset($pemeriksaan->pantau_tekanan_darah)) {
                $this->pantau_tekanan_darah = $pemeriksaan->pantau_tekanan_darah;
            }
            if (isset($pemeriksaan->pantau_protein_urine)) {
                $this->pantau_protein_urine = $pemeriksaan->pantau_protein_urine;
            }
            if (isset($pemeriksaan->pantau_kondisi_janin)) {
                $this->pantau_kondisi_janin = $pemeriksaan->pantau_kondisi_janin;
            }
            if (isset($pemeriksaan->hipertensi_lainnya)) {
                $this->hipertensi_lainnya = $pemeriksaan->hipertensi_lainnya;
            }
            
            // Isi nilai form Eklampsia jika ada
            if (isset($pemeriksaan->pantau_tekanan_darah_eklampsia)) {
                $this->pantau_tekanan_darah_eklampsia = $pemeriksaan->pantau_tekanan_darah_eklampsia;
            }
            if (isset($pemeriksaan->pantau_protein_urine_eklampsia)) {
                $this->pantau_protein_urine_eklampsia = $pemeriksaan->pantau_protein_urine_eklampsia;
            }
            if (isset($pemeriksaan->pantau_kondisi_janin_eklampsia)) {
                $this->pantau_kondisi_janin_eklampsia = $pemeriksaan->pantau_kondisi_janin_eklampsia;
            }
            if (isset($pemeriksaan->pemberian_antihipertensi)) {
                $this->pemberian_antihipertensi = $pemeriksaan->pemberian_antihipertensi;
            }
            if (isset($pemeriksaan->pemberian_mgso4)) {
                $this->pemberian_mgso4 = $pemeriksaan->pemberian_mgso4;
            }
            if (isset($pemeriksaan->pemberian_diazepam)) {
                $this->pemberian_diazepam = $pemeriksaan->pemberian_diazepam;
            }
            
            // Isi nilai form KEK jika ada
            if (isset($pemeriksaan->edukasi_gizi)) {
                $this->edukasi_gizi = $pemeriksaan->edukasi_gizi;
            }
            if (isset($pemeriksaan->kek_lainnya)) {
                $this->kek_lainnya = $pemeriksaan->kek_lainnya;
            }
            
            // Isi nilai form Obesitas jika ada
            if (isset($pemeriksaan->edukasi_gizi_obesitas)) {
                $this->edukasi_gizi_obesitas = $pemeriksaan->edukasi_gizi_obesitas;
            }
            if (isset($pemeriksaan->obesitas_lainnya)) {
                $this->obesitas_lainnya = $pemeriksaan->obesitas_lainnya;
            }
            
            // Isi nilai form Infeksi jika ada
            if (isset($pemeriksaan->pemberian_antipiretik)) {
                $this->pemberian_antipiretik = $pemeriksaan->pemberian_antipiretik;
            }
            if (isset($pemeriksaan->pemberian_antibiotik)) {
                $this->pemberian_antibiotik = $pemeriksaan->pemberian_antibiotik;
            }
            if (isset($pemeriksaan->infeksi_lainnya)) {
                $this->infeksi_lainnya = $pemeriksaan->infeksi_lainnya;
            }
            
            // Isi nilai form Penyakit Jantung jika ada
            if (isset($pemeriksaan->edukasi)) {
                $this->edukasi = $pemeriksaan->edukasi;
            }
            if (isset($pemeriksaan->jantung_lainnya)) {
                $this->jantung_lainnya = $pemeriksaan->jantung_lainnya;
            }
            
            // Isi nilai form HIV jika ada
            if (isset($pemeriksaan->datang_dengan_hiv)) {
                $this->datang_dengan_hiv = $pemeriksaan->datang_dengan_hiv;
            }
            if (isset($pemeriksaan->persalinan_pervaginam)) {
                $this->persalinan_pervaginam = $pemeriksaan->persalinan_pervaginam;
            }
            if (isset($pemeriksaan->persalinan_perapdoinam)) {
                $this->persalinan_perapdoinam = $pemeriksaan->persalinan_perapdoinam;
            }
            if (isset($pemeriksaan->ditawarkan_tes)) {
                $this->ditawarkan_tes = $pemeriksaan->ditawarkan_tes;
            }
            if (isset($pemeriksaan->dilakukan_tes)) {
                $this->dilakukan_tes = $pemeriksaan->dilakukan_tes;
            }
            if (isset($pemeriksaan->hasil_tes_hiv)) {
                $this->hasil_tes_hiv = $pemeriksaan->hasil_tes_hiv;
            }
            if (isset($pemeriksaan->mendapatkan_art)) {
                $this->mendapatkan_art = $pemeriksaan->mendapatkan_art;
            }
            if (isset($pemeriksaan->vct_pict)) {
                $this->vct_pict = $pemeriksaan->vct_pict;
            }
            if (isset($pemeriksaan->periksa_darah)) {
                $this->periksa_darah = $pemeriksaan->periksa_darah;
            }
            if (isset($pemeriksaan->serologi)) {
                $this->serologi = $pemeriksaan->serologi;
            }
            if (isset($pemeriksaan->arv_profilaksis)) {
                $this->arv_profilaksis = $pemeriksaan->arv_profilaksis;
            }
            if (isset($pemeriksaan->hiv_lainnya)) {
                $this->hiv_lainnya = $pemeriksaan->hiv_lainnya;
            }
            
            // Isi nilai form TB jika ada
            if (isset($pemeriksaan->diperiksa_dahak)) {
                $this->diperiksa_dahak = $pemeriksaan->diperiksa_dahak;
            }
            if (isset($pemeriksaan->tbc)) {
                $this->tbc = $pemeriksaan->tbc;
            }
            if (isset($pemeriksaan->obat_tb)) {
                $this->obat_tb = $pemeriksaan->obat_tb;
            }
            if (isset($pemeriksaan->sisa_obat)) {
                $this->sisa_obat = $pemeriksaan->sisa_obat;
            }
            if (isset($pemeriksaan->tb_lainnya)) {
                $this->tb_lainnya = $pemeriksaan->tb_lainnya;
            }
            
            // Isi nilai form Malaria jika ada
            if (isset($pemeriksaan->diberikan_kelambu)) {
                $this->diberikan_kelambu = $pemeriksaan->diberikan_kelambu;
            }
            if (isset($pemeriksaan->darah_malaria_rdt)) {
                $this->darah_malaria_rdt = $pemeriksaan->darah_malaria_rdt;
            }
            if (isset($pemeriksaan->darah_malaria_mikroskopis)) {
                $this->darah_malaria_mikroskopis = $pemeriksaan->darah_malaria_mikroskopis;
            }
            if (isset($pemeriksaan->ibu_hamil_malaria_rdt)) {
                $this->ibu_hamil_malaria_rdt = $pemeriksaan->ibu_hamil_malaria_rdt;
            }
            if (isset($pemeriksaan->ibu_hamil_malaria_mikroskopis)) {
                $this->ibu_hamil_malaria_mikroskopis = $pemeriksaan->ibu_hamil_malaria_mikroskopis;
            }
            if (isset($pemeriksaan->hasil_test_malaria)) {
                $this->hasil_test_malaria = $pemeriksaan->hasil_test_malaria;
            }
            if (isset($pemeriksaan->obat_malaria)) {
                $this->obat_malaria = $pemeriksaan->obat_malaria;
            }
            if (isset($pemeriksaan->malaria_lainnya)) {
                $this->malaria_lainnya = $pemeriksaan->malaria_lainnya;
            }
        } else {
            session()->flash('error', 'Data pemeriksaan ANC tidak ditemukan');
        }
    }
    
    public function delete($id)
    {
        try {
            DB::table('pemeriksaan_anc')
                ->where('id', $id)
                ->delete();
                
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
        $this->tanggal_anc = Carbon::now()->format('Y-m-d\TH:i:s');
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
                $riwayat = DB::table('pemeriksaan_anc')
                    ->where('no_rawat', $this->noRawat)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }
        } catch (\Exception $e) {
            \Log::error('Error loading data', [
                'error' => $e->getMessage(),
                'noRawat' => $this->noRawat
            ]);
        }
        
        return view('livewire.ralan.pemeriksaan-anc', [
            'riwayat' => $riwayat,
            'petugas' => $petugas
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
}
