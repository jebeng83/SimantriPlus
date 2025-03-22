<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;
use Illuminate\Support\Facades\Log;

class ResepController extends Controller
{
    use EnkripsiData;

    // public function getObatRanap(Request $request, $bangsal)
    // {
    //     try {
    //         $q = $request->get('q');
    //         $que = '%' . $q . '%';
            
    //         // Pastikan bangsal tidak null
    //         if (empty($bangsal)) {
    //             Log::error("kd_bangsal kosong saat getObatRanap");
    //             return response()->json([], 200);
    //         }
            
    //         // Ambil tanggal terakhir di riwayat_barang_medis untuk bangsal ini
    //         $maxTgl = DB::table('riwayat_barang_medis')
    //             ->where('kd_bangsal', $bangsal)
    //             ->max('tanggal');
                
    //         if (!$maxTgl) {
    //             Log::warning("Tidak ada data riwayat_barang_medis untuk bangsal {$bangsal}");
    //             return response()->json([], 200);
    //         }
            
    //         // Query menggunakan riwayat_barang_medis
    //         $obat = DB::table('databarang')
    //             ->join('riwayat_barang_medis', function($join) use ($bangsal, $maxTgl) {
    //                 $join->on('databarang.kode_brng', '=', 'riwayat_barang_medis.kode_brng')
    //                     ->where('riwayat_barang_medis.kd_bangsal', '=', $bangsal)
    //                     ->where('riwayat_barang_medis.tanggal', '=', $maxTgl)
    //                     ->whereRaw('riwayat_barang_medis.jam = (SELECT MAX(jam) FROM riwayat_barang_medis WHERE tanggal = ? AND kd_bangsal = ? AND kode_brng = databarang.kode_brng)', [$maxTgl, $bangsal]);
    //             })
    //             ->where('databarang.status', '1')
    //             ->where('riwayat_barang_medis.stok_akhir', '>', '0')
    //             ->where(function ($query) use ($que) {
    //                 $query->where('databarang.kode_brng', 'like', $que)
    //                     ->orWhere('databarang.nama_brng', 'like', $que);
    //             })
    //             ->selectRaw('databarang.kode_brng AS id, databarang.nama_brng AS text, riwayat_barang_medis.stok_akhir as stok')
    //             ->get();
            
    //         Log::info("getObatRanap: Ditemukan " . count($obat) . " obat di bangsal " . $bangsal);
            
    //         return response()->json($obat, 200);
    //     } catch (\Exception $e) {
    //         Log::error("Error di getObatRanap: " . $e->getMessage());
    //         return response()->json([], 500);
    //     }
    // }

