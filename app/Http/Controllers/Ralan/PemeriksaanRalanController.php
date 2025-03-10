<?php

namespace App\Http\Controllers\Ralan;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\EnkripsiData;
use Request;
use Illuminate\Support\Facades\Schema;

class PemeriksaanRalanController extends Controller
{
    use EnkripsiData;
    public $dokter, $noRawat, $noRM;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('loginauth');
        $this->middleware('decrypt')->except('getObat');
        $this->dokter = session()->get('username');
        $this->noRawat = Request::get('no_rawat');
        $this->noRM = Request::get('no_rm');
    }

    /**
     * Helper untuk mendekode no_rawat dengan aman
     * 
     * @param string $encodedValue
     * @return string
     */
    private function safeDecodeNoRawat($encodedValue)
    {
        // Log kritis dihapus untuk production
        // \Illuminate\Support\Facades\Log::info('Mencoba mendekode no_rawat: ' . $encodedValue);
        
        if (empty($encodedValue)) {
            return '';
        }
        
        // Pastikan data yang kita terima adalah string yang valid
        if (!is_string($encodedValue)) {
            $encodedValue = (string)$encodedValue;
        }
        
        // Hapus karakter non-printable
        $cleanValue = preg_replace('/[[:^print:]]/', '', $encodedValue);
        
        // Jika hasilnya kosong setelah pembersihan, gunakan nilai asli
        if (empty($cleanValue) && !empty($encodedValue)) {
            $cleanValue = $encodedValue;
        }
        
        // Log debug dihapus untuk production
        // \Illuminate\Support\Facades\Log::info('Nilai setelah dibersihkan: ' . $cleanValue);
        
        // Coba dekripsi dengan cara biasa
        try {
            $decodedValue = $this->decryptData($cleanValue);
            // Validasi hasil dekripsi (harus memiliki format yang benar)
            if (preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedValue)) {
                // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekripsi metode standar: ' . $decodedValue);
                return $decodedValue;
            }
        } catch (\Exception $e) {
            // Simpan log warning untuk error penting
            \Illuminate\Support\Facades\Log::warning('Gagal decrypt no_rawat: ' . $e->getMessage());
        }
        
        // Jika mengandung karakter % berarti URL encoded
        if (strpos($encodedValue, '%') !== false) {
            try {
                // Dekode URL dulu
                $urlDecoded = urldecode($encodedValue);
                // \Illuminate\Support\Facades\Log::info('URL decode result: ' . $urlDecoded);
                
                // Coba base64 dekode
                $base64Decoded = base64_decode($urlDecoded, true); // strict mode
                
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode dengan url+base64: ' . $base64Decoded);
                    return $base64Decoded;
                }
                
                // Jika masih mengandung % setelah urldecode, coba lagi
                if (strpos($urlDecoded, '%') !== false) {
                    $doubleUrlDecoded = urldecode($urlDecoded);
                    $base64Decoded = base64_decode($doubleUrlDecoded, true);
                    
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode dengan double-url+base64: ' . $base64Decoded);
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
                        // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode dengan trim+padding+base64: ' . $base64DecodedTrimmed);
                        return $base64DecodedTrimmed;
                    }
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mendekode no_rawat: ' . $e->getMessage());
            }
        }
        
        // Jika merupakan data base64 biasa, coba decode langsung
        if (preg_match('/^[A-Za-z0-9+\/]+={0,2}$/', $encodedValue)) {
            try {
                $directBase64Decoded = base64_decode($encodedValue, true);
                
                if ($directBase64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $directBase64Decoded)) {
                    // \Illuminate\Support\Facades\Log::info('No Rawat berhasil didekode dengan direct base64: ' . $directBase64Decoded);
                    return $directBase64Decoded;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mendekode no_rawat: ' . $e->getMessage());
            }
        }
        
        // Jika sampai di sini dan belum berhasil, coba cari no_rawat di database berdasarkan tanggal
        try {
            // Coba ekstrak tanggal dari parameter jika format bisa dikenali
            $possibleDate = null;
            if (preg_match('/\d{4}\/\d{2}\/\d{2}/', $encodedValue, $matches)) {
                $possibleDate = $matches[0];
            } elseif (strpos($encodedValue, '20') === 0 && strlen($encodedValue) >= 8) {
                // Coba format tanggal lain seperti YYYYMMDD di awal
                $year = substr($encodedValue, 0, 4);
                $month = substr($encodedValue, 4, 2);
                $day = substr($encodedValue, 6, 2);
                if (checkdate((int)$month, (int)$day, (int)$year)) {
                    $possibleDate = "$year/$month/$day";
                }
            }
            
            // Gunakan tanggal hari ini sebagai fallback
            if (!$possibleDate) {
                $possibleDate = date('Y/m/d');
            }
            
            $cekRawat = DB::table('reg_periksa')
                ->where('no_rawat', 'like', $possibleDate . '%')
                ->where('kd_dokter', session()->get('username'))
                ->orderBy('tgl_registrasi', 'desc')
                ->orderBy('jam_reg', 'desc')
                ->first();
                
            if ($cekRawat) {
                // \Illuminate\Support\Facades\Log::info('Berhasil menemukan no_rawat berdasarkan tanggal: ' . $cekRawat->no_rawat);
                return $cekRawat->no_rawat;
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::warning('Gagal mencari no_rawat di database: ' . $e->getMessage());
        }
        
        // \Illuminate\Support\Facades\Log::warning('Tidak berhasil mendekode no_rawat, mengembalikan nilai asli: ' . $encodedValue);
        return $encodedValue;
    }

    public function index()
    {
        $dokter = session()->get('username');
        $noRawat = Request::get('no_rawat');
        $noRM = Request::get('no_rm');
        
        // Log minimalis
        \Illuminate\Support\Facades\Log::info('Akses halaman pemeriksaan ralan', [
            'no_rawat' => $noRawat
        ]);
        
        // Dekode no_rawat
        $decodedNoRawat = $this->safeDecodeNoRawat($noRawat);
        
        $dataFound = false;
        $fallbackTriggered = false;
        $sampleData = null;
        
        // Verifikasi parameter
        if (!empty($decodedNoRawat) && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $decodedNoRawat)) {
            try {
                $pasienData = DB::table('reg_periksa')
                    ->where('no_rawat', $decodedNoRawat)
                    ->first();
                    
                if ($pasienData) {
                    $dataFound = true;
                    $noRawat = $decodedNoRawat;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error query database: ' . $e->getMessage());
            }
        }
        
        // Jika data tidak ditemukan, gunakan data pasien terbaru
        if (!$dataFound) {
            try {
                $recentPasien = DB::table('reg_periksa')
                    ->where('kd_dokter', $dokter)
                    ->orderBy('tgl_registrasi', 'desc')
                    ->orderBy('jam_reg', 'desc')
                    ->first();
                
                if ($recentPasien) {
                    $sampleData = $recentPasien->no_rawat;
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::warning('Gagal mendapatkan data contoh: ' . $e->getMessage());
            }
        }
        
        // Data untuk view
        $viewData = [
            'no_rawat' => $noRawat,
            'no_rm' => $noRM,
            'data_found' => $dataFound,
            'fallback_triggered' => $fallbackTriggered,
            'raw_param' => [
                'no_rawat_original' => Request::get('no_rawat', 'not_provided'),
                'no_rawat_decoded' => $decodedNoRawat,
                'no_rm_original' => Request::get('no_rm', 'not_provided')
            ],
            'param_info' => [
                'no_rawat_length' => $noRawat ? strlen($noRawat) : 0,
                'no_rm_length' => $noRM ? strlen($noRM) : 0,
                'has_special_chars' => $noRawat ? (preg_match('/[^a-zA-Z0-9\/\-\._]/', $noRawat) ? 'yes' : 'no') : 'n/a'
            ]
        ];
        
        if ($sampleData && !$dataFound) {
            $viewData['sample_data'] = $sampleData;
        }
        
        return view('ralan.pemeriksaan-ralan', $viewData);
    }

    public function hapusObat($noResep, $kdObat)
    {
        try {
            $delete = DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus']);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    public function hapusObatRacikan($noResep, $noRacikan)
    {
        try {
            $delete = DB::table('resep_dokter_racikan')->where('no_resep', $noResep)->where('no_racik', $noRacikan)->delete();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus']);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    public function postResepRacikan($noRawat)
    {
        $namaRacikan = Request::get('nama_racikan');
        $metodeRacikan = Request::get('metode_racikan');
        $jumlahRacikan = Request::get('jumlah_racikan');
        $aturanPakai = Request::get('aturan_racikan');
        $keteranganRacikan = Request::get('keterangan_racikan');
        
        try {
            // Dekripsi dan sanitasi no_rawat
            $originalNoRawat = $noRawat; // Simpan original untuk debugging jika diperlukan
            $no_rawat = $this->decryptData($noRawat);
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($no_rawat)) {
                $no_rawat = (string)$no_rawat;
            }
            
            // Hapus karakter non-printable
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $no_rawat);
            
            // Jika hasilnya kosong setelah pembersihan, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($no_rawat)) {
                $cleanNoRawat = $no_rawat;
            }
            
            // Verifikasi format no_rawat
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Format nomor rawat tidak valid: ' . $cleanNoRawat
                ]);
            }
            
            // Verifikasi no_rawat ada di database
            $cekNoRawat = DB::table('reg_periksa')
                ->where(DB::raw('BINARY no_rawat'), $cleanNoRawat)
                ->first();
                
            if (!$cekNoRawat) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'No Rawat tidak ditemukan di database'
                ]);
            }
            
            $dokter = session()->get('username');

            $validate = Request::validate([
                'nama_racikan' => 'required',
                'aturan_racikan' => 'required',
                'jumlah_racikan' => 'required',
                'metode_racikan' => 'required',
                'keterangan_racikan' => 'required',
            ]);

            // Generate nomor resep
            $no = DB::table('resep_obat')
                ->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')
                ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")
                ->first();
                
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Y-m-d'); // Format tanggal yang benar YYYY-MM-DD
            $tglNoResep = date('Ymd'); // Format untuk nomor resep
            $noResep = $tglNoResep . '' . $nextNo;

            // Cek apakah sudah ada resep racikan dengan nomor rawat dan tanggal yang sama
            $cek = DB::table('resep_obat')
                ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
                ->where(DB::raw('BINARY resep_obat.no_rawat'), $cleanNoRawat)
                ->where('resep_obat.tgl_peresepan', date('Y-m-d'))
                ->select('resep_obat.no_resep')
                ->first();

            if (!empty($cek)) {
                // Jika sudah ada, tambahkan racikan ke resep yang sudah ada
                $noRacik = DB::table('resep_dokter_racikan')
                    ->where('no_resep', $cek->no_resep)
                    ->max('no_racik');
                    
                $nextNoRacik = $noRacik + 1;
                
                $insert = DB::table('resep_dokter_racikan')
                    ->insert([
                        'no_resep' => $cek->no_resep,
                        'no_racik' => $nextNoRacik,
                        'nama_racik' => $namaRacikan,
                        'kd_racik' => $metodeRacikan,
                        'jml_dr' => $jumlahRacikan,
                        'aturan_pakai' => $aturanPakai,
                        'keterangan' => $keteranganRacikan,
                    ]);
                    
                if ($insert) {
                    return response()->json([
                        'status' => 'sukses', 
                        'pesan' => 'Racikan berhasil ditambahkan'
                    ]);
                }
            } else {
                // Jika belum ada, buat resep baru
                $insert = DB::table('resep_obat')
                    ->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => date('Y-m-d'),
                        'jam' => date('H:i:s'),
                        'no_rawat' => $cleanNoRawat, // Gunakan no_rawat yang sudah dibersihkan
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'ralan',
                        'tgl_penyerahan' => '0000-00-00',
                        'jam_penyerahan' => '00:00:00',
                    ]);
                if ($insert) {
                    $insert = DB::table('resep_dokter_racikan')
                        ->insert([
                            'no_resep' => $noResep,
                            'no_racik' => '1',
                            'nama_racik' => $namaRacikan,
                            'kd_racik' => $metodeRacikan,
                            'jml_dr' => $jumlahRacikan,
                            'aturan_pakai' => $aturanPakai,
                            'keterangan' => $keteranganRacikan,
                        ]);
                    if ($insert) {
                        return response()->json([
                            'status' => 'sukses',
                            'pesan' => 'Racikan berhasil ditambahkan'
                        ]);
                    }
                } else {
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Racikan gagal ditambahkan'
                    ]);
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function getCopyResep($noResep)
    {
        $data = DB::table('resep_dokter')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_dokter.no_resep', $noResep)
            ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai', 'resep_dokter.kode_brng')
            ->get();
        return response()->json($data);
    }

    public function postResumMedis($noRawat)
    {
        $keluhan = Request::get('keluhanUtama');
        $diagnosa = Request::get('diagnosaUtama');
        $terapi = Request::get('terapi');
        $prosedur = Request::get('prosedurUtama');
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($noRawat);

        try {
            $cek = DB::table('resume_pasien')->where('no_rawat', $noRawat)->count('no_rawat');
            if ($cek > 0) {
                $update = DB::table('resume_pasien')->where('no_rawat', $noRawat)->update([
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                ]);
                return response()->json(['status' => 'sukses', 'pesan' => 'Resume medis berhasil diperbarui']);
            } else {
                $insert = DB::table('resume_pasien')->insert([
                    'no_rawat' => $noRawat,
                    'kd_dokter' => $dokter,
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                ]);
                return response()->json(['status' => 'sukses', 'pesan' => 'Resume medis berhasil ditambahkan']);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    public function postCopyResep($noRawat)
    {
        $dokter = session()->get('username');
        $resObat = Request::get('obat');
        $resJml = Request::get('jumlah');
        $resAturan = Request::get('aturan_pakai');
        $no_rawat = $this->decryptData($noRawat);

        $resep = DB::table('resep_obat')->where('no_rawat', $no_rawat)->first();
        $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
        $maxNo = substr($no->resep, 0, 4);
        $nextNo = sprintf('%04s', ($maxNo + 1));
        $tgl = date('Ymd');
        $noResep = $tgl . '' . $nextNo;

        try {
            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0009')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'B0009')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0009')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < $jml) {
                    continue;
                    // $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    // return response()->json([
                    //     'status' => 'gagal',
                    //     'pesan' => 'Stok obat '.$dataBarang->nama_brng ?? $obat.' kosong'
                    // ]);
                }

                $cek = DB::table('resep_obat')->where('no_rawat', $no_rawat)->first();
                if ($cek) {
                    if (!empty($jml)) {
                        DB::table('resep_dokter')->insert([
                            'no_resep' => $resep->no_resep,
                            'kode_brng' => $obat,
                            'jml' => $jml,
                            'aturan_pakai' => $aturan,
                        ]);
                    }
                } else {
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => $tgl,
                        'jam' => date('H:i:s'),
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'Ralan',
                    ]);
                    if (!empty($jml)) {
                        DB::table('resep_dokter')->insert([
                            'no_resep' => $noResep,
                            'kode_brng' => $obat,
                            'jml' => $jml,
                            'aturan_pakai' => $aturan,
                        ]);
                    }
                }
            }
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResep($noRawat)
    {
        $dokter = session()->get('username');
        $resObat = Request::get('obat');
        $resJml = Request::get('jumlah');
        $resAturan = Request::get('aturan_pakai');
        $iter = Request::get('iter');
        
        try {
            // Dekripsi dan sanitasi no_rawat
            $originalNoRawat = $noRawat; // Simpan original untuk debugging jika diperlukan
            $noRawat = $this->decryptData($noRawat);
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika hasilnya kosong setelah pembersihan, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($noRawat)) {
                $cleanNoRawat = $noRawat;
            }
            
            // Verifikasi format no_rawat
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Format nomor rawat tidak valid: ' . $cleanNoRawat
                ]);
            }
            
            // Verifikasi no_rawat ada di database
            $cekNoRawat = DB::table('reg_periksa')
                ->where(DB::raw('BINARY no_rawat'), $cleanNoRawat)
                ->first();
                
            if (!$cekNoRawat) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'No Rawat tidak ditemukan di database'
                ]);
            }
            
            // Proses iter jika ada
            if ($iter != '-') {
                $insert = DB::table('resep_iter')->upsert([
                    'no_rawat' => $cleanNoRawat,
                    'catatan_iter' => $iter,
                ], ['no_rawat'], ['catatan_iter']);
            }

            // Looping untuk setiap obat
            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                // Cek stok
                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0009')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'B0009')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0009')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < 1) {
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Stok obat ' . $obat . ' kosong'
                    ]);
                }
                
                // Cek apakah sudah ada resep
                $resep = DB::table('resep_obat')
                    ->where(DB::raw('BINARY no_rawat'), $cleanNoRawat)
                    ->first();
                    
                // Generate nomor resep baru jika belum ada
                $no = DB::table('resep_obat')
                    ->where('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')
                    ->orWhere('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')
                    ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")
                    ->first();
                    
                $maxNo = substr($no->resep, 0, 4);
                $nextNo = sprintf('%04s', ($maxNo + 1));
                $tgl = date('Y-m-d'); // Format tanggal yang benar YYYY-MM-DD
                $tglNoResep = date('Ymd'); // Format untuk nomor resep
                $noResep = $tglNoResep . '' . $nextNo;

                if ($resep) {
                    // Jika sudah ada resep, tambahkan obat ke resep yang ada
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                } else {
                    // Jika belum ada resep, buat resep baru
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $cleanNoRawat, // Gunakan no_rawat yang sudah dibersihkan
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'Ralan',
                    ]);
                    
                    // Tambahkan obat ke resep
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                }
            }
            
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            // Catat detail error untuk debugging
            \Illuminate\Support\Facades\Log::error('Error saat menyimpan resep: ' . $ex->getMessage(), [
                'file' => __FILE__,
                'line' => __LINE__,
                'no_rawat' => $noRawat ?? 'tidak tersedia'
            ]);
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan saat menyimpan resep: ' . $ex->getMessage()
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error umum saat menyimpan resep: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public static function getResepObat($noResep)
    {
        $data = DB::table('resep_dokter')
            ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
            ->where('resep_dokter.no_resep', $noResep)
            ->select('databarang.nama_brng', 'resep_dokter.jml', 'resep_dokter.aturan_pakai')
            ->get();

        return $data;
    }

    public static function getObat()
    {
        $q = Request::get('q');
        $que = '%' . $q . '%';
        $obat = DB::table('databarang')
            ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->where('status', '1')
            ->where('gudangbarang.stok', '>', '0')
            ->where('gudangbarang.kd_bangsal', 'B0009')
            ->where(function ($query) use ($que) {
                $query->where('databarang.kode_brng', 'like', $que)
                    ->orWhere('databarang.nama_brng', 'like', $que);
            })
            ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
            ->get();
        return response()->json($obat, 200);
    }

    public static function getPemeriksaanRalan($noRawat, $status)
    {
        // Log parameter untuk membantu debug - dihapus untuk production
        // \Illuminate\Support\Facades\Log::info('getPemeriksaanRalan dipanggil dengan parameter', [
        //     'no_rawat' => $noRawat,
        //     'status' => $status
        // ]);
        
        // Sanitasi input untuk mencegah masalah encoding dan collation
        if (!is_string($noRawat)) {
            $noRawat = (string)$noRawat;
        }
        
        // Hapus karakter non-printable jika ada
        $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
        
        // Jika setelah dibersihkan kosong, gunakan nilai asli
        if (empty($cleanNoRawat) && !empty($noRawat)) {
            $cleanNoRawat = $noRawat;
        }
        
        // Log nilai yang sudah dibersihkan - dihapus untuk production
        // \Illuminate\Support\Facades\Log::info('Nilai no_rawat setelah dibersihkan', [
        //     'original' => $noRawat,
        //     'cleaned' => $cleanNoRawat
        // ]);
        
        try {
            if ($status == 'Ralan') {
                // Periksa dulu struktur tabel - dihapus untuk production
                // $tableColumns = Schema::getColumnListing('pemeriksaan_ralan');
                // \Illuminate\Support\Facades\Log::info('Kolom-kolom tersedia di tabel pemeriksaan_ralan', [
                //     'columns' => $tableColumns
                // ]);
                
                $data = DB::table('pemeriksaan_ralan')
                    ->leftJoin('pegawai', 'pemeriksaan_ralan.nip', '=', 'pegawai.nik')
                    ->where('pemeriksaan_ralan.no_rawat', $cleanNoRawat)
                    ->select(
                        'pemeriksaan_ralan.no_rawat',
                        'pemeriksaan_ralan.tgl_perawatan',
                        'pemeriksaan_ralan.jam_rawat',
                        'pemeriksaan_ralan.suhu_tubuh',
                        'pemeriksaan_ralan.tensi',
                        'pemeriksaan_ralan.nadi',
                        'pemeriksaan_ralan.respirasi',
                        'pemeriksaan_ralan.tinggi',
                        'pemeriksaan_ralan.berat',
                        'pemeriksaan_ralan.spo2',
                        'pemeriksaan_ralan.gcs',
                        'pemeriksaan_ralan.kesadaran',
                        'pemeriksaan_ralan.keluhan',
                        'pemeriksaan_ralan.pemeriksaan',
                        'pemeriksaan_ralan.alergi',
                        'pemeriksaan_ralan.lingkar_perut',
                        'pemeriksaan_ralan.rtl',
                        'pemeriksaan_ralan.penilaian',
                        'pemeriksaan_ralan.instruksi',
                        'pemeriksaan_ralan.evaluasi',
                        'pemeriksaan_ralan.nip',
                        'pegawai.nama'
                    )
                    ->orderBy('pemeriksaan_ralan.jam_rawat', 'desc')
                    ->get();
                    
                // Log hasil query - dihapus untuk production
                // \Illuminate\Support\Facades\Log::info('Hasil query pemeriksaan_ralan', [
                //     'found' => count($data),
                //     'sample' => count($data) > 0 ? $data[0] : null
                // ]);
            } else {
                // Periksa dulu struktur tabel - dihapus untuk production
                // $tableColumns = Schema::getColumnListing('pemeriksaan_ranap');
                // \Illuminate\Support\Facades\Log::info('Kolom-kolom tersedia di tabel pemeriksaan_ranap', [
                //     'columns' => $tableColumns
                // ]);
                
                $data = DB::table('pemeriksaan_ranap')
                    ->leftJoin('pegawai', 'pemeriksaan_ranap.nip', '=', 'pegawai.nik')
                    ->where('pemeriksaan_ranap.no_rawat', $cleanNoRawat)
                    ->select(
                        'pemeriksaan_ranap.*',
                        'pegawai.nama'
                    )
                    ->orderBy('pemeriksaan_ranap.tgl_perawatan', 'desc')
                    ->orderBy('pemeriksaan_ranap.jam_rawat', 'desc')
                    ->get();
                    
                // Log hasil query - dihapus untuk production
                // \Illuminate\Support\Facades\Log::info('Hasil query pemeriksaan_ranap', [
                //     'found' => count($data),
                //     'sample' => count($data) > 0 ? $data[0] : null
                // ]);
            }
            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada getPemeriksaanRalan: ' . $e->getMessage());
            return [];
        }
    }

    public static function getPemeriksaanLab($noRawat)
    {
        try {
            // Sanitasi input untuk mencegah masalah encoding dan collation
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable jika ada
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika setelah dibersihkan kosong, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($noRawat)) {
                $cleanNoRawat = $noRawat;
            }
            
            // Query ke detail_periksa_lab untuk mendapatkan data hasil lab
            $data = DB::table('detail_periksa_lab')
                ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
                ->where(DB::raw('BINARY detail_periksa_lab.no_rawat'), $cleanNoRawat)
                ->select(
                    'template_laboratorium.Pemeriksaan', 
                    'detail_periksa_lab.tgl_periksa', 
                    'detail_periksa_lab.jam', 
                    'detail_periksa_lab.nilai', 
                    'template_laboratorium.satuan', 
                    'detail_periksa_lab.nilai_rujukan', 
                    'detail_periksa_lab.keterangan'
                )
                ->orderBy('detail_periksa_lab.tgl_periksa', 'desc')
                ->get();
                
            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada getPemeriksaanLab: ' . $e->getMessage());
            return [];
        }
    }

    public static function getDiagnosa($noRawat)
    {
        try {
            // Sanitasi input untuk mencegah masalah encoding dan collation
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable jika ada
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika setelah dibersihkan kosong, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($noRawat)) {
                $cleanNoRawat = $noRawat;
            }
            
            $data = DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->where(DB::raw('BINARY diagnosa_pasien.no_rawat'), $cleanNoRawat)
                ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
                ->get();
            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada getDiagnosa: ' . $e->getMessage());
            return [];
        }
    }

    public static function getPemeriksaanObstetri($noRawat)
    {
        try {
            // Sanitasi input untuk mencegah masalah encoding dan collation
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable jika ada
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika setelah dibersihkan kosong, gunakan nilai asli
            if (empty($cleanNoRawat) && !empty($noRawat)) {
                $cleanNoRawat = $noRawat;
            }
            
            $data = DB::table('pemeriksaan_obstetri_ralan')
                ->where(DB::raw('BINARY no_rawat'), $cleanNoRawat)
                ->first();
            return $data;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error pada getPemeriksaanObstetri: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPemeriksaan(Request $request)
    {
        // return response()->json([
        //                 'status' => 'success',
        //                 'message' => Request::get('no_rawat')
        //             ], 200);
        // $validate = Request::validate([
        //     'tensi' => 'required',
        //     'kesadaran' => 'required',
        //     'rtl' => 'required',
        //     'penilaian' => 'required',
        //     'instruksi' => 'required',
        // ]);
        // $cek = DB::table('pemeriksaan_ralan')
        //     ->where('no_rawat', Request::get('no_rawat'))
        //     ->count();
        $data = [
            'no_rawat' => Request::get('no_rawat'),
            'nip' => session()->get('username'),
            'tgl_perawatan' => date('Y-m-d'),
            'jam_rawat' => date('H:i:s'),
            'suhu_tubuh' => Request::get('suhu'),
            'tensi' => Request::get('tensi'),
            'nadi' => Request::get('nadi'),
            'respirasi' => Request::get('respirasi'),
            'tinggi' => Request::get('tinggi'),
            'berat' => Request::get('berat'),
            'gcs' => Request::get('gcs'),
            'kesadaran' => Request::get('kesadaran'),
            'keluhan' => Request::get('keluhan'),
            'pemeriksaan' => Request::get('pemeriksaan'),
            'alergi' => Request::get('alergi'),
            'imun_ke' => Request::get('imun'),
            'rtl' => Request::get('rtl'),
            'penilaian' => Request::get('penilaian'),
            'instruksi' => Request::get('instruksi'),
        ];
        $insert = DB::table('pemeriksaan_ralan')
            ->insert($data);

        if ($insert) {
            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan'
            ], 200);
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Data gagal disimpan'
        ], 500);
    }

    // public function decryptData($data)
    // {
    //     $data = Crypt::decrypt($data);
    //     return $data;
    // }

    public function postCatatan()
    {
        $validate = Request::validate([
            'catatan' => 'required',
        ]);
        try {
            $cek = DB::table('catatan_perawatan')
                ->where('no_rawat', Request::get('no_rawat'))
                ->count();
            $data = [
                'no_rawat' => Request::get('no_rawat'),
                'kd_dokter' => session()->get('username'),
                'tanggal' => date('Y-m-d'),
                'jam' => date('H:i:s'),
                'catatan' => Request::get('catatan'),
            ];
            if ($cek > 0) {
                $insert = DB::table('catatan_perawatan')
                    ->where('no_rawat', Request::get('no_rawat'))
                    ->update($data);
                if ($insert) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan'
                    ], 200);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan'
                ], 500);
            } else {
                $insert = DB::table('catatan_perawatan')
                    ->insert($data);

                if ($insert) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan'
                    ], 200);
                }
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public static function getResume($noRM)
    {
        return DB::table('resume_pasien')
            ->where('no_rawat', $noRM)
            ->first();
    }

    public static function getRadiologi($noRM)
    {
        return DB::table('hasil_radiologi')
            ->where('no_rawat', $noRM)
            ->get();
    }

    public static function getFotoRadiologi($noRM)
    {
        return DB::table('gambar_radiologi')
            ->where('no_rawat', $noRM)
            ->get();
    }

    public function getBerkasRM($noRawat, $noRM)
    {
        try {

            $data = DB::table('berkas_digital_perawatan')
                ->whereRaw(
                    "no_rawat IN (SELECT no_rawat FROM reg_periksa WHERE no_rkm_medis = :noRM) AND lokasi_file <> :file AND (kode = :kode OR kode = :lab)",
                    ['noRM' => $noRM, 'file' => 'pages/upload/', 'kode' => 'B00', 'lab' => 'B05']
                )
                ->orderBy('no_rawat', 'desc')
                ->get();
            if ($data->count() > 0) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getBerkasRetensi($noRawat)
    {
        try {

            $data = DB::table('retensi_pasien')
                ->where('no_rkm_medis', $noRawat)
                ->first();
            if (!empty($data)) {
                return response()->json([
                    'status' => 'success',
                    'data' => $data
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public static function getPoli()
    {
        $q = Request::get('q');
        $que = '%' . $q . '%';
        $obat = DB::table('poliklinik')
            ->where('status', '1')
            ->where('nm_poli', 'like', $que)
            ->selectRaw('kd_poli AS id, nm_poli AS text')
            ->get();
        return response()->json($obat, 200);
    }

    public static function getDokter($kdPoli)
    {
        $data = DB::table('jadwal')
            ->join('dokter', 'dokter.kd_dokter', '=', 'jadwal.kd_dokter')
            ->where('jadwal.kd_poli', $kdPoli)
            ->groupBy('jadwal.kd_dokter')
            ->selectRaw('jadwal.kd_dokter, dokter.nm_dokter')
            ->get();
        return response()->json($data, 200);
    }

    public static function postRujukan(Request $request)
    {
        $validate = Request::validate([
            'no_rawat' => 'required',
            'kd_poli' => 'required',
            'kd_dokter' => 'required',
            'catatan' => 'required',
        ], [
            'no_rawat.required' => 'No Rawat tidak boleh kosong',
            'kd_poli.required' => 'Poli tujuan tidak boleh kosong',
            'kd_dokter.required' => 'Dokter tujuan tidak boleh kosong',
            'catatan.required' => 'Catatan tidak boleh kosong',
        ]);
        try {
            $data = [
                'no_rawat' => Request::get('no_rawat'),
                'kd_poli' => Request::get('kd_poli'),
                'kd_dokter' => Request::get('kd_dokter'),
            ];
            $insert = DB::table('rujukan_internal_poli')
                ->insert($data);
            if ($insert) {
                $insert = DB::table('rujukan_internal_poli_detail')
                    ->insert([
                        'no_rawat' => Request::get('no_rawat'),
                        'konsul' => Request::get('catatan'),
                    ]);
                if ($insert) {
                    return response()->json([
                        'status' => 'success',
                        'message' => 'Data berhasil disimpan'
                    ], 200);
                } else {
                    $delete = DB::table('rujuk_internal_poli')
                        ->where('no_rawat', Request::get('no_rawat'))
                        ->delete();
                    if ($delete) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Data gagal disimpan'
                        ], 500);
                    } else {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Gagal menghapus data'
                        ], 500);
                    }
                }
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal disimpan'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteRujukan($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);
        try {
            $delete = DB::table('rujukan_internal_poli')
                ->where('no_rawat', $noRawat)
                ->delete();
            if ($delete) {
                $delete = DB::table('rujukan_internal_poli_detail')
                    ->where('no_rawat', $noRawat)
                    ->delete();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ], 200);
            }
            return response()->json([
                'status' => 'error',
                'message' => 'Data gagal dihapus'
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateRujukanInternal($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);
        try {
            $data = [
                'pemeriksaan' => Request::get('pemeriksaan'),
                'diagnosa' => Request::get('diagnosa'),
                'saran' => Request::get('saran'),
            ];
            $update = DB::table('rujukan_internal_poli_detail')
                ->where('no_rawat', $noRawat)
                ->update($data);
            if ($update) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan'
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data gagal disimpan',
                    'no_rawat' => $noRawat,
                ], 200);
            }
            // }
            // return response()->json([
            //     'status' => 'error',
            //     'message' => 'Data gagal disimpan'
            // ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //  public static function getTono($noRawat)
    //  {
    //      return DB::table('pemeriksaan_tono')->where('no_rawat', $noRawat)->first();
    //  }
}
