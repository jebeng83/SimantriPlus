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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
        try {
            $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
            $kd_dokter = $this->dokter;
            $kd_poli = $this->kd_poli;
            
            if (!$kd_dokter || !$kd_poli) {
                \Log::error('FormPendaftaran: kd_dokter atau kd_poli tidak tersedia');
                throw new \Exception('Data dokter atau poli tidak lengkap');
            }
            
            // Buat kunci cache untuk locking spesifik per dokter dan poli
            $lockKey = "lock_no_reg_{$kd_dokter}_{$kd_poli}_{$tgl}";
            $isLocked = \Cache::add($lockKey, true, 30); // Lock selama 30 detik maksimal
            
            if (!$isLocked) {
                \Log::warning('FormPendaftaran: Menunggu lock untuk generate nomor registrasi...');
                // Tunggu sampai 3 detik untuk mendapatkan lock
                $startTime = microtime(true);
                while (!$isLocked && microtime(true) - $startTime < 3) {
                    usleep(100000); // Tunggu 100ms
                    $isLocked = \Cache::add($lockKey, true, 30);
                }
            }
            
            try {
                // Sesuai dengan contoh Java, gunakan filter dokter dan poli
                \Log::info("FormPendaftaran: Mencari nomor registrasi untuk dokter: $kd_dokter, poli: $kd_poli, tanggal: $tgl");
                
                // 1. Cek di reg_periksa
                $maxReg = 0;
                $regPeriksaData = DB::table('reg_periksa')
                    ->where('tgl_registrasi', $tgl)
                    ->where('kd_dokter', $kd_dokter)
                    ->where('kd_poli', $kd_poli)
                    ->get(['no_reg']);
                
                foreach ($regPeriksaData as $item) {
                    $regNum = intval(ltrim($item->no_reg, '0'));
                    if ($regNum > $maxReg) {
                        $maxReg = $regNum;
                    }
                }
                
                \Log::info("FormPendaftaran: Max reg dari reg_periksa: $maxReg");
                
                // 2. Cek di booking_registrasi
                if (Schema::hasTable('booking_registrasi')) {
                    $bookingData = DB::table('booking_registrasi')
                        ->where('tanggal_periksa', $tgl)
                        ->where('kd_dokter', $kd_dokter)
                        ->where('kd_poli', $kd_poli)
                        ->get(['no_reg']);
                    
                    foreach ($bookingData as $item) {
                        $regNum = intval(ltrim($item->no_reg, '0'));
                        if ($regNum > $maxReg) {
                            $maxReg = $regNum;
                        }
                    }
                    
                    \Log::info("FormPendaftaran: Max reg termasuk booking: $maxReg");
                }
                
                // 3. Cek di history_noreg jika ada
                if (Schema::hasTable('history_noreg')) {
                    $historyData = DB::table('history_noreg')
                        ->where('tgl_registrasi', $tgl)
                        ->where('kd_dokter', $kd_dokter)
                        ->where('kd_poli', $kd_poli)
                        ->get(['no_reg']);
                    
                    foreach ($historyData as $item) {
                        $regNum = intval(ltrim($item->no_reg, '0'));
                        if ($regNum > $maxReg) {
                            $maxReg = $regNum;
                        }
                    }
                    
                    \Log::info("FormPendaftaran: Max reg termasuk history: $maxReg");
                }
                
                // Generate nomor berikutnya
                $nextRegNum = $maxReg + 1;
                $nextReg = str_pad($nextRegNum, 3, '0', STR_PAD_LEFT);
                \Log::info("FormPendaftaran: Nomor registrasi final: $nextReg");
                
                // Simpan ke tabel history jika ada
                if (Schema::hasTable('history_noreg')) {
                    DB::table('history_noreg')->insert([
                        'kd_dokter' => $kd_dokter,
                        'tgl_registrasi' => $tgl,
                        'no_reg' => $nextReg,
                        'kd_poli' => $kd_poli,
                        'method' => 'form_pendaftaran',
                        'created_by' => auth()->check() ? auth()->user()->username : 'system',
                        'created_at' => now()
                    ]);
                }
                
                // Simpan ke cache global
                $globalCacheKey = "global_max_reg_{$kd_dokter}_{$kd_poli}_{$tgl}";
                \Cache::put($globalCacheKey, $nextRegNum, now()->addDay());
                
                return $nextReg;
            } finally {
                // Hapus lock dari cache setelah selesai
                if ($isLocked) {
                    \Cache::forget($lockKey);
                    \Log::info('FormPendaftaran: Lock untuk generate nomor registrasi dilepaskan');
                }
            }
        } catch (\Exception $e) {
            \Log::error('FormPendaftaran error generating no_reg: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Fallback: gunakan metode sederhana
            try {
                $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
                $queryMaxReg = DB::table('reg_periksa')
                    ->where('tgl_registrasi', $tgl)
                    ->where('kd_dokter', $this->dokter)
                    ->where('kd_poli', $this->kd_poli)
                    ->max('no_reg');
                
                $maxReg = $queryMaxReg ? intval(ltrim($queryMaxReg, '0')) : 0;
                $nextReg = str_pad($maxReg + 1, 3, '0', STR_PAD_LEFT);
                
                \Log::warning("FormPendaftaran: Menggunakan fallback untuk generate no_reg: $nextReg");
                return $nextReg;
            } catch (\Exception $fallbackError) {
                \Log::error('Fallback juga gagal: ' . $fallbackError->getMessage());
                return '001'; // Default ke 001 jika semua metode gagal
            }
        }
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
        try {
            if (empty($tgl_lahir) || empty($this->no_rkm_medis)) {
                return;
            }
            
            // Parse tanggal lahir dengan format yang benar
            $tgl_lahir_parsed = Carbon::parse($tgl_lahir);
            $umur_calculated = $tgl_lahir_parsed->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
            $this->umur = $umur_calculated;
            // Update dengan retry untuk mengatasi error 1615 (Prepared statement needs to be re-prepared)
            $maxAttempts = 3;
            $attempt = 0;
            $lastException = null;
            while ($attempt < $maxAttempts) {
                try {
                    $attempt++;
                    $affected = DB::table('pasien')
                        ->where('no_rkm_medis', '=', $this->no_rkm_medis)
                        ->update([
                            'umur' => $umur_calculated
                        ]);
                    if ($affected === 0) {
                        \Log::warning('No rows affected when updating umur for no_rkm_medis: ' . $this->no_rkm_medis);
                    }
                    // Berhasil, keluar dari loop retry
                    $lastException = null;
                    break;
                } catch (\Exception $inner) {
                    $message = $inner->getMessage();
                    $is1615 = stripos($message, '1615') !== false || stripos($message, 'Prepared statement needs to be re-prepared') !== false;
                    if ($is1615 && $attempt < $maxAttempts) {
                        \Log::warning('Retry update umur karena error 1615. Attempt: ' . $attempt . ' RM: ' . $this->no_rkm_medis);
                        // Reset koneksi dan coba lagi dengan backoff kecil
                        try { DB::purge(); DB::reconnect(); } catch (\Throwable $t) { /* ignore */ }
                        usleep(150000 * $attempt); // 150ms, 300ms, ...
                        $lastException = $inner;
                        continue;
                    }
                    // Bukan error 1615 atau sudah habis retry
                    $lastException = $inner;
                    break;
                }
            }
            if ($lastException) {
                throw $lastException;
            }
                
        } catch (\Exception $e) {
            \Log::error('Error updating umur pasien: ' . $e->getMessage(), [
                'no_rkm_medis' => $this->no_rkm_medis,
                'tgl_lahir' => $tgl_lahir,
                'umur' => $this->umur ?? null,
                'error_detail' => $e->getMessage()
            ]);
        }
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

            // Update umur pasien sebelum transaksi utama untuk menghindari konflik prepared statement
            $pasien = Pasien::where('no_rkm_medis', $this->no_rkm_medis)->first();
            if ($pasien && $pasien->tgl_lahir) {
                $this->rubahUmur($pasien->tgl_lahir);
            }

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
            
            // Cek apakah pasien menggunakan BPJS dan kirim data antrian
            $this->handleBPJSIntegration($no_rawat ?? $this->no_rawat);
            
            // Regenerate session token setelah simpan berhasil
            Session::regenerateToken();
            
            // Emit event untuk JavaScript dengan data registrasi
            $this->emit('registrationSuccess', [
                'no_rawat' => $no_rawat ?? $this->no_rawat,
                'no_reg' => $no_reg,
                'is_bpjs' => in_array($this->penjab, ['A03', 'A14', 'A15', 'BPJ']) || stripos($this->penjab, 'bpjs') !== false
            ]);
            
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

    /**
     * Handle BPJS integration for patient registration
     */
    private function handleBPJSIntegration($no_rawat)
    {
        try {
            // Cek apakah pasien menggunakan BPJS
            $isBPJS = in_array($this->penjab, ['A14', 'A15', 'BPJ']) || 
                     stripos($this->penjab, 'bpjs') !== false;
            
            if ($isBPJS) {
                // Ambil data registrasi yang baru disimpan
                $regData = DB::table('reg_periksa')
                    ->where('no_rawat', $no_rawat)
                    ->first();
                
                if ($regData) {
                    $this->kirimAntreanBPJS($regData);
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error dalam BPJS integration: ' . $e->getMessage());
        }
    }

    /**
     * Kirim data antrian ke BPJS
     */
    private function kirimAntreanBPJS($data)
    {
        try {
            \Log::info('Mulai proses kirim antrian BPJS untuk no_rawat: ' . $data->no_rawat, [
                'no_rkm_medis' => $data->no_rkm_medis,
                'kd_poli' => $data->kd_poli,
                'kd_dokter' => $data->kd_dokter
            ]);
            
            // Cek apakah data sudah pernah dikirim
            $cekLogAntrianBPJS = null;
            if (Schema::hasTable('antrean_bpjs_log')) {
                $cekLogAntrianBPJS = DB::table('antrean_bpjs_log')
                    ->where('no_rawat', $data->no_rawat)
                    ->where('status', 'Berhasil')
                    ->first();
            }
            
            if ($cekLogAntrianBPJS) {
                \Log::info('Data antrian BPJS sudah pernah dikirim dan berhasil.');
                return true;
            }
            
            // 1. Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $data->no_rkm_medis)
                ->first();
            
            if (!$pasien) {
                throw new \Exception('Data pasien tidak ditemukan');
            }
            
            // 2. Ambil data poli dan mapping ke kode BPJS
            $poli = DB::table('poliklinik')
                ->where('kd_poli', $data->kd_poli)
                ->first();
            
            $poliMapping = DB::table('maping_poliklinik_pcare')
                ->where('kd_poli_rs', $data->kd_poli)
                ->first();
            
            if (!$poliMapping) {
                $poliMapping = (object)[
                    'kd_poli_pcare' => $data->kd_poli,
                    'nm_poli_pcare' => $poli->nm_poli ?? ''
                ];
            }
            
            // 3. Ambil data dokter dan mapping ke kode BPJS
            $dokter = DB::table('dokter')
                ->where('kd_dokter', $data->kd_dokter)
                ->first();
            
            $dokterMapping = DB::table('maping_dokter_pcare')
                ->where('kd_dokter', $data->kd_dokter)
                ->first();
            
            if (!$dokterMapping) {
                $dokterMapping = (object)[
                    'kd_dokter_pcare' => 0,
                    'nm_dokter_pcare' => $dokter->nm_dokter ?? ''
                ];
            }
            
            // 4. Ambil data jadwal
            $today = date('l', strtotime($data->tgl_registrasi));
            $hariIndonesia = $this->translateDay($today);
            
            $jadwal = DB::table('jadwal')
                ->where('kd_dokter', $data->kd_dokter)
                ->where('kd_poli', $data->kd_poli)
                ->where('hari_kerja', $hariIndonesia)
                ->first();
            
            $jamPraktek = "-";
            if ($jadwal) {
                $jamPraktek = substr($jadwal->jam_mulai, 0, 5) . "-" . substr($jadwal->jam_selesai, 0, 5);
            }
            
            // 5. Generate nomor antrian
            $nomorAntrian = $this->generateNomorAntrian($data->kd_poli, $data->tgl_registrasi);
            
            // 6. Siapkan data untuk BPJS
            $dataAntrean = [
                'nomorkartu' => $pasien->no_peserta ?? '',
                'nik' => $pasien->no_ktp ?? '',
                'nohp' => $pasien->no_tlp ?? '',
                'kodepoli' => $poliMapping->kd_poli_pcare,
                'namapoli' => $poliMapping->nm_poli_pcare,
                'norm' => $data->no_rkm_medis,
                'tanggalperiksa' => date('Y-m-d', strtotime($data->tgl_registrasi)),
                'kodedokter' => (int)$dokterMapping->kd_dokter_pcare,
                'namadokter' => $dokterMapping->nm_dokter_pcare,
                'jampraktek' => $jamPraktek,
                'nomorantrean' => $nomorAntrian['nomor_antrian'],
                'angkaantrean' => $nomorAntrian['angka_antrian'],
                'keterangan' => ''
            ];
            
            // 7. Kirim ke BPJS menggunakan WsBPJSController
             $wsBPJSController = new \App\Http\Controllers\API\WsBPJSController();
             $request = new \Illuminate\Http\Request($dataAntrean);
             $response = $wsBPJSController->tambahAntrean($request);
             
             // Convert JsonResponse to array for processing
             $responseData = json_decode($response->getContent(), true);
            
            \Log::info('Response dari BPJS: ', ['response' => $responseData]);
             
             // Check if response is successful (BPJS returns code 200 for success)
             // Handle both 'metadata' and 'metaData' cases
             $metadata = $responseData['metadata'] ?? $responseData['metaData'] ?? null;
             $isSuccess = isset($metadata['code']) && $metadata['code'] == 200;
             
             // 8. Simpan log ke database
             if (Schema::hasTable('antrean_bpjs_log')) {
                 DB::table('antrean_bpjs_log')->insert([
                     'no_rawat' => $data->no_rawat,
                     'no_rkm_medis' => $data->no_rkm_medis,
                     'status' => $isSuccess ? 'Berhasil' : 'Gagal',
                     'response' => json_encode($responseData),
                     'created_at' => now()
                 ]);
             }
             
             return $isSuccess;
            
        } catch (\Exception $e) {
            \Log::error('Error kirim antrian BPJS: ' . $e->getMessage());
            
            // Simpan log error
            if (Schema::hasTable('antrean_bpjs_log')) {
                DB::table('antrean_bpjs_log')->insert([
                    'no_rawat' => $data->no_rawat ?? '',
                    'no_rkm_medis' => $data->no_rkm_medis ?? '',
                    'status' => 'Gagal',
                    'response_bpjs' => json_encode(['error' => $e->getMessage()]),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            return false;
        }
    }
    
    /**
     * Translate English day to Indonesian
     */
    private function translateDay($englishDay)
    {
        $days = [
            'Monday' => 'SENIN',
            'Tuesday' => 'SELASA', 
            'Wednesday' => 'RABU',
            'Thursday' => 'KAMIS',
            'Friday' => 'JUMAT',
            'Saturday' => 'SABTU',
            'Sunday' => 'MINGGU'
        ];
        
        return $days[$englishDay] ?? 'SENIN';
    }
    
    /**
     * Generate nomor antrian
     */
    private function generateNomorAntrian($kd_poli, $tgl_registrasi)
    {
        $tanggal = date('Y-m-d', strtotime($tgl_registrasi));
        
        // Hitung jumlah antrian hari ini untuk poli ini
        $jumlahAntrian = DB::table('reg_periksa')
            ->where('kd_poli', $kd_poli)
            ->whereDate('tgl_registrasi', $tanggal)
            ->count();
        
        $angkaAntrian = $jumlahAntrian;
        
        // Ambil kode poli untuk prefix
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        $prefix = $poli ? substr($poli->nm_poli, 0, 1) : 'A';
        
        $nomorAntrian = $prefix . '-' . $angkaAntrian;
        
        return [
            'nomor_antrian' => $nomorAntrian,
            'angka_antrian' => $angkaAntrian
        ];
    }
}
