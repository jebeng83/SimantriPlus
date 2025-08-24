<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Carbon;
use App\Http\Livewire\ILP\Pendaftaran;
use App\Exports\PasienExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PasienController extends Controller
{
    public function index()
    {
        // Mengambil data dari database untuk menghindari kueri langsung di tampilan
        $totalPasien = DB::table('pasien')->count();
        $pasienBaru = DB::table('pasien')->orderBy('no_rkm_medis', 'desc')->limit(10)->count();
        $kunjunganHariIni = DB::table('reg_periksa')->whereDate('tgl_registrasi', date('Y-m-d'))->count();
        $pasienBPJS = DB::table('pasien')->where('kd_pj', 'BPJ')->count();

        return view('pasien.index', [
            'totalPasien' => $totalPasien,
            'pasienBaru' => $pasienBaru,
            'kunjunganHariIni' => $kunjunganHariIni,
            'pasienBPJS' => $pasienBPJS,
            'kdProviderPeserta' => env('BPJS_PCARE_KODE_PPK', '11251919')
        ]);
    }

    public function edit($no_rkm_medis)
    {
        // Memastikan query langsung ke database tanpa cache
        DB::connection()->disableQueryLog();
        $pasien = DB::table('pasien')
                  ->where('no_rkm_medis', $no_rkm_medis)
                  ->useWritePdo() // Menggunakan koneksi write untuk memastikan data terbaru
                  ->first();
        
        // Ambil data posyandu langsung dari database
        $posyandu = DB::table('data_posyandu')
                    ->useWritePdo()
                    ->get();
        
        // Ambil data penjab langsung dari database
        $penjab = DB::table('penjab')
                  ->useWritePdo()
                  ->get();

        return view('pasien.edit', ['pasien' => $pasien, 'posyandu' => $posyandu, 'penjab' => $penjab]);
    }

    public function searchPasien(Request $request)
    {
        // Ambil parameter pencarian dari request
        $search = $request->get('term') ?? $request->get('q');
        
        // Debug log untuk melihat query yang diterima
        Log::info('PasienController - searchPasien: Query menerima: ' . $search);
        
        // Gunakan metode searchPasien dari Livewire\ILP\Pendaftaran
        $results = Pendaftaran::searchPasien($search);
        
        // Debug jumlah hasil yang ditemukan
        Log::info('PasienController - searchPasien: Jumlah hasil ditemukan: ' . count($results));
        
        // Jika request menggunakan parameter 'term' (untuk modal detail)
        if ($request->has('term')) {
            return response()->json($results);
        }
        
        // Format untuk Select2 jika menggunakan parameter 'q'
        $formattedResults = [];
        foreach ($results as $pasien) {
            // Format nomor KTP (tampilkan 4 digit pertama dan 1 digit terakhir)
            $maskedKtp = '-';
            if (!empty($pasien->no_ktp)) {
                $ktpLength = strlen($pasien->no_ktp);
                if ($ktpLength > 5) {
                    $firstFour = substr($pasien->no_ktp, 0, 4);
                    $lastOne = substr($pasien->no_ktp, -1);
                    $masked = str_repeat('x', $ktpLength - 5);
                    $maskedKtp = $firstFour . $masked . $lastOne;
                } else {
                    $maskedKtp = $pasien->no_ktp;
                }
            }
            
            // Format data untuk Select2
            $formattedResults[] = [
                'id' => $pasien->no_rkm_medis,
                'text' => $pasien->no_rkm_medis . ' - ' . $pasien->nm_pasien,
                'no_rkm_medis' => $pasien->no_rkm_medis,
                'nm_pasien' => $pasien->nm_pasien,
                'alamat' => $pasien->alamat,
                'tgl_lahir' => $pasien->tgl_lahir,
                'namakeluarga' => $pasien->namakeluarga,
                'keluarga' => $pasien->keluarga,
                'alamatpj' => $pasien->alamatpj,
                'kd_pj' => $pasien->kd_pj,
                'no_ktp' => $pasien->no_ktp,
                'masked_ktp' => $maskedKtp,
                'kelurahanpj' => $pasien->kelurahanpj,
            ];
        }
        
        // Tambahkan contoh hasil pertama untuk debugging
        if (count($formattedResults) > 0) {
            Log::info('PasienController - searchPasien: Contoh hasil pertama: ', ['item' => $formattedResults[0]]);
        }
        
        $response = [
            'items' => $formattedResults,
            'total_count' => count($formattedResults)
        ];
        
        return response()->json($response);
    }

    public function update(Request $request, $no_rkm_medis)
    {
        // Log request untuk debugging
        Log::info('PasienController - update: Menerima permintaan update', [
            'no_rkm_medis' => $no_rkm_medis,
            'method' => $request->method()
        ]);
        
        // Panggil metode simpan untuk menangani pembaruan data
        return $this->simpan($request, $no_rkm_medis);
    }

    public function simpan(Request $request, $no_rkm_medis)
    {
        // dd($request->all());
        $this->validate($request, [
            'nm_pasien' => 'required',
            'no_ktp' => 'required',
            'no_peserta' => 'required',
            'no_tlp' => 'required',
            'tgl_lahir' => 'required',
            'alamat' => 'required',
            'stts_nikah' => 'required',
            'status' => 'required',
            'data_posyandu' => 'required',
            'no_kk' => 'required',
            'pekerjaan' => 'nullable',
            'kd_pj' => 'required|exists:penjab,kd_pj',
        ], [
            'nm_pasien.required' => 'Nama Pasien tidak boleh kosong',
            'no_ktp.required' => 'No. KTP/SIM tidak boleh kosong',
            'no_peserta.required' => 'No. Peserta tidak boleh kosong',
            'no_tlp.required' => 'No. Telepon tidak boleh kosong',
            'tgl_lahir.required' => 'Tanggal Lahir tidak boleh kosong',
            'alamat.required' => 'Alamat tidak boleh kosong',
            'stts_nikah.required' => 'Status Nikah tidak boleh kosong',
            'status.required' => 'Status tidak boleh kosong',
            'data_posyandu.required' => 'Posyandu tidak boleh kosong',
            'no_kk.required' => 'No. KK tidak boleh kosong',
            'kd_pj.required' => 'Penjab tidak boleh kosong',
            'kd_pj.exists' => 'Penjab yang dipilih tidak valid',
        ]);

        try {
            // Log data sebelum update
            Log::info('PasienController - simpan: Memperbarui data pasien', [
                'no_rkm_medis' => $no_rkm_medis ?? 'unknown',
                'data' => $request->except('_token', '_method')
            ]);
            
            // Hitung umur dengan penanganan error
            $umur_calculated = $this->rubahUmur($request->tgl_lahir);
            
            // Format tanggal lahir untuk database (date type)
            $tgl_lahir_formatted = Carbon::parse($request->tgl_lahir)->format('Y-m-d');
            
            // Gunakan parameter binding yang eksplisit untuk menghindari konflik prepared statement
            $affected = DB::table('pasien')
                ->where('no_rkm_medis', '=', $no_rkm_medis)
                ->update([
                    'nm_pasien' => $request->nm_pasien,
                    'no_ktp' => $request->no_ktp,
                    'no_peserta' => $request->no_peserta,
                    'no_tlp' => $request->no_tlp,
                    'tgl_lahir' => $tgl_lahir_formatted,
                    'umur' => $umur_calculated,
                    'alamat' => $request->alamat,
                    'stts_nikah' => $request->stts_nikah,
                    'status' => $request->status,
                    'data_posyandu' => $request->data_posyandu,
                    'no_kk' => $request->no_kk,
                    'pekerjaan' => $request->pekerjaan,
                    'kd_pj' => $request->kd_pj,
                ]);
                
            if ($affected === 0) {
                Log::warning('No rows affected when updating pasien data for no_rkm_medis: ' . $no_rkm_medis);
            }

            // Log sukses update
            $noRkmMedisLog = $no_rkm_medis !== null ? (string)$no_rkm_medis : 'unknown';
            Log::info('PasienController - simpan: Berhasil memperbarui data pasien', [
                'no_rkm_medis' => $noRkmMedisLog
            ]);

            return Redirect::to('/data-pasien')->with('success', 'Data Pasien berhasil diperbarui');
        } catch (\Exception $e) {
            // Log error dengan type safety
            $errorMessage = $e->getMessage();
            $noRkmMedis = $no_rkm_medis !== null ? (string)$no_rkm_medis : 'unknown';
            Log::error('PasienController - simpan: Gagal memperbarui data pasien', [
                'no_rkm_medis' => $noRkmMedis,
                'error' => $errorMessage
            ]);
            
            return Redirect::to('/data-pasien')->with('error', 'Data Pasien gagal diperbarui: ' . $errorMessage);
        }
    }

    public function rubahUmur($tgl_lahir)
    {
        try {
            if (empty($tgl_lahir)) {
                return '0 Th 0 Bl 0 Hr';
            }
            
            // Parse tanggal lahir dengan format yang benar
            $tgl_lahir_parsed = Carbon::parse($tgl_lahir);
            $umur_result = $tgl_lahir_parsed->diff(Carbon::now())->format('%y Th %m Bl %d Hr');
            
            return $umur_result;
        } catch (\Exception $e) {
            // Error handling dengan type safety
            $errorMessage = $e->getMessage();
            $tglLahirValue = isset($tgl_lahir) ? (string)$tgl_lahir : 'null';
            Log::error('Error calculating umur: ' . $errorMessage, [
                'tgl_lahir' => $tglLahirValue,
                'error_detail' => $errorMessage
            ]);
            return '0 Th 0 Bl 0 Hr';
        }
    }

    /**
     * Mendapatkan detail pasien untuk PCare BPJS
     * Digunakan oleh form PCare BPJS
     */
    public function getDetailByRekamMedis($no_rkm_medis, Request $request)
    {
        try {
            // Bersihkan input untuk keamanan
            $no_rkm_medis = trim($no_rkm_medis);
            
            // Catat parameter lain yang mungkin dikirim
            $timestamp = $request->input('ts', '');
            $clearCache = $request->input('clear_cache', 'false');
            $userAgent = $request->header('User-Agent', 'Unknown');
            
            // Log lengkap untuk debugging
            Log::info('PasienController - getDetailByRekamMedis: Request diterima', [
                'no_rkm_medis' => $no_rkm_medis,
                'timestamp' => $timestamp,
                'clear_cache' => $clearCache,
                'ip' => $request->ip(),
                'user_agent' => $userAgent,
                'referrer' => $request->header('Referer', 'Unknown'),
                'request_id' => uniqid('pasien_req_')
            ]);
            
            // Cek apakah ada pasien yang registrasi hari ini
            $today = date('Y-m-d');
            
            // Query pasien yang registrasi hari ini dulu untuk data terbaru
            $registrasiHariIni = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
                ->where('reg_periksa.tgl_registrasi', $today)
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.no_peserta', // Nomor BPJS dari tabel pasien
                    'reg_periksa.kd_pj',
                    'penjab.png_jawab',
                    'pasien.no_tlp',
                    'pasien.alamat'
                )
                ->orderBy('reg_periksa.jam_reg', 'desc')
                ->first();
                
            // Jika ada registrasi hari ini, gunakan data tersebut
            if ($registrasiHariIni) {
                Log::info('PasienController - getDetailByRekamMedis: Ditemukan registrasi hari ini', [
                    'no_rkm_medis' => $no_rkm_medis,
                    'no_rawat' => $registrasiHariIni->no_rawat,
                    'kd_pj' => $registrasiHariIni->kd_pj,
                    'png_jawab' => $registrasiHariIni->png_jawab,
                    'timestamp' => now()->format('Y-m-d H:i:s.u')
                ]);
                
                $pasien = $registrasiHariIni;
            } else {
                // Jika tidak ada registrasi hari ini, cek registrasi terakhir
                $registrasiTerakhir = DB::table('reg_periksa')
                    ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                    ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
                    ->where('reg_periksa.no_rkm_medis', $no_rkm_medis)
                    ->select(
                        'reg_periksa.no_rawat',
                        'reg_periksa.no_rkm_medis',
                        'pasien.nm_pasien',
                        'pasien.no_ktp',
                        'pasien.jk',
                        'pasien.tmp_lahir',
                        'pasien.tgl_lahir',
                        'pasien.no_peserta', // Nomor BPJS dari tabel pasien
                        'reg_periksa.kd_pj',
                        'penjab.png_jawab',
                        'pasien.no_tlp',
                        'pasien.alamat',
                        'reg_periksa.tgl_registrasi'
                    )
                    ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                    ->orderBy('reg_periksa.jam_reg', 'desc')
                    ->first();
                
                // Jika ada registrasi terakhir, gunakan data tersebut
                if ($registrasiTerakhir) {
                    Log::info('PasienController - getDetailByRekamMedis: Ditemukan registrasi terakhir', [
                        'no_rkm_medis' => $no_rkm_medis,
                        'no_rawat' => $registrasiTerakhir->no_rawat,
                        'tgl_registrasi' => $registrasiTerakhir->tgl_registrasi,
                        'kd_pj' => $registrasiTerakhir->kd_pj,
                        'png_jawab' => $registrasiTerakhir->png_jawab
                    ]);
                    
                    $pasien = $registrasiTerakhir;
                } else {
                    // Jika tidak ada registrasi sama sekali, ambil dari data pasien saja
                    Log::info('PasienController - getDetailByRekamMedis: Tidak ditemukan registrasi, menggunakan data master pasien');
                    
                    $pasien = DB::table('pasien')
                        ->select(
                            DB::raw("'' as no_rawat"),
                            'no_rkm_medis',
                            'nm_pasien',
                            'no_ktp',
                            'jk',
                            'tmp_lahir',
                            'tgl_lahir',
                            'no_peserta',
                            DB::raw("'' as kd_pj"),
                            DB::raw("'' as png_jawab"),
                            'no_tlp',
                            'alamat'
                        )
                        ->where('no_rkm_medis', $no_rkm_medis)
                        ->first();
                }
            }
            
            if (!$pasien) {
                Log::warning('PasienController - getDetailByRekamMedis: Pasien tidak ditemukan dengan no_rkm_medis: ' . ($no_rkm_medis ?? 'unknown'));
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien tidak ditemukan',
                    'data' => null
                ], 404);
            }
            
            // Verifikasi ulang nomor rekam medis untuk memastikan kecocokan data
            if ($pasien->no_rkm_medis !== $no_rkm_medis) {
                Log::error('PasienController - getDetailByRekamMedis: Ketidakcocokan data', [
                    'requested_rm' => $no_rkm_medis ?? 'unknown',
                    'returned_rm' => $pasien->no_rkm_medis,
                    'timestamp' => now()->format('Y-m-d H:i:s.u')
                ]);
                
                return response()->json([
                    'status' => 'error',
                    'message' => 'Ketidakcocokan data pasien terdeteksi',
                    'data' => null
                ], 409);
            }
            
            // Hitung umur
            $tglLahir = Carbon::parse($pasien->tgl_lahir);
            $now = Carbon::now();
            $umurTahun = $tglLahir->diffInYears($now);
            $umurBulan = $tglLahir->copy()->addYears($umurTahun)->diffInMonths($now);
            $umurHari = $tglLahir->copy()->addYears($umurTahun)->addMonths($umurBulan)->diffInDays($now);
            
            // Tambahkan umur ke objek pasien
            $pasien->umur = "$umurTahun tahun, $umurBulan bulan, $umurHari hari";
            
            Log::info('PasienController - getDetailByRekamMedis: Pasien ditemukan', [
                'no_rkm_medis' => $pasien->no_rkm_medis,
                'nama' => $pasien->nm_pasien,
                'no_peserta' => $pasien->no_peserta,
                'jenis_peserta' => $pasien->png_jawab ?? 'Tidak ada',
                'timestamp' => now()->format('Y-m-d H:i:s.u'),
                'response_id' => uniqid('pasien_res_')
            ]);
            
            // Set header anti-cache yang lebih kuat untuk memastikan data selalu fresh
            return response()->json([
                'status' => 'success',
                'message' => 'Data pasien ditemukan',
                'data' => $pasien,
                'timestamp' => now()->timestamp, // Tambahkan timestamp di respons
                'request_rm' => $no_rkm_medis ?? 'unknown' // Echo back requested RM untuk verifikasi client
            ])
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, max-age=0, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0')
            ->header('X-Content-Type-Options', 'nosniff')
            ->header('X-Response-Time', now()->format('Y-m-d H:i:s.u'))
            ->header('X-Patient-RM', $no_rkm_medis ?? 'unknown');
            
        } catch (\Exception $e) {
            Log::error('PasienController - getDetailByRekamMedis: Error', [
                'no_rkm_medis' => $no_rkm_medis ?? 'unknown',
                'error' => $e ? $e->getMessage() : 'Unknown error',
                'file' => $e ? $e->getFile() : 'Unknown file',
                'line' => $e ? $e->getLine() : 0,
                'trace' => $e ? $e->getTraceAsString() : 'No trace available'
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan: ' . ($e ? $e->getMessage() : 'Unknown error'),
                'data' => null
            ], 500);
        }
    }

    public function export(Request $request)
    {
        $search = [
            'name' => $request->input('name'),
            'rm' => $request->input('rm'),
            'address' => $request->input('address')
        ];
        
        return Excel::download(new PasienExport($search), 'data-pasien-' . date('Y-m-d') . '.xlsx');
    }
    
    public function cetak(Request $request)
    {
        $search = [
            'name' => $request->input('name'),
            'rm' => $request->input('rm'),
            'address' => $request->input('address')
        ];
        
        $query = DB::table('pasien');
        
        if (!empty($search['name'])) {
            $query->where('nm_pasien', 'like', '%' . $search['name'] . '%');
        }
        
        if (!empty($search['rm'])) {
            $query->where('no_rkm_medis', 'like', '%' . $search['rm'] . '%');
        }
        
        if (!empty($search['address'])) {
            $query->where('alamat', 'like', '%' . $search['address'] . '%');
        }
        
        // Batasi jumlah data yang diambil untuk menghindari memory exhausted
        $pasien = $query->orderBy('tgl_daftar', 'desc')->limit(100)->get();
        
        // Hanya ambil kolom yang diperlukan untuk mengurangi penggunaan memori
        $pasienData = $pasien->map(function($item) {
            return [
                'no_rkm_medis' => $item->no_rkm_medis,
                'nm_pasien' => $item->nm_pasien,
                'no_ktp' => $item->no_ktp,
                'tgl_lahir' => $item->tgl_lahir,
                'alamat' => $item->alamat,
                'status' => $item->status
            ];
        });
        
        // Tingkatkan batas memori untuk proses PDF
        ini_set('memory_limit', '512M');
        
        $pdf = PDF::loadView('pasien.cetak', [
            'pasien' => $pasienData,
            'tanggal' => date('d-m-Y'),
            'filter' => $search,
            'user' => auth()->user() ?? (object)['name' => 'Admin'] // Memberikan fallback jika user tidak ada
        ]);
        
        // Mengatur ukuran kertas dan orientasi
        $pdf->setPaper('a4', 'landscape');
        
        // Opsi untuk mengoptimalkan PDF
        $pdf->setOption('isPhpEnabled', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        
        return $pdf->stream('data-pasien-' . date('Y-m-d') . '.pdf');
    }
    
    /**
     * Menampilkan detail pasien berdasarkan nomor rekam medis
     * 
     * @param string $no_rkm_medis
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($no_rkm_medis)
    {
        try {
            // Log request untuk debugging
            Log::info('PasienController - show: Mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis ?? 'unknown'
            ]);
            
            // Memastikan query langsung ke database tanpa cache
            DB::connection()->disableQueryLog();
            $pasien = DB::table('pasien')
                     ->where('no_rkm_medis', $no_rkm_medis)
                     ->useWritePdo() // Menggunakan koneksi write untuk memastikan data terbaru
                     ->first();
            
            if (!$pasien) {
                // Log jika pasien tidak ditemukan
                Log::warning('PasienController - show: Pasien tidak ditemukan', [
                    'no_rkm_medis' => $no_rkm_medis ?? 'unknown'
                ]);
                
                return response()->json([
                    'error' => true,
                    'message' => 'Data pasien tidak ditemukan'
                ], 404);
            }
            
            // Log sukses
            Log::info('PasienController - show: Berhasil mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis ?? 'unknown'
            ]);
            
            // Menambahkan header no-cache
            return response()->json($pasien)
                   ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                   ->header('Pragma', 'no-cache')
                   ->header('Expires', '0');
        } catch (\Exception $e) {
            // Log error
            Log::error('PasienController - show: Gagal mengambil detail pasien', [
                'no_rkm_medis' => $no_rkm_medis ?? 'unknown',
                'error' => $e ? $e->getMessage() : 'Unknown error'
            ]);
            
            return response()->json([
                'error' => true,
                'message' => 'Gagal mengambil data pasien: ' . ($e ? $e->getMessage() : 'Unknown error')
            ], 500);
        }
    }
}
