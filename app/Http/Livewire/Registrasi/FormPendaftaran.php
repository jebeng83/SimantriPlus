<?php

namespace App\Http\Livewire\Registrasi;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Penjab;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Poliklinik;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Illuminate\Support\Facades\Session;

class FormPendaftaran extends Component
{
    use LivewireAlert;
    public $tgl_registrasi;
    public $no_rawat;
    public $no_rkm_medis;
    public $no_rkm_medis_old;
    public $dokter;
    public $nm_dokter;
    public $nm_pasien;
    public $penjab;
    public $pj;
    public $kd_poli;
    public $hubungan_pj;
    public $alamat_pj;
    public $status;
    public $listPenjab = [];
    public $poliklinik = [];
    public $umur;

    protected $listeners = [
        'resetError' => 'resetError', 
        'bukaModalPendaftaran' => 'bukaModalPendaftaran',
        'initFormPendaftaran' => 'initFormPendaftaran',
        'refreshComponent' => '$refresh'
    ];

    public function mount()
    {
        // Pastikan session aktif dan valid
        if (!Session::isStarted()) {
            Session::start();
        }
        
        $this->tgl_registrasi = date('Y-m-d H:i:s');
        $this->listPenjab = $this->getPenjab();
        $this->poliklinik = $this->getPoliklinik();
    }

    public function hydrate()
    {
        // Pastikan session aktif setiap kali komponen di-hydrate
        if (!Session::isStarted()) {
            Session::start();
        }
        
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function dehydrate()
    {
        // Pastikan session aktif setiap kali komponen di-dehydrate
        if (!Session::isStarted()) {
            Session::start();
        }
    }

    public function updatedNoRkmMedis()
    {
        try {
            $pasien = DB::table('pasien')->where('no_rkm_medis', $this->no_rkm_medis)->first();
            if (!$pasien) {
                $this->addError('no_rkm_medis', 'Pasien tidak ditemukan');
                return;
            }
            
            $cek = DB::table('reg_periksa')->where('no_rkm_medis', $this->no_rkm_medis)->where('stts', 'Sudah')->first();
            $this->pj = $pasien->namakeluarga ?? '';
            $this->alamat_pj = $pasien->alamatpj ?? '';
            $this->hubungan_pj = $pasien->keluarga ?? '';
            $this->status = $cek ? 'Lama' : 'Baru';
            $this->penjab = $pasien->kd_pj ?? '';
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error saat mengambil data pasien: ' . $e->getMessage());
            $this->addError('no_rkm_medis', 'Terjadi kesalahan saat mengambil data pasien');
        }
    }

    public function render()
    {
        // Pastikan session aktif setiap kali render
        if (!Session::isStarted()) {
            Session::start();
        }
        
        return view('livewire.registrasi.form-pendaftaran');
    }

    public function getPenjab()
    {
        return Penjab::where('status', '1')->get();
    }

    public function getPoliklinik()
    {
        return Poliklinik::where('status', '1')->get();
    }

    public function generateNoReg()
    {
        $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
        $no_reg = DB::table('reg_periksa')
            ->where('tgl_registrasi', $tgl)
            ->where('kd_dokter', $this->dokter)
            ->where('kd_poli', $this->kd_poli)
            ->max('no_reg');
        return str_pad($no_reg + 1, 3, '0', STR_PAD_LEFT);
    }

    public function generateNoRawat()
    {
        $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
        $max = DB::table('reg_periksa')
            ->where('tgl_registrasi', $tgl)
            ->selectRaw("ifnull(MAX(CONVERT(RIGHT(reg_periksa.no_rawat,6),signed)),0) as no")
            ->first();

        return date('Y/m/d') . '/' . str_pad($max->no + 1, 6, '0', STR_PAD_LEFT);
    }

    public function getBiayaReg($kd_poli)
    {
        return Poliklinik::where('kd_poli', $kd_poli)->first()->registrasi;
    }

    public function rubahUmur($tgl_lahir)
    {
        $tgl_lahir = Carbon::parse($tgl_lahir);
        $this->umur = $tgl_lahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');

        Pasien::where('no_rkm_medis', $this->no_rkm_medis)->update([
            'umur' => $this->umur
        ]);
    }

    public function bukaModalPendaftaran($no_rawat)
    {
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        $this->no_rawat = $no_rawat;
        $data = DB::table('reg_periksa')->where('no_rawat', $this->no_rawat)->first();
        if ($data) {
            $this->nm_dokter = Dokter::where('kd_dokter', $data->kd_dokter)->first()->nm_dokter;
            $this->nm_pasien = Pasien::where('no_rkm_medis', $data->no_rkm_medis)->first()->nm_pasien;
            $this->tgl_registrasi = $data->tgl_registrasi;
            $this->no_rkm_medis = $data->no_rkm_medis;
            $this->dokter = $data->kd_dokter;
            $this->penjab = $data->kd_pj;
            $this->pj = $data->p_jawab;
            $this->kd_poli = $data->kd_poli;
            $this->hubungan_pj = $data->hubunganpj;
            $this->alamat_pj = $data->almt_pj;
            $this->status = $data->status_poli;
            $this->emit('openModalPendaftaran');
        } else {
            $this->alert('error', 'No. Rawat tidak ditemukan');
            $this->reset();
        }
    }

    public function simpan()
    {
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        $this->validate([
            'no_rkm_medis' => 'required',
            'dokter' => 'required',
            'kd_poli' => 'required',
            'penjab' => 'required',
            'pj' => 'required',
            'hubungan_pj' => 'required',
            'alamat_pj' => 'required',
            'status' => 'required',
        ], [
            'no_rkm_medis.required' => 'No. Rekam Medis tidak boleh kosong',
            'dokter.required' => 'Dokter tidak boleh kosong',
            'kd_poli.required' => 'Poliklinik tidak boleh kosong',
            'penjab.required' => 'Penjab tidak boleh kosong',
            'pj.required' => 'Penanggung Jawab tidak boleh kosong',
            'hubungan_pj.required' => 'Hubungan PJ tidak boleh kosong',
            'alamat_pj.required' => 'Alamat PJ tidak boleh kosong',
            'status.required' => 'Status tidak boleh kosong',
        ]);
        try {
            $no_reg = $this->generateNoReg();
            $no_rawat = $this->generateNoRawat();

            $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
            $jam = Carbon::parse($this->tgl_registrasi)->format('H:i:s');

            DB::beginTransaction();

            $this->rubahUmur(Pasien::where('no_rkm_medis', $this->no_rkm_medis)->first()->tgl_lahir);

            if (!empty($this->no_rawat)) {
                DB::table('reg_periksa')->where('no_rawat', $this->no_rawat)->update([
                    'kd_dokter' => $this->dokter,
                    'kd_poli' => $this->kd_poli,
                    'kd_pj' => $this->penjab,
                    'no_rkm_medis' => $this->no_rkm_medis,
                    'status_lanjut' => 'Ralan',
                    'status_poli' => $this->status,
                    'almt_pj' => $this->alamat_pj,
                    'p_jawab' => $this->pj,
                    'hubunganpj' => $this->hubungan_pj,
                ]);
            } else {
                DB::table('reg_periksa')->insert([
                    'no_rawat' => $no_rawat,
                    'no_reg' => $no_reg,
                    'tgl_registrasi' => $tgl,
                    'jam_reg' => $jam,
                    'kd_dokter' => $this->dokter,
                    'kd_poli' => $this->kd_poli,
                    'kd_pj' => $this->penjab,
                    'no_rkm_medis' => $this->no_rkm_medis,
                    'status_lanjut' => 'Ralan',
                    'stts' => 'Belum',
                    'status_poli' => $this->status,
                    'almt_pj' => $this->alamat_pj,
                    'p_jawab' => $this->pj,
                    'hubunganpj' => $this->hubungan_pj,
                    'umurdaftar' => $this->umur,
                    'sttsumur' => 'Th',
                    'biaya_reg' => $this->getBiayaReg($this->kd_poli),
                    'status_bayar' => 'Belum Bayar',
                    'keputusan' => '-'
                ]);
            }

            DB::commit();
            
            // Regenerate session token setelah simpan berhasil
            Session::regenerateToken();
            
            $this->alert('success', 'Registrasi berhasil ditambahkan');
            $this->resetExcept(['listPenjab', 'poliklinik']);
            $this->emit('closeModalPendaftaran');
            $this->emit('refreshDatatable');
            $this->tgl_registrasi = date('Y-m-d H:i:s');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Registrasi gagal ditambahkan : ' . $e->getMessage());
        }
    }
    
    // Fungsi untuk mengatasi masalah session
    public function resetError()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }
    