    public function getObatRalan(Request $request, $poli)
    {
        try {
            $q = $request->get('q');
            $que = '%' . $q . '%';

            // Log debug info
            Log::info("getObatRalan dipanggil dengan parameter poli: {$poli}, query: {$q}");
            
            // Berdasarkan kode Java, nilai default
            $STOKKOSONGRESEP = "no"; // Nilai default dalam Java
            $aktifkanBatch = "no"; // Nilai default dalam Java
            
            // Temukan depo/bangsal berdasarkan poli
            $depo = DB::table('set_depo_ralan')
                ->where('kd_poli', $poli)
                ->first();

            // Jika data depo tidak ditemukan, handle dengan fallback ke depo default atau berikan respons kosong
            if (!$depo) {
                Log::warning("Depo tidak ditemukan untuk poli: {$poli}, menggunakan fallback");
                // Cari depo pertama yang tersedia atau gunakan kode default
                $defaultDepo = DB::table('set_depo_ralan')->first();
                
                if ($defaultDepo) {
                    $kd_bangsal = $defaultDepo->kd_bangsal;
                    Log::info("Menggunakan depo fallback dengan kd_bangsal: {$kd_bangsal}");
                } else {
                    // Gunakan salah satu kode bangsal dari tabel yang sudah diketahui
                    $kd_bangsal = 'B0009'; // Alternatif: B0007, B0010, B0013, B0014, B0016
                    Log::info("Tidak ada depo yang ditemukan, menggunakan kode bangsal default: {$kd_bangsal}");
                }
            } else {
                $kd_bangsal = $depo->kd_bangsal;
                Log::info("Depo ditemukan dengan kd_bangsal: {$kd_bangsal}");
            }
            
            // Set filter stok
            $qrystokkosong = "";
            // Jika STOKKOSONGRESEP = no, maka hanya tampilkan stok > 0
            if($STOKKOSONGRESEP == "no") {
                $qrystokkosong = " and gudangbarang.stok > 0 ";
                Log::info("Filter STOKKOSONGRESEP aktif: hanya tampilkan stok > 0");
            }
            
            Log::info("Nilai AKTIFKANBATCH: " . $aktifkanBatch);
            
            if($aktifkanBatch == "yes") {
                // Query dengan batch
                Log::info("Menggunakan query dengan batch (no_batch dan no_faktur tidak kosong)");
                $obat = DB::table('databarang')
                    ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                    ->join('industrifarmasi', 'industrifarmasi.kode_industri', '=', 'databarang.kode_industri')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('databarang.status', '=', '1')
                    ->where('gudangbarang.kd_bangsal', '=', $kd_bangsal)
                    ->where('gudangbarang.no_batch', '<>', '')
                    ->where('gudangbarang.no_faktur', '<>', '')
                    ->whereRaw("1=1 {$qrystokkosong}")
                    ->where(function ($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                            ->orWhere('databarang.nama_brng', 'like', $que)
                            ->orWhere('jenis.nama', 'like', $que)
                            ->orWhere('databarang.letak_barang', 'like', $que);
                    })
                    ->select(
                        'databarang.kode_brng as id',
                        'databarang.nama_brng as text',
                        DB::raw('sum(gudangbarang.stok) as stok')
                    )
                    ->groupBy('gudangbarang.kode_brng')
                    ->orderBy('databarang.nama_brng')
                    ->limit(20) // Batasi hasil untuk performa
                    ->get();
            } else {
                // Query tanpa batch (nilai default dalam Java adalah "no")
                Log::info("Menggunakan query tanpa batch (no_batch dan no_faktur kosong)");
                $obat = DB::table('databarang')
                    ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                    ->join('industrifarmasi', 'industrifarmasi.kode_industri', '=', 'databarang.kode_industri')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('databarang.status', '=', '1')
                    ->where('gudangbarang.kd_bangsal', '=', $kd_bangsal)
                    // Hapus kondisi batch dan faktur yang ketat
                    // ->where('gudangbarang.no_batch', '=', '')
                    // ->where('gudangbarang.no_faktur', '=', '')
                    ->whereRaw("1=1 {$qrystokkosong}")
                    ->where(function ($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                            ->orWhere('databarang.nama_brng', 'like', $que)
                            ->orWhere('jenis.nama', 'like', $que)
                            ->orWhere('databarang.letak_barang', 'like', $que);
                    })
                    ->select(
                        'databarang.kode_brng as id',
                        'databarang.nama_brng as text',
                        DB::raw('SUM(gudangbarang.stok) as stok')
                    )
                    ->groupBy('databarang.kode_brng', 'databarang.nama_brng')
                    ->orderBy('databarang.nama_brng')
                    ->limit(100) // Meningkatkan batas hasil
                    ->get();
            }
            
            Log::info("Berhasil mengambil " . count($obat) . " obat");
            return response()->json($obat, 200);
        } catch (\Exception $e) {
            Log::error("Error di getObatRalan: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    public function getObatLuar(Request $request)
    {
        try {
            // Dapatkan parameter
            $q = $request->get('q');
            $que = '%' . $q . '%';
            
            // Log debug info
            Log::info("getObatLuar dipanggil dengan query: {$q}");
            
            // Berdasarkan kode Java, nilai default
            $STOKKOSONGRESEP = "no"; // Nilai default dalam Java
            $aktifkanBatch = "no"; // Nilai default dalam Java
            
            // Dapatkan kode bangsal untuk apotek/farmasi
            $kd_bangsal = DB::table('set_depo_ralan')
                ->select('kd_bangsal')
                ->first();
                
            if (!$kd_bangsal) {
                // Menggunakan konstanta default jika tidak ada setting
                $kd_bangsal = 'B0009'; // Ubah nilai default menjadi kode yang ada di tabel
                Log::warning("Depo obat luar tidak ditemukan, menggunakan default: {$kd_bangsal}");
            } else {
                $kd_bangsal = $kd_bangsal->kd_bangsal;
                Log::info("Depo obat luar ditemukan: {$kd_bangsal}");
            }
            
            // Set filter stok
            $qrystokkosong = "";
            // Jika STOKKOSONGRESEP = no, maka hanya tampilkan stok > 0
            if($STOKKOSONGRESEP == "no") {
                $qrystokkosong = " and gudangbarang.stok > 0 ";
                Log::info("Filter STOKKOSONGRESEP aktif: hanya tampilkan stok > 0");
            }
            
            Log::info("Nilai AKTIFKANBATCH: " . $aktifkanBatch);
            
            if($aktifkanBatch == "yes") {
                // Query dengan batch
                Log::info("Menggunakan query dengan batch (no_batch dan no_faktur tidak kosong)");
                $obat = DB::table('databarang')
                    ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                    ->join('industrifarmasi', 'industrifarmasi.kode_industri', '=', 'databarang.kode_industri')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('databarang.status', '=', '1')
                    ->where('gudangbarang.kd_bangsal', '=', $kd_bangsal)
                    ->where('gudangbarang.no_batch', '<>', '')
                    ->where('gudangbarang.no_faktur', '<>', '')
                    ->whereRaw("1=1 {$qrystokkosong}")
                    ->where(function ($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                            ->orWhere('databarang.nama_brng', 'like', $que)
                            ->orWhere('jenis.nama', 'like', $que)
                            ->orWhere('databarang.letak_barang', 'like', $que);
                    })
                    ->select(
                        'databarang.kode_brng as id',
                        'databarang.nama_brng as text',
                        DB::raw('sum(gudangbarang.stok) as stok')
                    )
                    ->groupBy('gudangbarang.kode_brng')
                    ->orderBy('databarang.nama_brng')
                    ->limit(20) // Batasi hasil untuk performa
                    ->get();
            } else {
                // Query tanpa batch (nilai default dalam Java adalah "no")
                Log::info("Menggunakan query tanpa batch (no_batch dan no_faktur kosong)");
                $obat = DB::table('databarang')
                    ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
                    ->join('industrifarmasi', 'industrifarmasi.kode_industri', '=', 'databarang.kode_industri')
                    ->join('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                    ->where('databarang.status', '=', '1')
                    ->where('gudangbarang.kd_bangsal', '=', $kd_bangsal)
                    // Hapus kondisi batch dan faktur yang ketat
                    // ->where('gudangbarang.no_batch', '=', '')
                    // ->where('gudangbarang.no_faktur', '=', '')
                    ->whereRaw("1=1 {$qrystokkosong}")
                    ->where(function ($query) use ($que) {
                        $query->where('databarang.kode_brng', 'like', $que)
                            ->orWhere('databarang.nama_brng', 'like', $que)
                            ->orWhere('jenis.nama', 'like', $que)
                            ->orWhere('databarang.letak_barang', 'like', $que);
                    })
                    ->select(
                        'databarang.kode_brng as id',
                        'databarang.nama_brng as text',
                        DB::raw('SUM(gudangbarang.stok) as stok')
                    )
                    ->groupBy('databarang.kode_brng', 'databarang.nama_brng')
                    ->orderBy('databarang.nama_brng')
                    ->limit(100) // Meningkatkan batas hasil
                    ->get();
            }
            
            Log::info("Berhasil mengambil " . count($obat) . " obat luar");
            return response()->json($obat, 200);
        } catch (\Exception $e) {
            Log::error("Error di getObatLuar: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            return response()->json([], 500);
        }
    }

    public function getDataObat(Request $request, $kdObat)
    {
        try {
            $input = $request->all();
            $status = $input['status'];
            $kode = $input['kode'];
            $bangsal = "";
            
            // Log debug info
            Log::info("getDataObat dipanggil untuk kode obat: {$kdObat}, status: {$status}, kode: {$kode}");
            
            // Tentukan kode bangsal berdasarkan status dan kode
            if ($status == 'ralan') {
                $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
                $bangsal = $db ? $db->kd_bangsal : null;
                Log::info("Status ralan: mencari depo dari poli {$kode}, hasilnya: " . ($bangsal ?: "tidak ditemukan"));
            } else {
                $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
                $bangsal = $db ? $db->kd_depo : null;
                Log::info("Status ranap: mencari depo dari bangsal {$kode}, hasilnya: " . ($bangsal ?: "tidak ditemukan"));
                
                // Jika tidak ditemukan, gunakan kode bangsal langsung
                if (empty($bangsal)) {
                    $bangsal = $kode;
                    Log::info("Menggunakan kode bangsal langsung: {$bangsal}");
                }
            }
            
            // Pastikan bangsal tidak null sebelum mengambil stok
            if (empty($bangsal)) {
                Log::error("kd_bangsal kosong saat getDataObat", [
                    'kode_brng' => $kdObat,
                    'status' => $status,
                    'kode' => $kode
                ]);
                
                // Gunakan kode bangsal default dari tabel
                $bangsal = 'B0009'; // Alternatif: B0007, B0010, B0013, dll
                Log::info("Menggunakan kode bangsal default: {$bangsal}");
                
                // Ambil data dengan kode bangsal default
                $data = DB::table('databarang')
                    ->leftJoin('gudangbarang', function($join) use ($kdObat, $bangsal) {
                        $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                            ->where('gudangbarang.kd_bangsal', '=', $bangsal);
                            // Hapus kondisi no_batch dan no_faktur yang ketat
                    })
                    ->where('databarang.kode_brng', $kdObat)
                    ->select(
                        'databarang.*', 
                        DB::raw('COALESCE(SUM(gudangbarang.stok), 0) as stok')
                    )
                    ->groupBy('databarang.kode_brng')
                    ->first();
                
                if ($data) {
                    return response()->json($data);
                }
                
                // Jika tidak ditemukan di depo default, kembalikan data barang tanpa stok
                $dataBarang = DB::table('databarang')
                    ->where('kode_brng', $kdObat)
                    ->first();
                    
                // Buat objek baru dengan stok = 0
                $dataWithStok = $dataBarang ? (array)$dataBarang : [];
                $dataWithStok['stok'] = 0;
                
                return response()->json((object)$dataWithStok);
            }
            
            // Ambil data dari gudangbarang dan databarang (langsung, tanpa riwayat)
            $data = DB::table('databarang')
                ->leftJoin('gudangbarang', function($join) use ($kdObat, $bangsal) {
                    $join->on('databarang.kode_brng', '=', 'gudangbarang.kode_brng')
                        ->where('gudangbarang.kd_bangsal', '=', $bangsal);
                        // Hapus kondisi no_batch dan no_faktur yang ketat
                })
                ->where('databarang.kode_brng', $kdObat)
                ->select(
                    'databarang.*', 
                    DB::raw('COALESCE(SUM(gudangbarang.stok), 0) as stok')
                )
                ->groupBy('databarang.kode_brng')
                ->first();
            
            // Log informasi tentang stok obat yang ditemukan
            Log::info("getDataObat: Stok obat {$kdObat} di bangsal {$bangsal}: " . ($data ? $data->stok : 'tidak ditemukan'));
            
            // Jika tidak ditemukan, kembalikan data barang dengan stok 0
            if (!$data) {
                $dataBarang = DB::table('databarang')
                    ->where('kode_brng', $kdObat)
                    ->first();
                    
                // Buat objek baru dengan stok = 0
                $dataWithStok = $dataBarang ? (array)$dataBarang : [];
                $dataWithStok['stok'] = 0;
                
                return response()->json((object)$dataWithStok);
            }

            return response()->json($data);
        } catch (\Exception $e) {
            Log::error("Error di getDataObat: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());
            
            // Return default response with empty stok
            $dataBarang = DB::table('databarang')
                ->where('kode_brng', $kdObat)
                ->first();
                
            $dataWithStok = $dataBarang ? (array)$dataBarang : [];
            $dataWithStok['stok'] = 0;
            
            return response()->json((object)$dataWithStok);
        }
    }

    public function postResep(Request $request, $noRawat)
    {
        try {
            // Validasi parameter yang diperlukan
            if (!$request->has('obat') || !$request->has('jumlah') || !$request->has('aturan_pakai') || !$request->has('status') || !$request->has('kode')) {
                Log::warning("Parameter input tidak lengkap pada panggilan postResep", [
                    'has_obat' => $request->has('obat'),
                    'has_jumlah' => $request->has('jumlah'),
                    'has_aturan_pakai' => $request->has('aturan_pakai'),
                    'has_status' => $request->has('status'),
                    'has_kode' => $request->has('kode')
                ]);
                
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Parameter input tidak lengkap'
                ], 400);
            }
            
            $dokter = session()->get('username');
            $resObat = $request->get('obat');
            $resJml = $request->get('jumlah');
            $resAturan = $request->get('aturan_pakai');
            $status = $request->get('status');
            $kode = $request->get('kode');
            
            // Pastikan array obat, jumlah, dan aturan pakai memiliki elemen
            if (empty($resObat) || empty($resJml)) {
                Log::warning("Data obat atau jumlah kosong", [
                    'count_obat' => count($resObat ?? []),
                    'count_jumlah' => count($resJml ?? [])
                ]);
                
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Data obat atau jumlah tidak boleh kosong'
                ], 400);
            }
            
            // Log parameter yang diterima untuk debugging
            Log::info("Parameter postResep:", [
                'noRawat' => $noRawat,
                'status' => $status,
                'kode' => $kode,
                'jumlah_obat' => count($resObat ?? [])
            ]);
            
            // Dekripsi dan sanitasi no_rawat
            $encryptedNoRawat = $noRawat; // Simpan nilai terenkripsi untuk log
            $noRawat = $this->decryptData($noRawat);
            
            // URL-decode jika perlu
            if (strpos($noRawat, '%') !== false) {
                $noRawat = urldecode($noRawat);
            }
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($noRawat)) {
                $noRawat = (string)$noRawat;
            }
            
            // Hapus karakter non-printable dan URL encoding
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $noRawat);
            
            // Jika hasil tidak sesuai format, coba decode dari base64
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                $base64Decoded = base64_decode($cleanNoRawat);
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    $cleanNoRawat = $base64Decoded;
                } else {
                    // Coba tanpa URL encoding
                    $base64Decoded = base64_decode(str_replace('%3D', '=', $encryptedNoRawat));
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        $cleanNoRawat = $base64Decoded;
                    }
                }
            }
            
            // Log debug untuk tracking
            Log::info('Proses no_rawat di postResep', [
                'encrypted' => $encryptedNoRawat,
                'decoded' => $noRawat,
                'cleaned' => $cleanNoRawat
            ]);
            
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
            
            // Mulai transaksi setelah validasi awal
            DB::beginTransaction();
            
            try {
                $noRawat = $cleanNoRawat; // Gunakan no_rawat yang sudah dibersihkan
                $bangsal = "";
            
                if ($status == 'Ralan') {
                    $iter = $request->get('iter');
                    if ($iter != '-') {
                        DB::table('resep_iter')->upsert(
                            [
                                'no_rawat' => $noRawat,
                                'catatan_iter' => $iter,
                            ],
                            ['no_rawat'],
                            ['catatan_iter']
                        );
                    }
                    
                    // Dapatkan bangsal dari set_depo_ralan
                    $db = DB::table('set_depo_ralan')->where('kd_poli', $kode)->first();
                    if ($db) {
                        $bangsal = $db->kd_bangsal;
                    } else {
                        Log::warning("Tidak ada bangsal yang terkait dengan poli {$kode}");
                    }
                } else {
                    // Dapatkan bangsal dari set_depo_ranap
                    $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
                    if ($db) {
                        $bangsal = $db->kd_depo;
                    } else {
                        Log::warning("Tidak ada depo yang terkait dengan bangsal {$kode}");
                    }
                }
                
                // Jika bangsal masih kosong, coba ambil langsung dari parameter kode
                if (empty($bangsal)) {
                    $bangsal = $kode;
                    Log::info("Menggunakan kode sebagai bangsal: {$bangsal}");
                }

                // Buat nomor resep
                $no = DB::table('resep_obat')
                    ->whereDate('tgl_peresepan', date('Y-m-d'))
                    ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as resep")
                    ->first();
                
                $maxNo = $no->resep;
                $nextNo = sprintf('%06s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . $nextNo;
                
                Log::info("Nomor resep yang dibuat: {$noResep}");

                // Cek apakah resep sudah ada untuk no_rawat ini
                $resepExists = DB::table('resep_obat')
                    ->where('no_rawat', $noRawat)
                    ->where('tgl_peresepan', date('Y-m-d'))
                    ->first();

                // Log hasil pengecekan resep
                Log::info("Cek resep yang sudah ada: ", [
                    'resep_exists' => $resepExists ? 'ya' : 'tidak',
                    'no_resep_exists' => $resepExists ? $resepExists->no_resep : 'tidak ada'
                ]);

                // Jika resep sudah ada dan sudah divalidasi, berikan pesan
                if ($resepExists && $resepExists->tgl_perawatan != '0000-00-00') {
                    DB::rollBack();
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Resep obat sudah tervalidasi'
                    ]);
                }
                
                // Jika resep belum ada, buat entry baru di resep_obat
                if (!$resepExists) {
                    Log::info("Membuat resep baru dengan nomor: {$noResep}");
                    
                    // Data untuk insert dengan nilai default untuk kolom tanggal dan jam
                    $resepData = [
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',  // Format default yang diharapkan
                        'jam' => '00:00:00',              // Format default yang diharapkan
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                        'tgl_penyerahan' => '0000-00-00', // Format default yang diharapkan
                        'jam_penyerahan' => '00:00:00',   // Format default yang diharapkan
                    ];
                    
                    // Log data yang akan dimasukkan
                    Log::info("Data resep_obat yang akan disimpan:", $resepData);
                    
                    $insert = DB::table('resep_obat')->insert($resepData);
                    
                    // Verifikasi data resep telah tersimpan
                    $cekResep = DB::table('resep_obat')->where('no_resep', $noResep)->first();
                    Log::info("Status penyimpanan resep baru: ", [
                        'insert_success' => $insert ? 'ya' : 'tidak',
                        'resep_found' => $cekResep ? 'ya' : 'tidak',
                        'tgl_perawatan' => $cekResep ? $cekResep->tgl_perawatan : 'N/A',
                        'jam' => $cekResep ? $cekResep->jam : 'N/A',
                        'tgl_penyerahan' => $cekResep ? $cekResep->tgl_penyerahan : 'N/A',
                        'jam_penyerahan' => $cekResep ? $cekResep->jam_penyerahan : 'N/A'
                    ]);
                    
                    $useNoResep = $noResep;
                } else {
                    $useNoResep = $resepExists->no_resep;
                    Log::info("Menggunakan resep yang sudah ada: {$useNoResep}");
                }

                // Flag untuk melacak apakah ada obat yang berhasil disimpan
                $anyDrugSaved = false;

                // Loop untuk setiap obat yang diresepkan
                for ($i = 0; $i < count($resObat); $i++) {
                    $obat = $resObat[$i];
                    $jml = $resJml[$i];
                    $aturan = $resAturan[$i] ?? '-';

                    if (empty($jml) || $jml < 1) {
                        Log::info("Melewati obat {$obat} karena jumlah tidak valid: {$jml}");
                        continue;
                    }

                    // Pastikan kd_bangsal tidak null
                    if (empty($bangsal)) {
                        Log::error("kd_bangsal kosong saat cek stok obat: {$obat}", [
                            'kode_brng' => $obat,
                            'kd_bangsal' => $bangsal
                        ]);
                        continue;
                    }

                    // Cek stok obat dari gudangbarang
                    $stokData = DB::table('gudangbarang')
                        ->where('kode_brng', $obat)
                        ->where('kd_bangsal', $bangsal)
                        ->select(DB::raw('SUM(stok) as total_stok'))
                        ->first();
                    
                    $maxStok = $stokData ? $stokData->total_stok : 0;
                    
                    // Log informasi stok
                    Log::info("Cek stok obat dari gudangbarang", [
                        'kode_brng' => $obat,
                        'kd_bangsal' => $bangsal,
                        'stok' => $maxStok,
                        'jumlah_diminta' => $jml
                    ]);

                    if ($maxStok < $jml) {
                        Log::warning("Stok obat tidak mencukupi", [
                            'kode_brng' => $obat,
                            'stok_tersedia' => $maxStok,
                            'jumlah_diminta' => $jml
                        ]);
                        continue; // Skip jika stok tidak mencukupi
                    }

                    // Untuk obat non-racikan, tidak perlu pembagian kapasitas
                    $detailData = [
                        'no_resep' => $useNoResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ];
                    
                    // Log data detail yang akan dimasukkan
                    Log::info("Data resep_dokter yang akan disimpan:", $detailData);
                    
                    $insert = DB::table('resep_dokter')->insert($detailData);
                    
                    Log::info("Hasil penyimpanan detail resep: ", [
                        'no_resep' => $useNoResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                        'insert_success' => $insert ? 'ya' : 'tidak'
                    ]);
                    
                    if ($insert) {
                        $anyDrugSaved = true;
                    }
                }
                
                // Jika tidak ada obat yang berhasil disimpan, rollback transaksi
                if (!$anyDrugSaved) {
                    Log::warning("Tidak ada obat yang berhasil disimpan, membatalkan transaksi");
                    DB::rollBack();
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Tidak ada obat yang berhasil ditambahkan ke resep'
                    ]);
                }

                // Ambil data resep untuk respons
                $resep = DB::table('resep_dokter')
                    ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                    ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                    ->where('resep_obat.no_rawat', $noRawat)
                    ->where('resep_obat.kd_dokter', $dokter)
                    ->select(
                        'resep_dokter.no_resep', 
                        'resep_dokter.kode_brng', 
                        'resep_dokter.jml', 
                        'databarang.nama_brng', 
                        'resep_dokter.aturan_pakai', 
                        'resep_obat.tgl_peresepan', 
                        'resep_obat.jam_peresepan'
                    )
                    ->orderBy('resep_obat.jam_peresepan', 'desc')
                    ->get();
                
                Log::info("Data resep yang berhasil diambil: " . count($resep));
                
                // Commit transaksi jika semua operasi berhasil
                DB::commit();
                
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Input resep berhasil',
                    'data' => $resep,
                ]);
            } catch (\Illuminate\Database\QueryException $ex) {
                DB::rollback();
                Log::error('Error pada postResep (QueryException): ' . $ex->getMessage());
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => $ex->getMessage()
                ]);
            } catch (\Exception $e) {
                DB::rollback();
                Log::error('Error pada postResep (Exception): ' . $e->getMessage());
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => $e->getMessage()
                ]);
            }
        } catch (\Exception $outerException) {
            Log::error('Error fatal pada postResep (outer): ' . $outerException->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $outerException->getMessage()
            ]);
        }
    }

    public function postResepRanap(Request $request, $noRawat)
    {
        $dokter = $request->get('dokter');
        $resObat = $request->get('obat');
        $resJml = $request->get('jumlah');
        $resAturan = $request->get('aturan_pakai');
        $status = $request->get('status');
        $kode = $request->get('kode');
        $noRawat = $this->decryptData($noRawat);
        $bangsal = "";

        if (empty($dokter)) {
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Dokter tidak boleh kosong'
            ]);
        }


        try {
            DB::beginTransaction();
            // $db = DB::table('set_depo_ranap')->where('kd_bangsal', $kode)->first();
            // $bangsal = $db->kd_depo;
            $bangsal = $kode;

            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl . '' . $nextNo;

            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i] < 1 ? 1 : $resJml[$i];
                $aturan = $resAturan[$i] ?? '-';

                // Pastikan kd_bangsal tidak null
                if (empty($bangsal)) {
                    Log::error("kd_bangsal kosong saat cek stok obat ranap: {$obat}", [
                        'kode_brng' => $obat,
                        'kd_bangsal' => $bangsal
                    ]);
                    continue;
                }

                $maxTgl = DB::table('riwayat_barang_medis')
                    ->where('kode_brng', $obat)
                    ->where('kd_bangsal', $bangsal)
                    ->max('tanggal');
                
                if (!$maxTgl) {
                    Log::warning("Tidak ada riwayat barang untuk obat ranap {$obat} di bangsal {$bangsal}");
                    continue;
                }
                
                $maxJam = DB::table('riwayat_barang_medis')
                    ->where('kode_brng', $obat)
                    ->where('tanggal', $maxTgl)
                    ->where('kd_bangsal', $bangsal)
                    ->max('jam');
                
                if (!$maxJam) {
                    Log::warning("Tidak ada jam untuk obat ranap {$obat} di bangsal {$bangsal} tanggal {$maxTgl}");
                    continue;
                }
                
                // Ambil stok_akhir dengan kondisi lengkap
                $stokData = DB::table('riwayat_barang_medis')
                    ->where('kode_brng', $obat)
                    ->where('kd_bangsal', $bangsal)
                    ->where('tanggal', $maxTgl)
                    ->where('jam', $maxJam)
                    ->first(['stok_akhir']);
                
                $maxStok = $stokData ? $stokData->stok_akhir : 0;
                
                // Log informasi stok
                Log::info("Cek stok obat ranap dari riwayat_barang_medis", [
                    'kode_brng' => $obat,
                    'kd_bangsal' => $bangsal,
                    'stok' => $maxStok,
                    'jumlah_diminta' => $jml,
                    'tanggal' => $maxTgl,
                    'jam' => $maxJam
                ]);

                if ($maxStok < $jml) {
                    continue;
                }

                $maxTglResep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->where('kd_dokter', $dokter)->max('jam_peresepan');
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->where('tgl_peresepan', date('Y-m-d'))->where('kd_dokter', $dokter)->where('jam_peresepan', $maxTglResep)->first();

                if (!empty($resep) && $resep->tgl_perawatan != '0000-00-00') {
                    //resep sudah divalidasi

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                } else if (empty($resep)) {
                    //resep belum ada

                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status,
                    ]);

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $noResep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                } else {
                    //resep sudah ada dan belum divalidasi

                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan ?? '-',
                    ]);
                }
            }
            DB::commit();
            return response()->json([
                'status' => 'sukses',
                'pesan' => 'Input resep berhasil'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
            ]);
        }
    }

    public function postResepRacikan(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];
        $satu_resep = $input['satu_resep'] ?? 0;

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        // Log parameter yang diterima untuk debugging
        Log::info("Parameter postResepRacikan:", [
            'noRawat' => $noRawat,
            'status' => $input['status'] ?? '-',
            'kode' => $input['kode'] ?? '-',
            'jumlah_obat' => count($kdObat ?? [])
        ]);

        try {
            // Dekripsi dan sanitasi no_rawat
            $encryptedNoRawat = $noRawat; // Simpan nilai terenkripsi untuk log
            $no_rawat = $this->decryptData($noRawat);
            
            // URL-decode jika perlu
            if (strpos($no_rawat, '%') !== false) {
                $no_rawat = urldecode($no_rawat);
            }
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($no_rawat)) {
                $no_rawat = (string)$no_rawat;
            }
            
            // Hapus karakter non-printable dan URL encoding
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $no_rawat);
            
            // Jika hasil tidak sesuai format, coba decode dari base64
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                $base64Decoded = base64_decode($cleanNoRawat);
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    $cleanNoRawat = $base64Decoded;
                } else {
                    // Coba tanpa URL encoding
                    $base64Decoded = base64_decode(str_replace('%3D', '=', $encryptedNoRawat));
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        $cleanNoRawat = $base64Decoded;
                    }
                }
            }
            
            // Log debug untuk tracking
            Log::info('Proses no_rawat di postResepRacikan', [
                'encrypted' => $encryptedNoRawat,
                'decoded' => $no_rawat,
                'cleaned' => $cleanNoRawat
            ]);
            
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
            
            // Gunakan no_rawat yang sudah dibersihkan
            $no_rawat = $cleanNoRawat;
            $dokter = session()->get('username');
            $status = $request->get('status');
            $bangsal = $request->get('kode');

            $request->validate([
                'nama_racikan' => 'required',
                'aturan_racikan' => 'required',
                'jumlah_racikan' => 'required',
                'metode_racikan' => 'required',
                'keterangan_racikan' => 'required',
                'kd_obat' => 'required',
                'kd_obat.*' => 'required',
                'p1' => 'required',
                'p1.*' => 'required',
                'p2' => 'required',
                'p2.*' => 'required',
                'kandungan' => 'required',
                'kandungan.*' => 'required',
                'jml' => 'required',
                'jml.*' => 'required',
            ], [
                'kd_obat.*.required' => 'Obat tidak boleh kosong',
                'p1.*.required' => 'P1 tidak boleh kosong',
                'p2.*.required' => 'P2 tidak boleh kosong',
                'kandungan.*.required' => 'Kandungan tidak boleh kosong',
                'jml.*.required' => 'Jumlah tidak boleh kosong',
            ]);

            DB::beginTransaction();
            $noResep = '';

            if ($satu_resep == 0) {
                $no = DB::table('resep_obat')
                    ->whereDate('tgl_peresepan', date('Y-m-d'))
                    ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as resep")
                    ->first();
                
                $maxNo = $no->resep;
                $nextNo = sprintf('%06s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . $nextNo;

                DB::table('resep_obat')
                    ->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status ?? 'ralan',
                        'tgl_penyerahan' => '0000-00-00',
                        'jam_penyerahan' => '00:00:00',
                    ]);
            } else {
                $resep = DB::table('resep_obat')
                    ->where(DB::raw('BINARY no_rawat'), $no_rawat)
                    ->where('tgl_peresepan', date('Y-m-d'))
                    ->first();
                if (!empty($resep)) {
                    $noResep = $resep->no_resep;
                } else {
                    $no = DB::table('resep_obat')
                        ->whereDate('tgl_peresepan', date('Y-m-d'))
                        ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as resep")
                        ->first();
                    
                    $maxNo = $no->resep;
                    $nextNo = sprintf('%06s', ($maxNo + 1));
                    $tgl = date('Ymd');
                    $noResep = $tgl . $nextNo;

                    DB::table('resep_obat')
                        ->insert([
                            'no_resep' => $noResep,
                            'tgl_perawatan' => '0000-00-00',
                            'jam' => '00:00:00',
                            'no_rawat' => $no_rawat,
                            'kd_dokter' => $dokter,
                            'tgl_peresepan' => date('Y-m-d'),
                            'jam_peresepan' => date('H:i:s'),
                            'status' => $status ?? 'ralan',
                            'tgl_penyerahan' => '0000-00-00',
                            'jam_penyerahan' => '00:00:00',
                        ]);
                }
            }

            DB::table('resep_dokter_racikan')
                ->insert([
                    'no_resep' => $noResep,
                    'no_racik' => '1',
                    'nama_racik' => $namaRacikan,
                    'kd_racik' => $metodeRacikan,
                    'jml_dr' => $jumlahRacikan,
                    'aturan_pakai' => $aturanPakai,
                    'keterangan' => $keteranganRacikan,
                ]);

            for ($i = 0; $i < count($kdObat); $i++) {
                if (empty($kdObat[$i]) || (float)$p1[$i] <= 0) {
                    continue;
                }

                // Pastikan kd_bangsal tidak null
                if (empty($bangsal)) {
                    Log::error("kd_bangsal kosong saat cek stok obat racikan: {$kdObat[$i]}", [
                        'kode_brng' => $kdObat[$i],
                        'kd_bangsal' => $bangsal
                    ]);
                    continue;
                }
                
                // Cek stok obat dari gudangbarang
                $stokData = DB::table('gudangbarang')
                    ->where('kode_brng', $kdObat[$i])
                    ->where('kd_bangsal', $bangsal)
                    ->select(DB::raw('SUM(stok) as total_stok'))
                    ->first();
                
                $maxStok = $stokData ? $stokData->total_stok : 0;
                
                // Dapatkan kapasitas obat
                $kapasitasData = DB::table('databarang')
                    ->where('kode_brng', $kdObat[$i])
                    ->select('kapasitas')
                    ->first();
                
                $kapasitas = ($kapasitasData && $kapasitasData->kapasitas > 0) ? $kapasitasData->kapasitas : 1;
                
                // Hitung jumlah berdasarkan kapasitas seperti di kode Java
                $jumlahAsli = (float)$jml[$i];
                $jumlahKapasitas = $satu_resep == 0 ? $jumlahAsli : (float)$jml[$i] * (float)$jumlahRacikan;
                
                // Pembagian dengan kapasitas (sesuai kode Java)
                $jumlahSetelahPembagian = $jumlahKapasitas / $kapasitas;
                
                // Log untuk debug
                Log::info("Perhitungan jumlah obat racikan berdasarkan kapasitas", [
                    'kode_brng' => $kdObat[$i],
                    'kapasitas' => (float)$kapasitas,
                    'jumlah_asli' => $jumlahAsli,
                    'jumlah_kapasitas' => $jumlahKapasitas,
                    'jumlah_setelah_pembagian' => $jumlahSetelahPembagian
                ]);
                
                // Cek stok
                if ($maxStok < $jumlahSetelahPembagian) {
                    Log::warning("Stok tidak cukup untuk obat: {$kdObat[$i]}, Stok: {$maxStok}, Diminta: {$jumlahSetelahPembagian}");
                    continue;
                }
                
                // Insert detail racikan dengan jumlah yang sudah dihitung
                DB::table('resep_dokter_racikan_detail')->insert([
                    'no_resep' => $noResep,
                    'no_racik' => '1',
                    'kode_brng' => $kdObat[$i],
                    'p1' => $p1[$i],
                    'p2' => $p2[$i],
                    'kandungan' => $kandungan[$i],
                    'jml' => $jumlahSetelahPembagian
                ]);
            }
            
            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            // Log detail error untuk debugging
            Log::error('Error saat menyimpan resep racikan: ' . $ex->getMessage(), [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $ex->getTraceAsString(),
                'no_rawat' => $no_rawat ?? 'tidak tersedia'
            ]);
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan database: ' . $ex->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error umum saat menyimpan resep racikan: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function postResepRacikanRanap(Request $request, $noRawat)
    {
        $input = $request->all();
        $namaRacikan = $input['nama_racikan'];
        $aturanPakai = $input['aturan_racikan'];
        $jumlahRacikan = $input['jumlah_racikan'];
        $metodeRacikan = $input['metode_racikan'];
        $keteranganRacikan = $input['keterangan_racikan'];
        $satu_resep = $input['satu_resep'] ?? 0;

        $kdObat = $input['kd_obat'];
        $p1 = $input['p1'];
        $p2 = $input['p2'];
        $kandungan = $input['kandungan'];
        $jml = $input['jml'];

        try {
            // Dekripsi dan sanitasi no_rawat
            $encryptedNoRawat = $noRawat; // Simpan nilai terenkripsi untuk log
            $no_rawat = $this->decryptData($noRawat);
            
            // URL-decode jika perlu
            if (strpos($no_rawat, '%') !== false) {
                $no_rawat = urldecode($no_rawat);
            }
            
            // Pastikan data yang kita terima adalah string yang valid
            if (!is_string($no_rawat)) {
                $no_rawat = (string)$no_rawat;
            }
            
            // Hapus karakter non-printable dan URL encoding
            $cleanNoRawat = preg_replace('/[[:^print:]]/', '', $no_rawat);
            
            // Jika hasil tidak sesuai format, coba decode dari base64
            if (!preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $cleanNoRawat)) {
                $base64Decoded = base64_decode($cleanNoRawat);
                if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                    $cleanNoRawat = $base64Decoded;
                } else {
                    // Coba tanpa URL encoding
                    $base64Decoded = base64_decode(str_replace('%3D', '=', $encryptedNoRawat));
                    if ($base64Decoded !== false && preg_match('/^\d{4}\/\d{2}\/\d{2}\/\d{6}$/', $base64Decoded)) {
                        $cleanNoRawat = $base64Decoded;
                    }
                }
            }
            
            // Log debug untuk tracking
            Log::info('Proses no_rawat di postResepRacikanRanap', [
                'encrypted' => $encryptedNoRawat,
                'decoded' => $no_rawat,
                'cleaned' => $cleanNoRawat
            ]);
            
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
            
            // Gunakan no_rawat yang sudah dibersihkan
            $no_rawat = $cleanNoRawat;
            $dokter = session()->get('username');
            $status = $request->get('status');
            $bangsal = $request->get('kode');

            DB::beginTransaction();
            $noResep = '';

            if ($satu_resep == 0) {
                $no = DB::table('resep_obat')
                    ->whereDate('tgl_peresepan', date('Y-m-d'))
                    ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as resep")
                    ->first();
                
                $maxNo = $no->resep;
                $nextNo = sprintf('%06s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . $nextNo;

                DB::table('resep_obat')
                    ->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => '0000-00-00',
                        'jam' => '00:00:00',
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => $status ?? 'ranap',
                        'tgl_penyerahan' => '0000-00-00',
                        'jam_penyerahan' => '00:00:00',
                    ]);
            } else {
                $resep = DB::table('resep_obat')
                    ->where(DB::raw('BINARY no_rawat'), $no_rawat)
                    ->where('tgl_peresepan', date('Y-m-d'))
                    ->first();
                if (!empty($resep)) {
                    $noResep = $resep->no_resep;
                } else {
                    $no = DB::table('resep_obat')
                        ->whereDate('tgl_peresepan', date('Y-m-d'))
                        ->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,6),signed)),0) as resep")
                        ->first();
                    
                    $maxNo = $no->resep;
                    $nextNo = sprintf('%06s', ($maxNo + 1));
                    $tgl = date('Ymd');
                    $noResep = $tgl . $nextNo;

                    DB::table('resep_obat')
                        ->insert([
                            'no_resep' => $noResep,
                            'tgl_perawatan' => '0000-00-00',
                            'jam' => '00:00:00',
                            'no_rawat' => $no_rawat,
                            'kd_dokter' => $dokter,
                            'tgl_peresepan' => date('Y-m-d'),
                            'jam_peresepan' => date('H:i:s'),
                            'status' => $status ?? 'ranap',
                            'tgl_penyerahan' => '0000-00-00',
                            'jam_penyerahan' => '00:00:00',
                        ]);
                }
            }

            DB::table('resep_dokter_racikan')
                ->insert([
                    'no_resep' => $noResep,
                    'no_racik' => '1',
                    'nama_racik' => $namaRacikan,
                    'kd_racik' => $metodeRacikan,
                    'jml_dr' => $jumlahRacikan,
                    'aturan_pakai' => $aturanPakai,
                    'keterangan' => $keteranganRacikan,
                ]);

            for ($i = 0; $i < count($kdObat); $i++) {
                if (empty($kdObat[$i]) || (float)$p1[$i] <= 0) {
                    continue;
                }

                // Pastikan kd_bangsal tidak null
                if (empty($bangsal)) {
                    Log::error("kd_bangsal kosong saat cek stok obat racikan: {$kdObat[$i]}", [
                        'kode_brng' => $kdObat[$i],
                        'kd_bangsal' => $bangsal
                    ]);
                    continue;
                }
                
                // Cek stok obat dari gudangbarang
                $stokData = DB::table('gudangbarang')
                    ->where('kode_brng', $kdObat[$i])
                    ->where('kd_bangsal', $bangsal)
                    ->select(DB::raw('SUM(stok) as total_stok'))
                    ->first();
                
                $maxStok = $stokData ? $stokData->total_stok : 0;
                
                // Dapatkan kapasitas obat
                $kapasitasData = DB::table('databarang')
                    ->where('kode_brng', $kdObat[$i])
                    ->select('kapasitas')
                    ->first();
                
                $kapasitas = ($kapasitasData && $kapasitasData->kapasitas > 0) ? $kapasitasData->kapasitas : 1;
                
                // Hitung jumlah berdasarkan kapasitas seperti di kode Java
                $jumlahAsli = (float)$jml[$i];
                $jumlahKapasitas = $satu_resep == 0 ? $jumlahAsli : (float)$jml[$i] * (float)$jumlahRacikan;
                
                // Pembagian dengan kapasitas (sesuai kode Java)
                $jumlahSetelahPembagian = $jumlahKapasitas / $kapasitas;
                
                // Log untuk debug
                Log::info("Perhitungan jumlah obat racikan berdasarkan kapasitas", [
                    'kode_brng' => $kdObat[$i],
                    'kapasitas' => (float)$kapasitas,
                    'jumlah_asli' => $jumlahAsli,
                    'jumlah_kapasitas' => $jumlahKapasitas,
                    'jumlah_setelah_pembagian' => $jumlahSetelahPembagian
                ]);
                
                // Cek stok
                if ($maxStok < $jumlahSetelahPembagian) {
                    Log::warning("Stok tidak cukup untuk obat: {$kdObat[$i]}, Stok: {$maxStok}, Diminta: {$jumlahSetelahPembagian}");
                    continue;
                }
                
                // Insert detail racikan dengan jumlah yang sudah dihitung
                DB::table('resep_dokter_racikan_detail')->insert([
                    'no_resep' => $noResep,
                    'no_racik' => '1',
                    'kode_brng' => $kdObat[$i],
                    'p1' => $p1[$i],
                    'p2' => $p2[$i],
                    'kandungan' => $kandungan[$i],
                    'jml' => $jumlahSetelahPembagian
                ]);
            }
            
            DB::commit();
            return response()->json(['status' => 'sukses', 'message' => 'Racikan berhasil ditambahkan']);
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollBack();
            // Log detail error untuk debugging
            Log::error('Error saat menyimpan resep racikan ranap: ' . $ex->getMessage(), [
                'file' => __FILE__,
                'line' => __LINE__,
                'trace' => $ex->getTraceAsString(),
                'no_rawat' => $no_rawat ?? 'tidak tersedia'
            ]);
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan database: ' . $ex->getMessage()
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error umum saat menyimpan resep racikan ranap: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }

    public function hapusObat($noResep, $kdObat, $noRawat)
    {
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($noRawat);
        try {
            $cek = DB::table('resep_obat')->where('no_resep', $noResep)->first();
            if ($cek->tgl_perawatan != '0000-00-00') {
                return response()->json(['status' => 'gagal', 'pesan' => 'Resep sudah tervalidasi, silahkan hubungi farmasi untuk menghapus obat']);
            }
            DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            $resep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->where('resep_obat.no_rawat', $noRawat)
                ->where('resep_obat.kd_dokter', $dokter)
                ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                ->get();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $resep]);
        } catch (\Exception $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    public function hapusObatBatch(Request $request)
    {
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($request->get('no_rawat'));
        $noResep = $request->get('no_resep');
        $kdObat = $request->get('obat');
        // return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $kdObat]);
        try {
            DB::beginTransaction();
            $cek = DB::table('resep_obat')->where('no_resep', $noResep)->first();
            if ($cek->tgl_perawatan != '0000-00-00') {
                return response()->json(['status' => 'gagal', 'pesan' => 'Resep sudah tervalidasi, silahkan hubungi farmasi untuk menghapus obat']);
            }
            foreach ($kdObat as $key => $value) {
                DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat[$key])->delete();
            }
            DB::commit();
            DB::table('resep_dokter')->where('no_resep', $noResep)->where('kode_brng', $kdObat)->delete();
            $resep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->join('resep_obat', 'resep_obat.no_resep', '=', 'resep_dokter.no_resep')
                ->where('resep_obat.no_rawat', $noRawat)
                ->where('resep_obat.kd_dokter', $dokter)
                ->select('resep_dokter.no_resep', 'resep_dokter.kode_brng', 'resep_dokter.jml', 'databarang.nama_brng', 'resep_dokter.aturan_pakai', 'resep_dokter.no_resep', 'databarang.nama_brng', 'resep_obat.tgl_peresepan', 'resep_obat.jam_peresepan')
                ->get();
            return response()->json(['status' => 'sukses', 'pesan' => 'Obat berhasil dihapus', 'data' => $resep]);
        } catch (\Exception $ex) {
            DB::rollBack();
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
        }
    }

    /**
     * Mendapatkan detail resep untuk copy resep
     */
    public function getDetailResep($noResep)
    {
        try {
            Log::info("Mengambil detail resep untuk noResep: {$noResep}");
            
            // Validasi nomor resep
            if (empty($noResep)) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Nomor resep tidak boleh kosong'
                ], 400);
            }
            
            // Ambil detail resep
            $detailResep = DB::table('resep_dokter')
                ->join('databarang', 'resep_dokter.kode_brng', '=', 'databarang.kode_brng')
                ->where('resep_dokter.no_resep', $noResep)
                ->select(
                    'resep_dokter.kode_brng',
                    'databarang.nama_brng',
                    'resep_dokter.jml',
                    'resep_dokter.aturan_pakai'
                )
                ->get();
            
            Log::info("Berhasil mengambil " . count($detailResep) . " detail resep");
            
            if (count($detailResep) === 0) {
                return response()->json([
                    'status' => 'gagal',
                    'pesan' => 'Tidak ada detail resep yang ditemukan'
                ], 404);
            }
            
            return response()->json($detailResep);
        } catch (\Exception $e) {
            Log::error("Error saat mengambil detail resep: " . $e->getMessage());
            return response()->json([
                'status' => 'gagal',
                'pesan' => 'Terjadi kesalahan saat mengambil detail resep: ' . $e->getMessage()
            ], 500);
        }
    }
}
