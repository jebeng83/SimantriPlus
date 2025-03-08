<?php

namespace App\Http\Livewire\Pasien;

use Livewire\Component;
use App\Models\Pasien;
use App\Models\Posyandu;
use App\Models\Penjab;
use App\Models\PerusahaanPasien;
use App\Models\SukuBangsa;
use App\Models\BahasaPasien;
use App\Models\Kelurahan;
use App\Models\Kecamatan;
use App\Models\Kabupaten;
use App\Models\Propinsi;
use App\Models\CacatFisik;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class FormPendaftaran extends Component
{
    // Properties untuk form
    public $no_rkm_medis;
    public $nm_pasien;
    public $no_ktp;
    public $jk = 'L';
    public $tmp_lahir;
    public $tgl_lahir;
    public $nm_ibu;
    public $alamat;
    public $gol_darah = '-';
    public $pekerjaan = 'Swasta';
    public $stts_nikah = 'MENIKAH';
    public $agama = 'ISLAM';
    public $tgl_daftar;
    public $no_tlp;
    public $umur_tahun;
    public $umur_bulan;
    public $umur_hari;
    public $pnd = '-';
    public $keluarga = 'AYAH';
    public $namakeluarga;
    public $asuransi = '-';
    public $no_peserta;
    public $pekerjaanpj;
    public $alamatpj = 'ALAMAT';
    public $suku_bangsa = 5;
    public $bahasa_pasien = 11;
    public $perusahaan_pasien = '-';
    public $email = 'pedot@gmail.com';
    public $cacat_fisik = 5;
    public $kd_pj = '-';
    public $kelurahan = 'KELURAHAN';
    public $kecamatan = 'KECAMATAN';
    public $kabupaten = 'KABUPATEN';
    public $propinsi = 'PROPINSI';
    public $kelurahanpj = 'KELURAHAN';
    public $kecamatanpj = 'KECAMATAN';
    public $kabupatenpj = 'KABUPATEN';
    public $propinsipj = 'PROPINSI';
    public $no_kk;
    public $data_posyandu;
    public $status = 'Kepala Keluarga';
    public $sama_dengan_pasien = false;
    public $penjab_list;
    public $perusahaan_list;
    public $suku_bangsa_list;
    public $bahasa_list;
    public $kelurahan_list;
    public $kecamatan_list;
    public $kabupaten_list;
    public $propinsi_list;
    public $cacat_fisik_list;
    public $nip = '0';

    // Properties untuk pencarian
    public $search_kelurahan = '';
    public $search_kecamatan = '';
    public $search_kabupaten = '';
    public $search_propinsi = '';

    // Properties untuk dropdown
    public $showKelurahanDropdown = false;
    public $showKecamatanDropdown = false;
    public $showKabupatenDropdown = false;
    public $showPropinsiDropdown = false;

    public $filtered_kelurahan;
    public $filtered_kecamatan;
    public $filtered_kabupaten;
    public $filtered_propinsi;

    public $filtered_posyandu;
    public $showPosyanduDropdown = false;
    public $search_posyandu = '';
    public $posyandu_not_found = false;

    // public function generateNoRekamMedis()
    // {
    //     try {
    //         // Hitung total data pasien
    //         $totalPasien = DB::table('pasien')->count();
            
    //         // Tambahkan 1 untuk nomor berikutnya
    //         $nextNumber = $totalPasien + 1;
            
    //         // Format nomor dengan padding 6 digit
    //         $this->no_rkm_medis = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
    //         \Log::info('Generated RM number:', [
    //             'total_pasien' => $totalPasien,
    //             'next_number' => $nextNumber,
    //             'formatted' => $this->no_rkm_medis
    //         ]);

    //     } catch (\Exception $e) {
    //         \Log::error('Error generating RM number: ' . $e->getMessage());
    //         // Set default jika terjadi error
    //         $this->no_rkm_medis = '000001';
    //     }
    // }

public function generateNoRekamMedis()
{
    try {
        // Ambil nomor terakhir dengan urutan numerik yang benar
        $lastRecord = DB::table('pasien')
                        ->orderByRaw('CAST(no_rkm_medis AS UNSIGNED) DESC')
                        ->first();

        // Ekstrak angka terakhir
        $lastNumber = $lastRecord ? (int)$lastRecord->no_rkm_medis : 0;

        // Increment dan format
        $nextNumber = $lastNumber + 1;
        $this->no_rkm_medis = str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        // Log informasi
        \Log::info('Generated RM number:', [
            'last_number' => $lastNumber,
            'next_number' => $nextNumber,
            'formatted' => $this->no_rkm_medis
        ]);

    } catch (\Exception $e) {
        \Log::error('Error generating RM number: ' . $e->getMessage());
        $this->no_rkm_medis = '000001';
    }
}



    

    public function mount()
    {
        try {
            // Generate nomor rekam medis
            $this->generateNoRekamMedis();

            // Set default values
            $this->tgl_daftar = date('Y-m-d');
            $this->tgl_lahir = date('Y-m-d');
            $this->nip = '0';
            
            // Set nilai default sesuai permintaan
            $this->no_tlp = '081';
            $this->no_peserta = '0000';
            $this->no_kk = '0';
            $this->kd_pj = '-'; // Nilai default untuk kd_pj
            $this->suku_bangsa = 5; // ID untuk JAWA
            $this->bahasa_pasien = 11; // ID untuk JAWA
            $this->cacat_fisik = 5; // ID untuk TIDAK ADA

            // Initialize lists sebagai array kosong
            $this->penjab_list = [];
            $this->perusahaan_list = [];
            $this->suku_bangsa_list = [];
            $this->bahasa_list = [];
            $this->cacat_fisik_list = [];
            $this->filtered_kelurahan = [];
            $this->filtered_kecamatan = [];
            $this->filtered_kabupaten = [];
            $this->filtered_propinsi = [];
            $this->filtered_posyandu = [];

            // Ambil data dari database
            $this->penjab_list = DB::table('penjab')
                ->select('kd_pj', 'png_jawab')
                ->get()
                ->map(function($item) {
                    return [
                        'kd_pj' => $item->kd_pj,
                        'png_jawab' => $item->png_jawab
                    ];
                })->toArray();

            $this->perusahaan_list = DB::table('perusahaan_pasien')
                ->select('kode_perusahaan', 'nama_perusahaan')
                ->get()
                ->map(function($item) {
                    return [
                        'kode_perusahaan' => $item->kode_perusahaan,
                        'nama_perusahaan' => $item->nama_perusahaan
                    ];
                })->toArray();

            $this->suku_bangsa_list = DB::table('suku_bangsa')
                ->select('id', 'nama_suku_bangsa')
                ->orderBy('nama_suku_bangsa')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => (int)$item->id,
                        'nama_suku_bangsa' => $item->nama_suku_bangsa
                    ];
                })->toArray();

            $this->bahasa_list = DB::table('bahasa_pasien')
                ->select('id', 'nama_bahasa')
                ->orderBy('nama_bahasa')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => (int)$item->id,
                        'nama_bahasa' => $item->nama_bahasa
                    ];
                })->toArray();

            $this->cacat_fisik_list = DB::table('cacat_fisik')
                ->select('id', 'nama_cacat')
                ->orderBy('nama_cacat')
                ->get()
                ->map(function($item) {
                    return [
                        'id' => (int)$item->id,
                        'nama_cacat' => $item->nama_cacat
                    ];
                })->toArray();

            // Initialize collections kosong untuk dropdown
            $this->filtered_kelurahan = collect([]);
            $this->filtered_kecamatan = collect([]);
            $this->filtered_kabupaten = collect([]);
            $this->filtered_propinsi = collect([]);
            $this->filtered_posyandu = [];

        } catch (\Exception $e) {
            \Log::error('Error in mount: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Initialize empty arrays if error occurs
            $this->penjab_list = [];
            $this->perusahaan_list = [];
            $this->suku_bangsa_list = [];
            $this->bahasa_list = [];
            $this->cacat_fisik_list = [];
            $this->propinsi_list = [];
        }
    }

    public function hitungUmur()
    {
        if ($this->tgl_lahir) {
            $birthDate = new \DateTime($this->tgl_lahir);
            $today = new \DateTime('today');
            $diff = $today->diff($birthDate);
            
            $this->umur_tahun = $diff->y;
            $this->umur_bulan = $diff->m;
            $this->umur_hari = $diff->d;
        }
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'tgl_lahir') {
            $this->hitungUmur();
        }
    }

    public function updatedSamaDenganPasien($value)
    {
        if ($value) {
            // Copy alamat pasien ke alamat PJ
            $this->alamatpj = $this->alamat;
            $this->kelurahanpj = $this->search_kelurahan; // Menggunakan nama kelurahan
            $this->kecamatanpj = $this->search_kecamatan; // Menggunakan nama kecamatan
            $this->kabupatenpj = $this->search_kabupaten; // Menggunakan nama kabupaten
            $this->propinsipj = $this->search_propinsi;   // Menggunakan nama propinsi
        } else {
            // Reset alamat PJ
            $this->alamatpj = 'ALAMAT';
            $this->kelurahanpj = 'KELURAHAN';
            $this->kecamatanpj = 'KECAMATAN';
            $this->kabupatenpj = 'KABUPATEN';
            $this->propinsipj = 'PROPINSI';
        }
    }

    public function updatedPropinsi($value)
    {
        if ($value) {
            try {
                $this->kabupaten_list = DB::table('kabupaten')
                    ->where('kd_prop', $value)
                    ->select('kd_kab', 'nm_kab')
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error loading kabupaten: ' . $e->getMessage());
                $this->kabupaten_list = collect();
            }
        }
    }

    public function updatedKabupaten($value)
    {
        if ($value) {
            try {
                $this->kecamatan_list = DB::table('kecamatan')
                    ->where('kd_kab', $value)
                    ->select('kd_kec', 'nm_kec')
                    ->get();
            } catch (\Exception $e) {
                \Log::error('Error loading kecamatan: ' . $e->getMessage());
                $this->kecamatan_list = collect();
            }
        }
    }

    public function updatedKecamatan($value)
    {
        if ($value) {
            try {
                $this->kelurahan_list = DB::table('kelurahan')
                    ->where('kd_kec', $value)
                    ->select('kd_kel', 'nm_kel')
                    ->orderBy('nm_kel')
                    ->get()
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('Error loading kelurahan: ' . $e->getMessage());
                $this->kelurahan_list = [];
            }
        }
    }

    public function updatedKelurahan($value)
    {
        if ($value) {
            try {
                $this->filtered_posyandu = DB::table('data_posyandu')
                    ->where('kd_kel', $value)
                    ->select(['kode_posyandu', 'nama_posyandu'])
                    ->get()
                    ->toArray();
            } catch (\Exception $e) {
                \Log::error('Error loading posyandu: ' . $e->getMessage());
                $this->filtered_posyandu = [];
            }
        } else {
            $this->filtered_posyandu = [];
        }

        // Reset pilihan posyandu ketika kelurahan berubah
        $this->data_posyandu = '';
    }

    // Tambahkan method untuk debugging
    public function getFilteredPosyanduProperty()
    {
        \Log::info('Current filtered_posyandu:', [
            'data' => $this->filtered_posyandu,
            'kelurahan' => $this->kelurahan
        ]);
        return $this->filtered_posyandu;
    }

    protected function updateAlamatLengkap()
    {
        try {
            $kelurahan = Kelurahan::select('nm_kel')->where('kd_kel', $this->kelurahan)->first();
            $kecamatan = Kecamatan::select('nm_kec')->where('kd_kec', $this->kecamatan)->first();
            $kabupaten = Kabupaten::select('nm_kab')->where('kd_kab', $this->kabupaten)->first();
            $propinsi = Propinsi::select('nm_prop')->where('kd_prop', $this->propinsi)->first();

            $alamat_lengkap = [];
            if ($this->alamat) $alamat_lengkap[] = strtoupper($this->alamat);
            if ($kelurahan) $alamat_lengkap[] = strtoupper($kelurahan->nm_kel);
            if ($kecamatan) $alamat_lengkap[] = strtoupper($kecamatan->nm_kec);
            if ($kabupaten) $alamat_lengkap[] = strtoupper($kabupaten->nm_kab);
            if ($propinsi) $alamat_lengkap[] = strtoupper($propinsi->nm_prop);

            $this->alamat = implode(', ', $alamat_lengkap);
        } catch (\Exception $e) {
            \Log::error('Error updating alamat: ' . $e->getMessage());
        }
    }

    protected function updatePosyanduList()
    {
        try {
            if ($this->kelurahan) {
                $this->filtered_posyandu = DB::table('data_posyandu')
                    ->where('kd_kel', $this->kelurahan)
                    ->select(['kode_posyandu', 'nama_posyandu'])
                    ->get()
                    ->map(function($item) {
                        return [
                            'kode_posyandu' => $item->kode_posyandu,
                            'nama_posyandu' => $item->nama_posyandu
                        ];
                    })->toArray();
            } else {
                $this->filtered_posyandu = [];
            }
        } catch (\Exception $e) {
            \Log::error('Error loading posyandu: ' . $e->getMessage());
            $this->filtered_posyandu = [];
        }
    }

    public function save()
    {
        try {
            DB::beginTransaction();

            // Validasi input
            $this->validate([
                'nm_pasien' => 'required',
                'no_ktp' => 'required',
                'jk' => 'required',
                'tmp_lahir' => 'required',
                'tgl_lahir' => 'required|date',
                'nm_ibu' => 'required',
                'alamat' => 'required',
                'namakeluarga' => 'required'
            ]);

            // Hitung umur
            $this->hitungUmur();
            $umur = $this->umur_tahun . " Th " . 
                    $this->umur_bulan . " Bl " . 
                    $this->umur_hari . " Hr";

            // Siapkan data untuk disimpan
            $data = [
                'no_rkm_medis' => $this->no_rkm_medis,
                'nm_pasien' => strtoupper($this->nm_pasien),
                'no_ktp' => $this->no_ktp,
                'jk' => $this->jk,
                'tmp_lahir' => strtoupper($this->tmp_lahir),
                'tgl_lahir' => $this->tgl_lahir,
                'nm_ibu' => strtoupper($this->nm_ibu),
                'alamat' => strtoupper($this->alamat),
                'gol_darah' => $this->gol_darah,
                'pekerjaan' => strtoupper($this->pekerjaan ?: '-'),
                'stts_nikah' => $this->stts_nikah,
                'agama' => $this->agama,
                'tgl_daftar' => $this->tgl_daftar,
                'no_tlp' => $this->no_tlp,
                'umur' => $umur,
                'pnd' => $this->pnd,
                'keluarga' => $this->keluarga,
                'namakeluarga' => strtoupper($this->namakeluarga),
                'kd_pj' => $this->kd_pj,
                'no_peserta' => $this->no_peserta,
                'kd_kel' => $this->kelurahan,
                'kd_kec' => $this->kecamatan,
                'kd_kab' => $this->kabupaten,
                'kd_prop' => $this->propinsi,
                'pekerjaanpj' => strtoupper($this->pekerjaanpj ?: '-'),
                'alamatpj' => strtoupper($this->alamatpj),
                'kelurahanpj' => strtoupper($this->kelurahanpj),
                'kecamatanpj' => strtoupper($this->kecamatanpj),
                'kabupatenpj' => strtoupper($this->kabupatenpj),
                'propinsipj' => strtoupper($this->propinsipj),
                'perusahaan_pasien' => $this->perusahaan_pasien,
                'suku_bangsa' => (int)$this->suku_bangsa,
                'bahasa_pasien' => (int)$this->bahasa_pasien,
                'cacat_fisik' => (int)$this->cacat_fisik,
                'email' => strtolower($this->email),
                'nip' => $this->nip ?: '0',
                'no_kk' => $this->no_kk,
                'data_posyandu' => $this->data_posyandu,
                'status' => $this->status
            ];

            // Log data sebelum insert
            \Log::info('Saving patient data:', $data);

            // Insert data
            DB::table('pasien')->insert($data);

            DB::commit();
            
            // Reset form dan generate nomor baru
            $this->reset();
            $this->mount();
            
            session()->flash('message', 'Data pasien berhasil disimpan.');
            $this->emit('showNotification');
            
            // Emit event untuk memperbarui daftar pasien
            $this->emit('refreshPasienList');
            $this->emit('refresh');
            
            // Dispatch browser event untuk refresh data jika menggunakan Alpine.js atau JavaScript murni
            $this->dispatchBrowserEvent('pasien-saved', ['message' => 'Data pasien berhasil disimpan']);

        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error saving patient: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menyimpan data: ' . $e->getMessage());
            $this->emit('showNotification');
        }
    }

    public function render()
    {
        // Debug log untuk melihat data yang dikirim ke view
        \Log::info('Render data:', [
            'kelurahan' => $this->kelurahan,
            'filtered_posyandu' => $this->filtered_posyandu
        ]);

        return view('livewire.pasien.form-pendaftaran', [
            'posyandu_list' => $this->filtered_posyandu,
            'penjab_list' => $this->penjab_list ?? [],
            'perusahaan_list' => $this->perusahaan_list ?? [],
            'suku_bangsa_list' => $this->suku_bangsa_list ?? [],
            'bahasa_list' => $this->bahasa_list ?? [],
            'cacat_fisik_list' => $this->cacat_fisik_list ?? [],
            'propinsi_list' => $this->propinsi_list ?? [],
            'kabupaten_list' => $this->kabupaten_list ?? [],
            'kecamatan_list' => $this->kecamatan_list ?? [],
            'kelurahan_list' => $this->kelurahan_list ?? [],
        ]);
    }

    public function updatedSearchKelurahan()
    {
        if (strlen($this->search_kelurahan) > 2) {
            try {
                $this->filtered_kelurahan = DB::table('kelurahan')
                    ->where('nm_kel', 'like', '%' . $this->search_kelurahan . '%')
                    ->select(['kd_kel', 'nm_kel'])
                    ->take(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'kd_kel' => $item->kd_kel,
                            'nm_kel' => $item->nm_kel
                        ];
                    })->toArray();

                $this->showKelurahanDropdown = true;
            } catch (\Exception $e) {
                \Log::error('Error searching kelurahan: ' . $e->getMessage());
                $this->filtered_kelurahan = [];
            }
        } else {
            $this->showKelurahanDropdown = false;
            $this->filtered_kelurahan = [];
        }
    }

    public function updatedSearchKecamatan()
    {
        if (strlen($this->search_kecamatan) > 2) {
            try {
                $this->filtered_kecamatan = DB::table('kecamatan')
                    ->where('nm_kec', 'like', '%' . $this->search_kecamatan . '%')
                    ->select(['kd_kec', 'nm_kec'])
                    ->take(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'kd_kec' => $item->kd_kec,
                            'nm_kec' => $item->nm_kec
                        ];
                    })->toArray();

                $this->showKecamatanDropdown = true;
            } catch (\Exception $e) {
                \Log::error('Error searching kecamatan: ' . $e->getMessage());
                $this->filtered_kecamatan = [];
            }
        } else {
            $this->showKecamatanDropdown = false;
            $this->filtered_kecamatan = [];
        }
    }

    public function updatedSearchKabupaten()
    {
        if (strlen($this->search_kabupaten) > 2) {
            try {
                $this->filtered_kabupaten = DB::table('kabupaten')
                    ->where('nm_kab', 'like', '%' . $this->search_kabupaten . '%')
                    ->select(['kd_kab', 'nm_kab'])
                    ->take(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'kd_kab' => $item->kd_kab,
                            'nm_kab' => $item->nm_kab
                        ];
                    })->toArray();

                $this->showKabupatenDropdown = true;
            } catch (\Exception $e) {
                \Log::error('Error searching kabupaten: ' . $e->getMessage());
                $this->filtered_kabupaten = [];
            }
        } else {
            $this->showKabupatenDropdown = false;
            $this->filtered_kabupaten = [];
        }
    }

    public function updatedSearchPropinsi()
    {
        if (strlen($this->search_propinsi) > 2) {
            try {
                $this->filtered_propinsi = DB::table('propinsi')
                    ->where('nm_prop', 'like', '%' . $this->search_propinsi . '%')
                    ->select(['kd_prop', 'nm_prop'])
                    ->take(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'kd_prop' => $item->kd_prop,
                            'nm_prop' => $item->nm_prop
                        ];
                    })->toArray();

                $this->showPropinsiDropdown = true;
            } catch (\Exception $e) {
                \Log::error('Error searching propinsi: ' . $e->getMessage());
                $this->filtered_propinsi = [];
            }
        } else {
            $this->showPropinsiDropdown = false;
            $this->filtered_propinsi = [];
        }
    }

    public function selectKelurahan($kd_kel, $nama)
    {
        try {
            // Cukup set kode dan nama kelurahan
            $this->kelurahan = $kd_kel;
            $this->search_kelurahan = $nama;
            $this->showKelurahanDropdown = false;

            // Log untuk debugging
            \Log::info('Selected kelurahan:', [
                'kd_kel' => $kd_kel,
                'nama' => $nama
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in selectKelurahan: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memilih kelurahan');
        }
    }

    public function selectKecamatan($kd_kec, $nama)
    {
        try {
            // Set kode dan nama kecamatan
            $this->kecamatan = $kd_kec;
            $this->search_kecamatan = $nama;
            $this->showKecamatanDropdown = false;

            // Ambil data kabupaten dan propinsi terkait
            $kecamatan = DB::table('kecamatan')
                ->where('kd_kec', $kd_kec)
                ->first();

            if ($kecamatan) {
                // Ambil data kabupaten
                $kabupaten = DB::table('kabupaten')
                    ->where('kd_kab', $kecamatan->kd_kab)
                    ->first();

                if ($kabupaten) {
                    $this->kabupaten = $kabupaten->kd_kab;
                    $this->search_kabupaten = $kabupaten->nm_kab;

                    // Ambil data propinsi
                    $propinsi = DB::table('propinsi')
                        ->where('kd_prop', $kabupaten->kd_prop)
                        ->first();

                    if ($propinsi) {
                        $this->propinsi = $propinsi->kd_prop;
                        $this->search_propinsi = $propinsi->nm_prop;
                    }
                }
            }

            // Reset kelurahan
            $this->kelurahan = '';
            $this->search_kelurahan = '';

            // Log untuk debugging
            \Log::info('Selected kecamatan:', [
                'kd_kec' => $kd_kec,
                'nama' => $nama,
                'kabupaten' => $this->kabupaten ?? null,
                'propinsi' => $this->propinsi ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in selectKecamatan: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memilih kecamatan');
        }
    }

    public function selectKabupaten($kd_kab, $nama)
    {
        try {
            // Set kode dan nama kabupaten
            $this->kabupaten = $kd_kab;
            $this->search_kabupaten = $nama;
            $this->showKabupatenDropdown = false;

            // Ambil data propinsi terkait
            $kabupaten = DB::table('kabupaten')
                ->where('kd_kab', $kd_kab)
                ->first();

            if ($kabupaten) {
                // Ambil data propinsi
                $propinsi = DB::table('propinsi')
                    ->where('kd_prop', $kabupaten->kd_prop)
                    ->first();

                if ($propinsi) {
                    $this->propinsi = $propinsi->kd_prop;
                    $this->search_propinsi = $propinsi->nm_prop;
                }
            }

            // Reset kelurahan dan kecamatan
            $this->kelurahan = '';
            $this->kecamatan = '';
            $this->search_kelurahan = '';
            $this->search_kecamatan = '';

            // Log untuk debugging
            \Log::info('Selected kabupaten:', [
                'kd_kab' => $kd_kab,
                'nama' => $nama,
                'propinsi' => $this->propinsi ?? null
            ]);

        } catch (\Exception $e) {
            \Log::error('Error in selectKabupaten: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memilih kabupaten');
        }
    }

    public function selectPropinsi($kd_prop, $nama)
    {
        try {
            $this->propinsi = $kd_prop;
            $this->search_propinsi = $nama;

            // Reset kelurahan, kecamatan, dan kabupaten
            $this->kelurahan = '';
            $this->kecamatan = '';
            $this->kabupaten = '';
            $this->search_kelurahan = '';
            $this->search_kecamatan = '';
            $this->search_kabupaten = '';

            $this->showPropinsiDropdown = false;

        } catch (\Exception $e) {
            \Log::error('Error in selectPropinsi: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat memilih propinsi');
        }
    }

    public function updatedSearchPosyandu()
    {
        if (strlen($this->search_posyandu) > 2) {
            try {
                $this->filtered_posyandu = DB::table('data_posyandu')
                    ->where('nama_posyandu', 'like', '%' . $this->search_posyandu . '%')
                    ->select(['kode_posyandu', 'nama_posyandu', 'desa'])
                    ->orderBy('nama_posyandu')
                    ->limit(5)
                    ->get()
                    ->map(function($item) {
                        return [
                            'kode_posyandu' => $item->kode_posyandu,
                            'nama_posyandu' => $item->nama_posyandu,
                            'desa' => $item->desa
                        ];
                    })->toArray();

                $this->showPosyanduDropdown = true;
                $this->posyandu_not_found = empty($this->filtered_posyandu);

                \Log::info('Posyandu search result:', [
                    'keyword' => $this->search_posyandu,
                    'count' => count($this->filtered_posyandu)
                ]);
            } catch (\Exception $e) {
                \Log::error('Error searching posyandu: ' . $e->getMessage());
                $this->filtered_posyandu = [];
                $this->posyandu_not_found = true;
            }
        } else {
            $this->showPosyanduDropdown = false;
            $this->filtered_posyandu = [];
            $this->posyandu_not_found = false;
        }
    }

    public function selectPosyandu($kd_posyandu, $nama)
    {
        $this->data_posyandu = $nama;
        $this->search_posyandu = $nama;
        $this->showPosyanduDropdown = false;
        $this->posyandu_not_found = false;
    }

    public function resetPosyanduSearch()
    {
        $this->search_posyandu = '';
        $this->filtered_posyandu = [];
        $this->showPosyanduDropdown = false;
        $this->posyandu_not_found = false;
    }

    // Tambahkan method untuk mendapatkan nama wilayah
    public function getNamaWilayah($kode, $tabel, $kolom_kode, $kolom_nama)
    {
        try {
            $data = DB::table($tabel)
                ->where($kolom_kode, $kode)
                ->value($kolom_nama);
            return $data ?? strtoupper($kode);
        } catch (\Exception $e) {
            \Log::error("Error getting nama wilayah: " . $e->getMessage());
            return strtoupper($kode);
        }
    }
} 