    // Metode khusus untuk menangani session expired
    public function handleSessionExpired()
    {
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        // Regenerate CSRF token dan session ID
        Session::regenerateToken();
        Session::regenerate(true);
        
        // Kirim pesan ke frontend
        $this->emit('sessionRefreshed');
        
        return true;
    }
    
    // Metode untuk inisialisasi form pendaftaran
    public function initFormPendaftaran()
    {
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        // Reset form
        $this->reset(['no_rawat', 'no_rkm_medis', 'dokter', 'nm_dokter', 'nm_pasien', 'pj', 'kd_poli', 'hubungan_pj', 'alamat_pj', 'status']);
        
        // Set tanggal registrasi ke waktu sekarang
        $this->tgl_registrasi = date('Y-m-d H:i:s');
        
        // Emit event bahwa form telah diinisialisasi
        $this->emit('formInitialized');
    }

    // Metode khusus untuk menangani pemilihan pasien
    public function setPasien($no_rkm_medis, $token = null)
    {
        // Pastikan session aktif
        if (!Session::isStarted()) {
            Session::start();
        }
        
        // Jika token diberikan, gunakan token tersebut
        if ($token) {
            // Set CSRF token secara manual
            Session::put('_token', $token);
        } else {
            // Regenerate CSRF token
            Session::regenerateToken();
        }
        
        try {
            $this->no_rkm_medis = $no_rkm_medis;
            $this->updatedNoRkmMedis();
            
            return ['success' => true];
        } catch (\Exception $e) {
            // Log error
            \Log::error('Error saat set pasien: ' . $e->getMessage());
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
