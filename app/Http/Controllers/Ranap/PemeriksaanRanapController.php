<?php

namespace App\Http\Controllers\Ranap;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Traits\EnkripsiData;
use Request;

class PemeriksaanRanapController extends Controller
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

    public function index()
    {
        $dokter = session()->get('username');
        $noRawat = Request::get('no_rawat');
        $noRM = Request::get('no_rm');
        return view('ranap.pemeriksaan-ranap');
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
        $aturanPakai = Request::get('aturan_racikan');
        $jumlahRacikan = Request::get('jumlah_racikan');
        $metodeRacikan = Request::get('metode_racikan');
        $keteranganRacikan = Request::get('keterangan_racikan');
        $no_rawat = $this->decryptData($noRawat);
        $dokter = session()->get('username');

        $validate = Request::validate([
            'nama_racikan' => 'required',
            'aturan_racikan' => 'required',
            'jumlah_racikan' => 'required',
            'metode_racikan' => 'required',
            'keterangan_racikan' => 'required',
        ]);

        try {
            // $resep = DB::table('resep_obat')->where('no_rawat', $no_rawat)->first();
            $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
            $maxNo = substr($no->resep, 0, 4);
            $nextNo = sprintf('%04s', ($maxNo + 1));
            $tgl = date('Ymd');
            $noResep = $tgl . '' . $nextNo;

            $cek = DB::table('resep_obat')
                ->join('resep_dokter_racikan', 'resep_obat.no_resep', '=', 'resep_dokter_racikan.no_resep')
                ->where('resep_obat.no_rawat', $no_rawat)->where('resep_obat.tgl_peresepan', date('Y-m-d'))
                ->select('resep_obat.no_resep')
                ->first();

            if (!empty($cek)) {
                $noRacik = DB::table('resep_dokter_racikan')->where('no_resep', $cek->no_resep)->max('no_racik');
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
                    return response()->json(['status' => 'sukses', 'pesan' => 'Racikan berhasil ditambahkan']);
                }
            } else {
                $insert = DB::table('resep_obat')
                    ->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => date('Y-m-d'),
                        'jam' => date('H:i:s'),
                        'no_rawat' => $no_rawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => date('Y-m-d'),
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'ralan',
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
                        return response()->json(['status' => 'sukses', 'pesan' => 'Racikan berhasil ditambahkan']);
                    }
                } else {
                    return response()->json(['status' => 'gagal', 'pesan' => 'Racikan gagal ditambahkan']);
                }
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json(['status' => 'gagal', 'pesan' => $ex->getMessage()]);
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

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0007')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'B0007')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'B0007')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < 1) {
                    $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                    return response()->json([
                        'status' => 'gagal',
                        'pesan' => 'Stok obat ' . $dataBarang->nama_brng ?? $obat . ' kosong'
                    ]);
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
        $noRawat = $this->decryptData($noRawat);
        // $validate = Request::validate([
        //     'obat' => 'required',
        //     'jumlah' => 'required',
        //     'aturan_pakai' => 'required',
        // ]);
        // if ($validate->fails()) {    
        //     return response()->json($validate->messages(), Response::HTTP_BAD_REQUEST);
        // }

        try {

            for ($i = 0; $i < count($resObat); $i++) {
                $obat = $resObat[$i];
                $jml = $resJml[$i];
                $aturan = $resAturan[$i];

                $maxTgl = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'FARM')->max('tanggal');
                $maxJam = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('tanggal', $maxTgl)->where('kd_bangsal', 'FARM')->max('jam');
                $maxStok = DB::table('riwayat_barang_medis')->where('kode_brng', $obat)->where('kd_bangsal', 'FARM')->where('tanggal', $maxTgl)->where('jam', $maxJam)->max('stok_akhir');

                if ($maxStok < 1) {
                    if (empty($obat)) {
                        return response()->json([
                            'status' => 'gagal',
                            'pesan' => 'Obat tidak boleh kosong'
                        ]);
                    } else {
                        $dataBarang = DB::table('databarang')->where('kode_brng', $obat)->first();
                        return response()->json([
                            'status' => 'gagal',
                            'pesan' => 'Stok obat ' . $dataBarang->nama_brng . ' kosong'
                        ]);
                    }
                }
                $resep = DB::table('resep_obat')->where('no_rawat', $noRawat)->first();
                $no = DB::table('resep_obat')->where('tgl_perawatan', 'like', '%' . date('Y-m-d') . '%')->orWhere('tgl_peresepan', 'like', '%' . date('Y-m-d') . '%')->selectRaw("ifnull(MAX(CONVERT(RIGHT(no_resep,4),signed)),0) as resep")->first();
                $maxNo = substr($no->resep, 0, 4);
                $nextNo = sprintf('%04s', ($maxNo + 1));
                $tgl = date('Ymd');
                $noResep = $tgl . '' . $nextNo;

                if ($resep) {
                    DB::table('resep_dokter')->insert([
                        'no_resep' => $resep->no_resep,
                        'kode_brng' => $obat,
                        'jml' => $jml,
                        'aturan_pakai' => $aturan,
                    ]);
                } else {
                    DB::table('resep_obat')->insert([
                        'no_resep' => $noResep,
                        'tgl_perawatan' => $tgl,
                        'jam' => date('H:i:s'),
                        'no_rawat' => $noRawat,
                        'kd_dokter' => $dokter,
                        'tgl_peresepan' => $tgl,
                        'jam_peresepan' => date('H:i:s'),
                        'status' => 'Ranap',
                    ]);
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
            return response()->json([
                'status' => 'gagal',
                'pesan' => $ex->getMessage()
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
            ->where('gudangbarang.kd_bangsal', 'B0007')
            ->where(function ($query) use ($que) {
                $query->where('databarang.kode_brng', 'like', $que)
                    ->orWhere('databarang.nama_brng', 'like', $que);
            })
            ->selectRaw('gudangbarang.kode_brng AS id, databarang.nama_brng AS text')
            ->get();
        return response()->json($obat, 200);
    }
    
    
    public static function getobatRalan($noRM)
    {
        
        $dataobat = DB::table('detail_pemberian_obat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->where('detail_pemberian_obat.no_rawat', $noRM)
            ->where('detail_pemberian_obat.status', 'Ralan')
            ->select('databarang.nama_brng', 'detail_pemberian_obat.jml','detail_pemberian_obat.kode_brng','detail_pemberian_obat.tgl_perawatan','detail_pemberian_obat.jam')
            ->get();

        foreach($dataobat as $obat) {
            $aturan = DB::table('aturan_pakai')
                ->where('kode_brng', $obat->kode_brng)
                ->where('no_rawat', $noRM)
                ->value('aturan');
            $obat->aturan = $aturan ?? '-';
        }

        return $dataobat;
    }

    public static function getobatRanap($noRM)
    {
        $dataobat = DB::table('detail_pemberian_obat')
            ->join('databarang', 'detail_pemberian_obat.kode_brng', '=', 'databarang.kode_brng')
            ->where('detail_pemberian_obat.no_rawat', $noRM)
            ->where('detail_pemberian_obat.status', 'Ranap')
            ->select('databarang.nama_brng', 'detail_pemberian_obat.jml','detail_pemberian_obat.kode_brng','detail_pemberian_obat.tgl_perawatan','detail_pemberian_obat.jam')
            ->orderBy('detail_pemberian_obat.tgl_perawatan', 'desc')
            ->get();

        foreach($dataobat as $obat) {
            $aturan = DB::table('aturan_pakai')
                ->where('kode_brng', $obat->kode_brng)
                ->where('no_rawat', $noRM)
                ->value('aturan');
            $obat->aturan = $aturan ?? '-';
        }

        return $dataobat;
    }

    public static function getPemeriksaanRanap($noRawat, $status)
    {
        if ($status == 'Ralan') {
            $data = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $noRawat)
                ->get();
        } else {
            $data = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $noRawat)
                ->get();
        }
        return $data;
    }

    public static function getDiagnosa($noRawat)
    {
        $data = DB::table('diagnosa_pasien')
            ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
            ->where('diagnosa_pasien.no_rawat', $noRawat)
            ->select('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
            ->get();
        return $data;
    }

    public static function getPemeriksaanObstetri($noRawat)
    {
        $data = DB::table('pemeriksaan_obstetri_ralan')
            ->where('no_rawat', $noRawat)
            ->first();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postPemeriksaan(Request $request)
    {
        try {
            // Dekripsi no_rawat
            $encryptedNoRawat = Request::get('no_rawat');
            $decodedNoRawat = $this->decryptData($encryptedNoRawat);
            
            // Validasi input
            $validate = Request::validate([
                'tensi' => 'required',
                'kesadaran' => 'required',
                'penilaian' => 'required',
                'instruksi' => 'required',
            ]);
            
            // Generate unique timestamp untuk jam_rawat
            // Ini akan memastikan setiap entri memiliki jam_rawat yang unik
            // bahkan jika ada beberapa input dalam waktu yang sama
            $timestamp = date('H:i:s');
            
            // Cek apakah sudah ada pemeriksaan hari ini dengan jam yang sama
            $cek = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $decodedNoRawat)
                ->where('tgl_perawatan', date('Y-m-d'))
                ->where('jam_rawat', $timestamp)
                ->count();
            
            // Jika ada entri dengan timestamp yang sama, tambahkan beberapa milidetik
            if ($cek > 0) {
                $timestamp = date('H:i:s', strtotime('+1 second'));
            }
            
            $data = [
                'no_rawat' => $decodedNoRawat,
                'nip' => session()->get('username'),
                'tgl_perawatan' => date('Y-m-d'),
                'jam_rawat' => $timestamp,
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
                'rtl' => Request::get('rtl'),
                'penilaian' => Request::get('penilaian'),
                'instruksi' => Request::get('instruksi'),
                'spo2' => Request::get('spo2'),
                'evaluasi' => Request::get('evaluasi'),
            ];
            
            DB::beginTransaction();
            
            try {
                // Selalu insert data baru untuk menghindari konflik
                $insert = DB::table('pemeriksaan_ranap')
                    ->insert($data);
                
                DB::commit();
                
                // Ambil data yang baru saja dimasukkan untuk verifikasi
                $newData = DB::table('pemeriksaan_ranap')
                    ->where('no_rawat', $decodedNoRawat)
                    ->where('tgl_perawatan', date('Y-m-d'))
                    ->where('jam_rawat', $timestamp)
                    ->first();
                
                return response()->json([
                    'status' => 'success',
                    'message' => 'Data berhasil disimpan',
                    'timestamp' => now()->timestamp, // Tambahkan timestamp untuk memastikan data terbaru
                    'data' => $newData ? [
                        'tgl_perawatan' => $newData->tgl_perawatan,
                        'jam_rawat' => $newData->jam_rawat
                    ] : null
                ], 200);
                
            } catch (\Exception $innerEx) {
                DB::rollBack();
                throw $innerEx;
            }
            
        } catch (\Exception $e) {
            if (DB::transactionLevel() > 0) {
                DB::rollBack();
            }
            
            \Illuminate\Support\Facades\Log::error('Error menyimpan pemeriksaan: ' . $e->getMessage(), [
                'exception' => $e,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat menyimpan data',
                'error_details' => env('APP_DEBUG') ? $e->getMessage() : null
            ], 500);
        }
    }

    public  function getPemeriksaan($noRawat, $tgl, $jam)
    {
        $no_rawat =  $this->decryptData($noRawat);
        try {
            $data = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $no_rawat)
                ->where('tgl_perawatan', $tgl)
                ->where('jam_rawat', $jam)
                ->first();
            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function editPemeriksaan($noRawat, $tgl, $jam)
    {
        $no_rawat =  $this->decryptData($noRawat);
        $data = [
            'nip' => session()->get('username'),
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
            'rtl' => Request::get('rtl'),
            'penilaian' => Request::get('penilaian'),
            'instruksi' => Request::get('instruksi'),
            'spo2' => Request::get('spo2'),
            'evaluasi' => Request::get('evaluasi'),
        ];
        try {
            $update = DB::table('pemeriksaan_ranap')
                ->where('no_rawat', $no_rawat)
                ->where('tgl_perawatan', $tgl)
                ->where('jam_rawat', $jam)
                ->update($data);

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil diubah'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public static function getPemeriksaanLab($noRawat)
    {
        $data = DB::table('detail_periksa_lab')
            ->join('template_laboratorium', 'detail_periksa_lab.id_template', '=', 'template_laboratorium.id_template')
            ->where('detail_periksa_lab.no_rawat', $noRawat)
            ->select('template_laboratorium.Pemeriksaan', 'detail_periksa_lab.tgl_periksa', 'detail_periksa_lab.jam', 'detail_periksa_lab.nilai', 'template_laboratorium.satuan', 'detail_periksa_lab.nilai_rujukan', 'detail_periksa_lab.keterangan')
            ->orderBy('detail_periksa_lab.tgl_periksa', 'desc')
            ->get();
        return $data;
    }

    public static function getResume($noRM)
    {
        return DB::table('resume_pasien')
            ->where('no_rawat', $noRM)
            ->limit(5)
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
}
