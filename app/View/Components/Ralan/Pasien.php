<?php

namespace App\View\Components\ralan;

use Illuminate\View\Component;
use Illuminate\Support\Facades\DB;

class Pasien extends Component
{
    public $data;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($noRawat)
    {
        // Tambahkan logging untuk debugging
        \Illuminate\Support\Facades\Log::info('Konstruktor Pasien Component', [
            'no_rawat_received' => $noRawat,
            'empty_check' => empty($noRawat)
        ]);
        
        if (empty($noRawat)) {
            $this->data = null;
            return;
        }
        
        // Coba bersihkan parameter jika diperlukan
        $noRawat = trim($noRawat);
        
        // Log nilai yang akan digunakan untuk pencarian
        \Illuminate\Support\Facades\Log::info('Parameter pencarian yang akan digunakan', [
            'no_rawat_clean' => $noRawat
        ]);
        
        try {
            // LANGKAH 1: Coba semua kemungkinan dekoding
            $possibleValues = $this->tryVariousDecodings($noRawat);
            $result = $this->tryAllPossibleValues($possibleValues);
            
            if ($result) {
                $this->data = $result;
                \Illuminate\Support\Facades\Log::info('Berhasil menemukan data dengan berbagai percobaan dekode');
                return;
            }
            
            // LANGKAH 2: Jika masih belum berhasil, coba query standard
            $this->data = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
                ->where('reg_periksa.no_rawat', $noRawat)
                ->select(
                    'pasien.*',
                    'penjab.png_jawab',
                    'reg_periksa.no_reg',
                    'reg_periksa.no_rawat',
                    'reg_periksa.tgl_registrasi',
                    'reg_periksa.jam_reg',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.no_rkm_medis',
                    'reg_periksa.kd_poli',
                    'reg_periksa.p_jawab',
                    'reg_periksa.almt_pj',
                    'reg_periksa.hubunganpj',
                    'reg_periksa.biaya_reg',
                    'reg_periksa.stts',
                    'reg_periksa.status_lanjut',
                    'reg_periksa.kd_pj',
                    'dokter.nm_dokter',
                    'poliklinik.nm_poli',
                    'catatan_pasien.catatan',
                    'personal_pasien.gambar'
                )
                ->first();
                
            // Tambahkan logging untuk hasil query
            \Illuminate\Support\Facades\Log::info('Hasil Query Pasien Component', [
                'no_rawat' => $noRawat,
                'data_found' => !is_null($this->data),
                'data_sample' => $this->data ? [
                    'no_rawat' => $this->data->no_rawat ?? null,
                    'no_rkm_medis' => $this->data->no_rkm_medis ?? null,
                    'nm_pasien' => $this->data->nm_pasien ?? null
                ] : null
            ]);
            
            // LANGKAH 3: Jika masih belum berhasil, coba strategi lain
            if (is_null($this->data)) {
                // Ambil 5 baris pertama dari tabel reg_periksa untuk debugging
                $sampleData = DB::table('reg_periksa')
                    ->limit(5)
                    ->get();
                    
                \Illuminate\Support\Facades\Log::info('Sample data dari reg_periksa (5 baris pertama)', [
                    'count' => count($sampleData),
                    'sample' => $sampleData
                ]);
                
                $searches = [
                    // Pendekatan 1: Gunakan LIKE untuk no_rawat
                    'LIKE_search' => function() use ($noRawat) {
                        \Illuminate\Support\Facades\Log::info('Mencoba query dengan LIKE untuk no_rawat');
                        return $this->searchWithLike($noRawat);
                    },
                    
                    // Pendekatan 2: Coba jika parameter adalah no_rkm_medis
                    'no_rkm_medis_search' => function() use ($noRawat) {
                        \Illuminate\Support\Facades\Log::info('Mencoba mencari dengan no_rkm_medis');
                        return $this->searchByRkmMedis($noRawat);
                    },
                    
                    // Pendekatan 3: Coba jika parameter adalah tanggal dalam format lain
                    'date_format_search' => function() use ($noRawat) {
                        if (strpos($noRawat, '/') !== false || strpos($noRawat, '-') !== false) {
                            \Illuminate\Support\Facades\Log::info('Mencoba format tanggal alternatif untuk no_rawat');
                            return $this->searchByDateFormat($noRawat);
                        }
                        return null;
                    }
                ];
                
                // Jalankan semua pendekatan pencarian sampai berhasil
                foreach ($searches as $name => $searchFunc) {
                    $result = $searchFunc();
                    if (!is_null($result)) {
                        $this->data = $result;
                        \Illuminate\Support\Facades\Log::info("Berhasil menemukan data dengan pendekatan: $name");
                        break;
                    }
                }
            }
                
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada Pasien Component: ' . $e->getMessage());
            $this->data = null;
        }
    }
    
