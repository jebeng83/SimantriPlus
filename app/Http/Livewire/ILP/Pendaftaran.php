<?php

namespace App\Http\Livewire\ILP;

use App\Models\Dokter;
use App\Models\Pasien;
use App\Models\Penjab;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Poliklinik;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class Pendaftaran extends Component
{
    use LivewireAlert;
    
    public $tgl_registrasi;
    public $no_rawat;
    public $no_rkm_medis;
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
    public $umur_tahun;
    public $status_pasien;
    public $data_posyandu;
    public $showModal = false;
    public $isOpen = false;
    public $search_term = '';
    public $searchTerm = '';
    public $filterPoliPustu = true;
    public $selectedPosyandu = '';
    public $listPosyandu = [];
    public $showFormPendaftaran = true;
    public $searchNamaPasien = '';
    public $filterStatus = '';

    protected $listeners = [
        'resetError' => 'resetError', 
        'bukaModalPendaftaran' => 'bukaModalPendaftaran', 
        'refreshDatatable' => '$refresh',
        'pasienSelected' => 'pasienSelected',
        'searchPasien' => 'searchPasienByTerm'
    ];

    protected $rules = [
        'no_rkm_medis' => 'required',
        'dokter' => 'required',
        'kd_poli' => 'required',
        'penjab' => 'required',
        'pj' => 'required',
        'hubungan_pj' => 'required',
        'alamat_pj' => 'required',
        'data_posyandu' => 'required',
    ];

    protected $messages = [
        'no_rkm_medis.required' => 'No. Rekam Medis tidak boleh kosong',
        'dokter.required' => 'Dokter tidak boleh kosong',
        'kd_poli.required' => 'Poliklinik tidak boleh kosong',
        'penjab.required' => 'Penjab tidak boleh kosong',
        'pj.required' => 'Penanggung Jawab tidak boleh kosong',
        'hubungan_pj.required' => 'Hubungan PJ tidak boleh kosong',
        'alamat_pj.required' => 'Alamat PJ tidak boleh kosong',
        'data_posyandu.required' => 'Data Posyandu tidak boleh kosong',
    ];

    public function mount()
    {
        $this->tgl_registrasi = date('Y-m-d H:i:s');
        $this->listPenjab = $this->getPenjab();
        $this->poliklinik = $this->getPoliklinik();
        $this->listPosyandu = $this->getPosyandu();
        
        // Default filterPoliPosyandu diatur aktif
        $this->filterPoliPustu = true;
        
        // Debug untuk memverifikasi format data
        // Uncomment jika diperlukan untuk debugging
        // \Log::info('Format Data Posyandu: ', ['type' => gettype($this->listPosyandu), 'sample' => isset($this->listPosyandu[0]) ? $this->listPosyandu[0] : null]);
        
        $this->isOpen = true; // Tampilkan form secara default
        $this->showFormPendaftaran = true; // Tampilkan form pendaftaran secara default
    }

    public function hydrate()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function resetError()
    {
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function updatedNoRkmMedis()
    {
        $this->cariPasien();
    }

    public function pasienSelected($noRkmMedis)
    {
        $this->no_rkm_medis = $noRkmMedis;
        $this->cariPasien();
    }

    public function cariPasien()
    {
        if (!empty($this->search_term) && empty($this->no_rkm_medis)) {
            $this->searchPasienByTerm();
            return;
        }
        
        $pasien = DB::table('pasien')->where('no_rkm_medis', $this->no_rkm_medis)->first();
        if ($pasien) {
            $this->nm_pasien = $pasien->nm_pasien;
            $this->pj = $pasien->namakeluarga ?? '';
            $this->alamat_pj = $pasien->alamatpj ?? '';
            $this->hubungan_pj = $pasien->keluarga ?? '';
            $this->data_posyandu = $pasien->data_posyandu ?? '';
            
            // Hitung umur pasien
            $tglLahir = Carbon::parse($pasien->tgl_lahir);
            $this->umur_tahun = $tglLahir->diffInYears(Carbon::now());
            $this->umur = $tglLahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
            
            // Tentukan status pasien berdasarkan umur
            $this->status_pasien = $this->hitungStatusPasien($this->umur_tahun);
            
            $cek = DB::table('reg_periksa')->where('no_rkm_medis', $this->no_rkm_medis)->where('stts', 'Sudah')->first();
            $this->status = $cek ? 'Lama' : 'Baru';
            $this->penjab = $pasien->kd_pj ?? '';
            
            // Update kolom nip dengan status pasien
            $this->updateStatusPasien($pasien->no_rkm_medis, $this->status_pasien);
        } else {
            $this->alert('error', 'No. Rekam Medis tidak ditemukan');
            $this->nm_pasien = '';
            $this->pj = '';
            $this->alamat_pj = '';
            $this->hubungan_pj = '';
            $this->status = '';
            $this->penjab = '';
            $this->data_posyandu = '';
            $this->umur_tahun = null;
            $this->status_pasien = '';
        }
    }

    public function render()
    {
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_rawat',
                'reg_periksa.no_rkm_medis',
                'pasien.nm_pasien',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'reg_periksa.stts',
                'penjab.png_jawab',
                'pasien.data_posyandu'
            )
            ->whereDate('reg_periksa.tgl_registrasi', Carbon::today())
            ->orderBy('reg_periksa.no_rawat', 'desc');

        if ($this->filterPoliPustu) {
            $query->where('poliklinik.nm_poli', 'like', '%PUSTU%');
        }

        if (!empty($this->selectedPosyandu)) {
            $query->where('pasien.data_posyandu', $this->selectedPosyandu);
        }
        
        // Filter berdasarkan status pasien
        if (!empty($this->filterStatus)) {
            $query->where('reg_periksa.stts', $this->filterStatus);
        }
        
        // Filter berdasarkan nama pasien
        if (!empty($this->searchNamaPasien)) {
            $query->where('pasien.nm_pasien', 'like', '%' . $this->searchNamaPasien . '%');
        }
        
        // Filter berdasarkan searchTerm (nama, NIK, atau no rekam medis)
        if (!empty($this->searchTerm)) {
            $query->where(function($q) {
                $q->where('pasien.nm_pasien', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('pasien.no_ktp', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('reg_periksa.no_rkm_medis', 'like', '%' . $this->searchTerm . '%');
            });
        }

        $pendaftaran = $query->get();
        
        $dokters = Dokter::join('pegawai', 'dokter.kd_dokter', '=', 'pegawai.nik')
                    ->select('dokter.kd_dokter', 'pegawai.nama')
                    ->get();
                    
        return view('livewire.ilp.pendaftaran', [
            'pendaftaran' => $pendaftaran,
            'dokters' => $dokters
        ]);
    }

    public function getPenjab()
    {
        return Penjab::where('status', '1')->get();
    }

    public function getPoliklinik()
    {
        return Poliklinik::where('status', '1')->get();
    }

    public function getPosyandu()
    {
        // Mengkonversi hasil query ke array JSON kemudian decode kembali
        // untuk memastikan format array yang konsisten
        $posyandu = DB::table('data_posyandu')->get();
        return json_decode(json_encode($posyandu), true);
    }

    public function toggleFilterPoliPustu()
    {
        $this->filterPoliPustu = !$this->filterPoliPustu;
    }

    public function resetFilters()
    {
        $this->filterPoliPustu = true;
        $this->selectedPosyandu = '';
        $this->searchTerm = '';
        $this->filterStatus = '';
        $this->emit('refreshDatatable');
    }

    public function resetSearchNamaPasien()
    {
        $this->searchNamaPasien = '';
    }

    // Fungsi untuk menyembunyikan sebagian nomor KTP
    private function maskKtp($ktp)
    {
        if (empty($ktp)) return '-';
        $length = strlen($ktp);
        if ($length <= 5) return $ktp;
        
        $firstFour = substr($ktp, 0, 4);
        $lastOne = substr($ktp, -1);
        $masked = str_repeat('x', $length - 5);
        
        return $firstFour . $masked . $lastOne;
    }
    
    public function searchPasienByTerm($term = null)
    {
        if ($term) {
            $this->search_term = $term;
        }
        
        if (empty($this->search_term)) {
            $this->alert('error', 'Masukkan Nomor RM / KTP / Nama Pasien dahulu');
            return;
        }
        
        $search = $this->search_term;
        
        // Log untuk debugging
        Log::info('Melakukan pencarian pasien dengan term: ' . $search);
        
        // Coba pencarian exact match terlebih dahulu untuk no_rkm_medis atau no_ktp
        $exactResults = DB::table('pasien')
            ->where('no_rkm_medis', $search)
            ->orWhere('no_ktp', $search)
            ->select('no_rkm_medis', 'nm_pasien', 'alamat', 'tgl_lahir', 'namakeluarga', 'keluarga', 'alamatpj', 'kd_pj', 'no_ktp', 'kelurahanpj')
            ->limit(10)
            ->get();
        
        // Jika ditemukan dengan exact match, langsung kembalikan hasilnya
        if ($exactResults->count() > 0) {
            Log::info('Ditemukan dengan exact match: ' . $exactResults->count() . ' pasien');
            $results = $exactResults;
        } else {
            // Jika tidak ditemukan exact match, coba dengan LIKE
            $likeSearch = '%' . $search . '%';
            
            // Buat query dengan grouping yang benar menggunakan Database Raw Expression
            $results = DB::table('pasien')
                ->whereRaw("(no_rkm_medis LIKE ? OR no_ktp LIKE ? OR nm_pasien LIKE ?)", [$likeSearch, $likeSearch, $likeSearch])
                ->select('no_rkm_medis', 'nm_pasien', 'alamat', 'tgl_lahir', 'namakeluarga', 'keluarga', 'alamatpj', 'kd_pj', 'no_ktp', 'kelurahanpj')
                ->limit(10)
                ->get();
                
            Log::info('Pencarian dengan LIKE: ' . $results->count() . ' pasien ditemukan');
        }
        
        // Log hasil pencarian
        Log::info('Total hasil pencarian: ' . $results->count() . ' pasien ditemukan');
        if ($results->count() > 0) {
            Log::info('Contoh pasien pertama: ', ['pasien' => $results->first()]);
        }
        
        // Tambahkan masked_ktp ke hasil pencarian
        foreach ($results as $result) {
            $result->masked_ktp = $this->maskKtp($result->no_ktp);
        }
            
        if ($results->count() === 0) {
            $this->alert('error', 'Pasien tidak ditemukan');
        } elseif ($results->count() === 1) {
            $pasien = $results->first();
            $this->no_rkm_medis = $pasien->no_rkm_medis;
            $this->cariPasien();
        } else {
            $this->emit('searchResults', $results);
        }
    }

    public function generateNoReg()
    {
        try {
            // Gunakan FormPendaftaran dari namespace Registrasi untuk konsistensi
            $formPendaftaran = new \App\Http\Livewire\Registrasi\FormPendaftaran();
            $formPendaftaran->dokter = $this->dokter;
            $formPendaftaran->kd_poli = $this->kd_poli;
            $formPendaftaran->tgl_registrasi = $this->tgl_registrasi;
            
            // Panggil method generateNoReg dari FormPendaftaran
            $no_reg = $formPendaftaran->generateNoReg();
            
            Log::info("ILP Pendaftaran: Nomor registrasi didapatkan dari FormPendaftaran: $no_reg");
            
            return $no_reg;
        } catch (\Exception $e) {
            Log::error('Error generateNoReg ILP via FormPendaftaran: ' . $e->getMessage());
            
            // Fallback jika terjadi error
            $tgl = Carbon::parse($this->tgl_registrasi)->format('Y-m-d');
            $max = DB::table('reg_periksa')
                ->where('tgl_registrasi', $tgl)
                ->where('kd_dokter', $this->dokter)
                ->where('kd_poli', $this->kd_poli)
                ->max('no_reg');
                
            $no_reg = str_pad(intval($max ?? 0) + 1, 3, '0', STR_PAD_LEFT);
            Log::info("ILP Pendaftaran: Fallback nomor registrasi: $no_reg");
            
            return $no_reg;
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
            // Update dengan retry untuk mitigasi error 1615
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
                        Log::warning('No rows affected when updating umur for no_rkm_medis: ' . $this->no_rkm_medis);
                    }
                    $lastException = null;
                    break;
                } catch (\Exception $inner) {
                    $message = $inner->getMessage();
                    $is1615 = stripos($message, '1615') !== false || stripos($message, 'Prepared statement needs to be re-prepared') !== false;
                    if ($is1615 && $attempt < $maxAttempts) {
                        Log::warning('Retry update umur karena error 1615. Attempt: ' . $attempt . ' RM: ' . $this->no_rkm_medis);
                        try { DB::purge(); DB::reconnect(); } catch (\Throwable $t) { /* ignore */ }
                        usleep(150000 * $attempt);
                        $lastException = $inner;
                        continue;
                    }
                    $lastException = $inner;
                    break;
                }
            }
            if ($lastException) {
                throw $lastException;
            }
                
        } catch (\Exception $e) {
            Log::error('Error updating umur pasien: ' . $e->getMessage(), [
                'no_rkm_medis' => $this->no_rkm_medis,
                'tgl_lahir' => $tgl_lahir,
                'umur' => $this->umur ?? null,
                'error_detail' => $e->getMessage()
            ]);
        }
    }

    public function openModal()
    {
        $this->showModal = true;
        $this->resetError();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetExcept(['listPenjab', 'poliklinik', 'isOpen', 'listPosyandu', 'showFormPendaftaran']);
        $this->tgl_registrasi = date('Y-m-d H:i:s');
        $this->search_term = '';
        $this->status_pasien = '';
        $this->umur_tahun = null;
        $this->data_posyandu = '';
    }

    public function bukaModalPendaftaran($no_rawat)
    {
        $this->no_rawat = $no_rawat;
        $data = DB::table('reg_periksa')->where('no_rawat', $this->no_rawat)->first();
        if ($data) {
            $dokterData = Dokter::where('kd_dokter', $data->kd_dokter)->first();
            $this->nm_dokter = $dokterData ? $dokterData->nm_dokter : '';
            
            $pasienData = Pasien::where('no_rkm_medis', $data->no_rkm_medis)->first();
            $this->nm_pasien = $pasienData ? $pasienData->nm_pasien : '';
            
            // Hitung umur pasien
            if ($pasienData) {
                $tglLahir = Carbon::parse($pasienData->tgl_lahir);
                $this->umur_tahun = $tglLahir->diffInYears(Carbon::now());
                $this->umur = $tglLahir->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
                
                // Tentukan status pasien berdasarkan umur
                $this->status_pasien = $this->hitungStatusPasien($this->umur_tahun);
                
                // Ambil data posyandu
                $this->data_posyandu = $pasienData->data_posyandu ?? '';
            }
            
            $this->tgl_registrasi = $data->tgl_registrasi;
            $this->no_rkm_medis = $data->no_rkm_medis;
            $this->dokter = $data->kd_dokter;
            $this->penjab = $data->kd_pj;
            $this->pj = $data->p_jawab;
            $this->kd_poli = $data->kd_poli;
            $this->hubungan_pj = $data->hubunganpj;
            $this->alamat_pj = $data->almt_pj;
            $this->status = $data->status_poli;
            $this->openModal();
            $this->emit('openModalPendaftaran');
        } else {
            $this->alert('error', 'No. Rawat tidak ditemukan');
            $this->reset();
        }
    }

    public function simpan()
    {
        $this->validate();
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
                
                // Update data posyandu dan status pasien
                Pasien::where('no_rkm_medis', $this->no_rkm_medis)->update([
                    'data_posyandu' => $this->data_posyandu,
                    'nip' => $this->status_pasien
                ]);
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
            $this->alert('success', 'Registrasi berhasil ditambahkan');
            $this->closeModal();
            $this->emit('closeModalPendaftaran');
            $this->emit('refreshDatatable');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->alert('error', 'Registrasi gagal ditambahkan : ' . $e->getMessage());
        }
    }

    // Method untuk route API Search Pasien
    public static function searchPasien($query)
    {
        try {
            $search = trim($query); // Bersihkan whitespace
            
            // Log untuk debugging
            Log::info('API: Memulai pencarian pasien', [
                'raw_query' => $query,
                'cleaned_query' => $search
            ]);
            
            // Kolom yang akan diambil
            $columns = [
                'no_rkm_medis', 'nm_pasien', 'alamat', 'tgl_lahir', 'namakeluarga', 
                'keluarga', 'alamatpj', 'kd_pj', 'no_ktp', 'kelurahanpj', 'no_tlp',
                'jk', 'stts_nikah', 'pekerjaan', 'agama', 'umur'
            ];

            // Bersihkan format KTP (hilangkan karakter non-numerik)
            $cleanKtp = preg_replace('/[^0-9]/', '', $search);
            
            // Log KTP yang sudah dibersihkan
            Log::info('API: KTP yang sudah dibersihkan', [
                'original' => $search,
                'cleaned' => $cleanKtp
            ]);
            
            // Query builder
            $query = DB::table('pasien')->select($columns);
            
            // Jika panjang input 16 digit, mungkin ini adalah nomor KTP
            if (strlen($cleanKtp) == 16) {
                $query->where(function($q) use ($cleanKtp) {
                    $q->where('no_ktp', $cleanKtp)
                      ->orWhere('no_ktp', 'like', '%' . $cleanKtp . '%');
                });
                Log::info('API: Mencari berdasarkan KTP', ['ktp' => $cleanKtp]);
            } else {
                // Jika bukan KTP, cari berdasarkan berbagai kriteria
                $query->where(function($q) use ($search, $cleanKtp) {
                    $q->where('no_rkm_medis', 'like', '%' . $search . '%')
                      ->orWhere('nm_pasien', 'like', '%' . $search . '%')
                      ->orWhere('no_ktp', 'like', '%' . $cleanKtp . '%')
                      ->orWhere('alamat', 'like', '%' . $search . '%')
                      ->orWhere('no_tlp', 'like', '%' . $cleanKtp . '%');
                });
                Log::info('API: Mencari berdasarkan multiple kriteria', [
                    'search' => $search,
                    'cleanKtp' => $cleanKtp
                ]);
            }
            
            // Debug query yang akan dijalankan
            Log::info('API: Query yang akan dijalankan', [
                'sql' => $query->toSql(),
                'bindings' => $query->getBindings()
            ]);
            
            // Eksekusi query dengan limit
            $results = $query->limit(10)->get();
            
            // Log hasil pencarian
            Log::info('API: Hasil pencarian', [
                'total_results' => $results->count(),
                'first_result' => $results->first()
            ]);
            
            return $results;

        } catch (\Exception $e) {
            // Log error jika terjadi
            Log::error('API: Error saat mencari pasien', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            throw $e;
        }
    }

    // Method untuk toggle visibilitas form pendaftaran
    public function toggleFormPendaftaran()
    {
        // Gunakan pendekatan yang lebih sederhana
        $this->showFormPendaftaran = !$this->showFormPendaftaran;
        
        // Tambahkan sleep kecil untuk memastikan proses berjalan dengan benar
        usleep(100000); // 100ms
        
        // Emit event setelah toggle selesai
        $this->emit('toggleFormPendaftaran', $this->showFormPendaftaran);

    }

    // Method untuk menghitung status pasien berdasarkan umur
    public function hitungStatusPasien($umur)
    {
        if ($umur < 6) {
            return 'Balita';
        } elseif ($umur < 10) {
            return 'PraSekolah';
        } elseif ($umur < 18) {
            return 'Remaja';
        } elseif ($umur < 60) {
            return 'Produktif';
        } else {
            return 'Lansia';
        }
    }
    
    // Method untuk update status pasien di kolom nip
    public function updateStatusPasien($no_rkm_medis, $status)
    {
        DB::table('pasien')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->update(['nip' => $status]);
    }

    public function resetSearch()
    {
        $this->searchTerm = '';
        $this->emit('refreshDatatable');
        $this->render();
    }

    public function resetStatusFilter()
    {
        $this->filterStatus = '';
        $this->emit('refreshDatatable');
        $this->render();
    }
}
