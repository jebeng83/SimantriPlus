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
            // $petugas = DB::table('petugas')
            //     ->where('kd_jbtn', 'j008')
            //     ->where('status', '1')
            //     ->orderBy('nama', 'asc')
            //     ->get();
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
        return [
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
        ];
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
            'keadaan_pulang'
        ]);
        
        $this->isEdit = false;
        $this->pemeriksaanId = null;
        
        // Reset kembali nilai default
        $this->tanggal_anc = Carbon::now()->format('Y-m-d\TH:i:s');
        
        // Ambil kembali nama petugas dari database
        try {
            $petugas = DB::table('petugas')
                ->where('kd_jbtn', 'j008')
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
                ->where('kd_jbtn', 'j008')
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
} 