    /**
     * Mencari data pasien dengan LIKE query
     */
    private function searchWithLike($noRawat)
    {
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', 'LIKE', '%' . $noRawat . '%')
            ->select(
                'pasien.*',
                'penjab.png_jawab',
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.biaya_reg',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar'
            )
            ->first();
    }
    
    /**
     * Mencari data pasien dengan no_rkm_medis
     */
    private function searchByRkmMedis($noRkmMedis)
    {
        $result = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rkm_medis', $noRkmMedis)
            // Tambahan: ambil registrasi terakhir untuk pasien ini
            ->orderBy('reg_periksa.tgl_registrasi', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc')
            ->select(
                'pasien.*',
                'penjab.png_jawab',
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.biaya_reg',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar'
            )
            ->first();
            
        \Illuminate\Support\Facades\Log::info('Hasil pencarian dengan no_rkm_medis', [
            'no_rkm_medis' => $noRkmMedis,
            'found' => !is_null($result)
        ]);
            
        return $result;
    }
    
    /**
     * Mencari data pasien dengan format tanggal alternatif
     */
    private function searchByDateFormat($noRawat)
    {
        // Coba format tanggal yang berbeda
        $dateFormats = [
            // Format tanggal yang mungkin ada di no_rawat
            '\d{4}/\d{2}/\d{2}',
            '\d{4}-\d{2}-\d{2}',
            '\d{2}/\d{2}/\d{4}',
            '\d{2}-\d{2}-\d{4}'
        ];
        
        // Cari pola tanggal di string no_rawat
        $matchedDate = null;
        foreach ($dateFormats as $format) {
            if (preg_match("/$format/", $noRawat, $matches)) {
                $matchedDate = $matches[0];
                break;
            }
        }
        
        if (!$matchedDate) {
            return null;
        }
        
        \Illuminate\Support\Facades\Log::info('Menemukan format tanggal dalam no_rawat', [
            'no_rawat' => $noRawat,
            'matched_date' => $matchedDate
        ]);
        
        // Cari dengan pola tanggal
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', 'LIKE', '%' . $matchedDate . '%')
            ->select(
                'pasien.*',
                'penjab.png_jawab',
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.biaya_reg',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar'
            )
            ->first();
    }

    /**
     * Fungsi khusus untuk mencoba berbagai kemungkinan dekoding no_rawat
     */
    private function tryVariousDecodings($noRawat) 
    {
        $attempts = [
            'original' => $noRawat,
            'trim' => trim($noRawat),
            'base64_decode' => function() use ($noRawat) {
                // Coba dekode jika sepertinya dalam format base64
                if (preg_match('/^[a-zA-Z0-9\/\+=]+$/', $noRawat)) {
                    $decoded = base64_decode($noRawat);
                    if ($decoded !== false) {
                        return $decoded;
                    }
                }
                return null;
            },
            'url_decode' => urldecode($noRawat),
            'url_and_base64' => function() use ($noRawat) {
                $decoded = urldecode($noRawat);
                if (preg_match('/^[a-zA-Z0-9\/\+=]+$/', $decoded)) {
                    $base64decoded = base64_decode($decoded);
                    if ($base64decoded !== false) {
                        return $base64decoded;
                    }
                }
                return null;
            }
        ];
        
        // Log semua upaya dekode
        $results = [];
        foreach ($attempts as $name => $value) {
            if (is_callable($value)) {
                $results[$name] = $value();
            } else {
                $results[$name] = $value;
            }
        }
        
        \Illuminate\Support\Facades\Log::info('Hasil berbagai percobaan dekode no_rawat', [
            'input' => $noRawat,
            'results' => $results
        ]);
        
        // Kumpulkan semua nilai yang valid
        $validValues = [];
        foreach ($results as $name => $value) {
            if (!empty($value) && is_string($value)) {
                $validValues[$name] = $value;
            }
        }
        
        return $validValues;
    }
    
    /**
     * Mencoba setiap nilai yang mungkin sampai menemukan data
     */
    private function tryAllPossibleValues($values) 
    {
        foreach ($values as $name => $value) {
            \Illuminate\Support\Facades\Log::info("Mencoba pencarian dengan nilai: $name", ['value' => $value]);
            
            // Coba cari dengan nilai ini
            $data = DB::table('reg_periksa')
                ->where('no_rawat', $value)
                ->first();
                
            if ($data) {
                \Illuminate\Support\Facades\Log::info("Berhasil menemukan data dengan nilai: $name", [
                    'no_rawat' => $data->no_rawat,
                    'no_rkm_medis' => $data->no_rkm_medis
                ]);
                
                // Dapatkan data lengkap
                return $this->getFullPatientData($data->no_rawat);
            }
        }
        
        return null;
    }
    
    /**
     * Mendapatkan data pasien lengkap
     */
    private function getFullPatientData($noRawat)
    {
        \Illuminate\Support\Facades\Log::info("Mengambil data lengkap untuk no_rawat: $noRawat");
        
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->leftJoin('catatan_pasien', 'reg_periksa.no_rkm_medis', '=', 'catatan_pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->leftJoin('personal_pasien', 'pasien.no_rkm_medis', '=', 'personal_pasien.no_rkm_medis')
            ->where('reg_periksa.no_rawat', $noRawat)
            ->select(
                'pasien.*',
                'penjab.png_jawab',
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'reg_periksa.tgl_registrasi',
                'reg_periksa.jam_reg',
                'reg_periksa.kd_dokter',
                'reg_periksa.no_rkm_medis',
                'reg_periksa.kd_poli',
                'reg_periksa.p_jawab',
                'reg_periksa.almt_pj',
                'reg_periksa.hubunganpj',
                'reg_periksa.biaya_reg',
                'reg_periksa.stts',
                'reg_periksa.status_lanjut',
                'reg_periksa.kd_pj',
                'dokter.nm_dokter',
                'poliklinik.nm_poli',
                'catatan_pasien.catatan',
                'personal_pasien.gambar'
            )
            ->first();
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.ralan.pasien', [
            'data' => $this->data, 
            'dokter' => session()->get('username')
        ]);
    }

    /**
     * Helper untuk mendekode no_rawat dengan aman
     * 
     * @param string $encodedValue
     * @return string
     */
    private function safeDecodeNoRawat($encodedValue)
    {
        \Illuminate\Support\Facades\Log::info('Pasien Component: Mencoba mendekode no_rawat: ' . $encodedValue);
        
        if (empty($encodedValue)) {
            return '';
        }
        
        // Coba dekripsi dengan cara biasa
        try {
            $decodedValue = $this->decryptData($encodedValue);
            // Validasi hasil dekripsi (harus memiliki format yang benar)
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedValue)) {
                \Illuminate\Support\Facades\Log::info('Pasien Component: No Rawat berhasil didekripsi metode standar: ' . $decodedValue);
                return $decodedValue;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Pasien Component: Gagal decrypt no_rawat [metode 1]: ' . $e->getMessage());
        }
        
        // Jika mengandung karakter % berarti URL encoded
        if (strpos($encodedValue, '%') !== false) {
            try {
                // Dekode URL dulu
                $urlDecoded = urldecode($encodedValue);
                
                // Coba base64 dekode
                $base64Decoded = base64_decode($urlDecoded, true); // strict mode
                
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    \Illuminate\Support\Facades\Log::info('Pasien Component: No Rawat berhasil didekode dengan url+base64: ' . $base64Decoded);
                    return $base64Decoded;
                }
                
                // Jika masih mengandung % setelah urldecode, coba lagi
                if (strpos($urlDecoded, '%') !== false) {
                    $doubleUrlDecoded = urldecode($urlDecoded);
                    $base64Decoded = base64_decode($doubleUrlDecoded, true);
                    
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        \Illuminate\Support\Facades\Log::info('Pasien Component: No Rawat berhasil didekode dengan double-url+base64: ' . $base64Decoded);
                        return $base64Decoded;
                    }
                }
                
                // Cobalah menghapus %3D di akhir (=) secara manual jika ada
                if (substr($encodedValue, -3) === '%3D') {
                    $trimmedEncoded = substr($encodedValue, 0, -3);
                    $urlDecodedTrimmed = urldecode($trimmedEncoded);
                    
                    // Tambahkan padding jika perlu
                    $paddedBase64 = $urlDecodedTrimmed . str_repeat('=', 4 - (strlen($urlDecodedTrimmed) % 4));
                    $base64DecodedTrimmed = base64_decode($paddedBase64, true);
                    
                    if ($base64DecodedTrimmed !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64DecodedTrimmed)) {
                        \Illuminate\Support\Facades\Log::info('Pasien Component: No Rawat berhasil didekode dengan trim+padding+base64: ' . $base64DecodedTrimmed);
                        return $base64DecodedTrimmed;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Pasien Component: Gagal mendekode no_rawat [metode url]: ' . $e->getMessage());
            }
        }
        
        // Jika merupakan data base64 biasa, coba decode langsung
        if (preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $encodedValue)) {
            try {
                $directBase64Decoded = base64_decode($encodedValue, true);
                
                if ($directBase64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $directBase64Decoded)) {
                    \Illuminate\Support\Facades\Log::info('Pasien Component: No Rawat berhasil didekode dengan direct base64: ' . $directBase64Decoded);
                    return $directBase64Decoded;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Pasien Component: Gagal mendekode no_rawat [metode direct base64]: ' . $e->getMessage());
            }
        }
        
        // Jika sampai di sini dan belum berhasil, coba cari no_rawat di database berdasarkan tanggal
        try {
            // Gunakan tanggal hari ini sebagai fallback
            $possibleDate = date('Y/m/d');
            
            $cekRawat = DB::table('reg_periksa')
                ->where('no_rawat', 'like', $possibleDate . '%')
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->first();
                
            if ($cekRawat) {
                \Illuminate\Support\Facades\Log::info('Pasien Component: Berhasil menemukan no_rawat berdasarkan tanggal: ' . $cekRawat->no_rawat);
                return $cekRawat->no_rawat;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Pasien Component: Gagal mencari no_rawat di database: ' . $e->getMessage());
        }
        
        \Illuminate\Support\Facades\Log::warning('Pasien Component: Tidak berhasil mendekode no_rawat, mengembalikan nilai asli: ' . $encodedValue);
        return $encodedValue;
    }

    public function mount($noRawat)
    {
        $this->no_rawat_received = $noRawat;
        \Illuminate\Support\Facades\Log::info('Konstruktor Pasien Component', [
            'no_rawat_received' => $this->no_rawat_received,
            'empty_check' => empty($this->no_rawat_received)
        ]);
        
        // Gunakan metode dekode yang lebih aman
        $decodedNoRawat = $this->safeDecodeNoRawat($noRawat);
        
        // Validasi format no_rawat sebelum query database
        if (!empty($decodedNoRawat) && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedNoRawat)) {
            try {
                $this->pasien = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
                    ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                    ->where('reg_periksa.no_rawat', $decodedNoRawat)
                    ->select('reg_periksa.*', 'pasien.*', 'dokter.nm_dokter', 'poliklinik.nm_poli')
                    ->first();
                
                if ($this->pasien) {
                    \Illuminate\Support\Facades\Log::info('Berhasil menemukan data dengan dekode yang aman', [
                        'no_rawat' => $decodedNoRawat,
                        'no_rkm_medis' => $this->pasien->no_rkm_medis
                    ]);
                } else {
                    \Illuminate\Support\Facades\Log::warning('Tidak ditemukan data pasien untuk no_rawat yang valid: ' . $decodedNoRawat);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error query database di Pasien component: ' . $e->getMessage());
            }
        } else {
            \Illuminate\Support\Facades\Log::warning('Format no_rawat tidak valid setelah dekode: ' . $decodedNoRawat);
        }
    }
}
