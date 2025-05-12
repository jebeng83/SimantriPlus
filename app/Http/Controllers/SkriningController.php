<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\SkriningPkg;

class SkriningController extends Controller
{
    /**
     * Menampilkan form skrining kesehatan minimal
     */
    public function index()
    {
        return view('form-skrining-minimal');
    }

    /**
     * Menyimpan data skrining demografi
     */
    public function simpanDemografi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_rkm_medis' => 'nullable|string|max:50',
            'nik' => 'required|string|max:25',
            'status_perkawinan' => 'required|string|in:Belum Menikah,Menikah,Cerai Mati,Cerai Hidup',
            'rencana_menikah' => 'nullable|string|in:Ya,Tidak',
            'status_hamil' => 'nullable|string|in:Ya,Tidak',
            'status_disabilitas' => 'nullable|string|in:Non disabilitas,Penyandang disabilitas',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data demografi
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->status_perkawinan = $request->status_perkawinan;
            $skrining->rencana_menikah = $request->rencana_menikah;
            $skrining->status_hamil = $request->status_hamil;
            $skrining->status_disabilitas = $request->status_disabilitas;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data demografi berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data demografi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data tekanan darah
     */
    public function simpanTekananDarah(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'riwayat_hipertensi' => 'required|string|in:Ya,Tidak',
            'riwayat_diabetes' => 'required|string|in:Ya,Tidak',
            'tekanan_sistolik' => 'nullable|integer',
            'tekanan_diastolik' => 'nullable|integer',
            'gds' => 'nullable|numeric',
            'gdp' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data tekanan darah
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->riwayat_hipertensi = $request->riwayat_hipertensi;
            $skrining->riwayat_diabetes = $request->riwayat_diabetes;
            $skrining->tekanan_sistolik = $request->tekanan_sistolik;
            $skrining->tekanan_diastolik = $request->tekanan_diastolik;
            $skrining->gds = $request->gds;
            $skrining->gdp = $request->gdp;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tekanan darah berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data tekanan darah',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data perilaku merokok
     */
    public function simpanPerilakuMerokok(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'status_merokok' => 'required|string|in:Ya,Tidak',
            'lama_merokok' => 'nullable|required_if:status_merokok,Ya|integer',
            'jumlah_rokok' => 'nullable|required_if:status_merokok,Ya|integer',
            'paparan_asap' => 'nullable|string|in:Ya,Tidak',
            'riwayat_merokok' => 'nullable|string|in:Ya,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data perilaku merokok
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->status_merokok = $request->status_merokok;
            $skrining->lama_merokok = $request->status_merokok === 'Ya' ? $request->lama_merokok : null;
            $skrining->jumlah_rokok = $request->status_merokok === 'Ya' ? $request->jumlah_rokok : null;
            $skrining->paparan_asap = $request->paparan_asap;
            $skrining->riwayat_merokok = $request->riwayat_merokok;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data perilaku merokok berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data perilaku merokok: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data perilaku merokok',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data kesehatan jiwa
     */
    public function simpanKesehatanJiwa(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'minat' => 'nullable|string|max:50',
            'sedih' => 'nullable|string|max:50',
            'cemas' => 'nullable|string|max:50',
            'khawatir' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data kesehatan jiwa
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->minat = $request->minat;
            $skrining->sedih = $request->sedih;
            $skrining->cemas = $request->cemas;
            $skrining->khawatir = $request->khawatir;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kesehatan jiwa berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data kesehatan jiwa',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data hati
     */
    public function simpanHati(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'riwayat_hepatitis' => 'nullable|string|in:Ya,Tidak',
            'riwayat_kuning' => 'nullable|string|in:Ya,Tidak',
            'riwayat_transfusi' => 'nullable|string|in:Ya,Tidak',
            'riwayat_tattoo' => 'nullable|string|in:Ya,Tidak',
            'riwayat_tindik' => 'nullable|string|in:Ya,Tidak',
            'narkoba_suntik' => 'nullable|string|in:Ya,Tidak',
            'odhiv' => 'nullable|string|in:Ya,Tidak',
            'kolesterol' => 'nullable|string|in:Ya,Tidak',
            'hubungan_intim' => 'nullable|string|in:Ya,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data hati
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->riwayat_hepatitis = $request->riwayat_hepatitis;
            $skrining->riwayat_kuning = $request->riwayat_kuning;
            $skrining->riwayat_transfusi = $request->riwayat_transfusi;
            $skrining->riwayat_tattoo = $request->riwayat_tattoo;
            $skrining->riwayat_tindik = $request->riwayat_tindik;
            $skrining->narkoba_suntik = $request->narkoba_suntik;
            $skrining->odhiv = $request->odhiv;
            $skrining->kolesterol = $request->kolesterol;
            $skrining->hubungan_intim = $request->hubungan_intim;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data hati berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data hati',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data kanker leher rahim
     */
    public function simpanKankerLeherRahim(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'hubungan_intim' => 'nullable|string|in:Ya,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data hubungan intim
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->hubungan_intim = $request->hubungan_intim;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data kanker leher rahim berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data kanker leher rahim: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data kanker leher rahim',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data aktivitas fisik
     */
    public function simpanAktivitasFisik(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'frekuensi_olahraga' => 'nullable|string|max:50',
            'durasi_olahraga' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data aktivitas fisik
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->frekuensi_olahraga = $request->frekuensi_olahraga;
            $skrining->durasi_olahraga = $request->durasi_olahraga;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data aktivitas fisik berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data aktivitas fisik: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data aktivitas fisik',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data tuberkulosis
     */
    public function simpanTuberkulosis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'batuk_berdahak' => 'nullable|string|in:Ya,Tidak',
            'demam' => 'nullable|string|in:Ya,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data tuberkulosis
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->batuk = $request->batuk_berdahak;
            $skrining->demam = $request->demam;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tuberkulosis berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data tuberkulosis: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data tuberkulosis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data antropometri dan laboratorium
     */
    public function simpanAntropometriLab(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'tinggi_badan' => 'nullable|numeric',
            'berat_badan' => 'nullable|numeric',
            'lingkar_perut' => 'nullable|numeric',
            'tekanan_sistolik' => 'nullable|integer',
            'tekanan_diastolik' => 'nullable|integer',
            'gds' => 'nullable|numeric',
            'gdp' => 'nullable|numeric',
            'kolesterol' => 'nullable|numeric',
            'trigliserida' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data antropometri dan laboratorium
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->tinggi_badan = $request->tinggi_badan ?: null;
            $skrining->berat_badan = $request->berat_badan ?: null;
            $skrining->lingkar_perut = $request->lingkar_perut ?: null;
            $skrining->tekanan_sistolik = $request->tekanan_sistolik ?: null;
            $skrining->tekanan_diastolik = $request->tekanan_diastolik ?: null;
            
            // Atur nilai default ke 0 untuk mencegah error validasi
            $skrining->gds = $request->gds !== null && $request->gds !== '' ? $request->gds : 0;
            $skrining->gdp = $request->gdp !== null && $request->gdp !== '' ? $request->gdp : 0;
            $skrining->kolesterol_lab = $request->kolesterol !== null && $request->kolesterol !== '' ? $request->kolesterol : 0;
            $skrining->trigliserida = $request->trigliserida !== null && $request->trigliserida !== '' ? $request->trigliserida : 0;
            
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data antropometri dan laboratorium berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data antropometri dan laboratorium: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data antropometri dan laboratorium',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data skrining indra
     */
    public function simpanSkriningIndra(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'pendengaran' => 'nullable|string|in:Normal,Gangguan pendengaran',
            'penglihatan' => 'nullable|string|in:Normal,Menggunakan Kacamata',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data skrining indra
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->pendengaran = $request->pendengaran;
            $skrining->penglihatan = $request->penglihatan;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining indra berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data skrining indra: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining indra',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data skrining gigi
     */
    public function simpanSkriningGigi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'karies' => 'nullable|string|in:Ya,Tidak',
            'hilang' => 'nullable|string|in:Ya,Tidak',
            'goyang' => 'nullable|string|in:Ya,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                // Jika belum ada, buat baru
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data skrining gigi
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->karies = $request->karies;
            $skrining->hilang = $request->hilang;
            $skrining->goyang = $request->goyang;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining gigi berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data skrining gigi: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining gigi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validasi NIK untuk memastikan hanya dapat mengisi form 1 kali dalam 1 tahun
     */
    private function validasiNikTahunan($nik)
    {
        // Cari data skrining dengan NIK yang sama
        $skrining = SkriningPkg::where('nik', $nik)
            ->where('tanggal_skrining', '>=', now()->subYear())
            ->first();
        
        \Log::info('Validasi NIK tahunan: ' . $nik, [
            'status' => $skrining ? 'sudah pernah skrining' : 'belum pernah skrining',
            'tanggal_skrining' => $skrining ? $skrining->tanggal_skrining : null
        ]);
        
        // Jika data ditemukan, berarti sudah mengisi dalam 1 tahun terakhir
        if ($skrining) {
            return [
                'status' => false,
                'message' => 'NIK ini sudah melakukan skrining dalam 1 tahun terakhir',
                'data' => $skrining
            ];
        }
        
        // Jika tidak ditemukan, berarti belum pernah mengisi dalam 1 tahun terakhir
        return [
            'status' => true,
            'message' => 'NIK valid untuk skrining tahun ini',
            'data' => null
        ];
    }

    /**
     * Simpan data skrining secara lengkap (semua bagian)
     */
    public function simpanSkrining(Request $request)
    {
        try {
            \Log::info('Permintaan simpanSkrining diterima:', $request->all());

            // Validasi data dasar
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'nama_lengkap' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
            ]);

            if ($validator->fails()) {
                \Log::warning('Validasi gagal pada simpanSkrining:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            // Data identitas
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            if (!$skrining) {
                \Log::info('Membuat data skrining baru untuk NIK: ' . $request->nik);
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                // Gunakan no_rkm_medis dari tabel pasien jika ditemukan
                $skrining->no_rkm_medis = $no_rkm_medis;
                $skrining->tanggal_skrining = date('Y-m-d');
            } else {
                \Log::info('Update data skrining yang sudah ada untuk NIK: ' . $request->nik);
                // Update no_rkm_medis jika ditemukan di tabel pasien
                if ($no_rkm_medis) {
                    $skrining->no_rkm_medis = $no_rkm_medis;
                }
            }
            
            // Update semua data yang tersedia dari request
            $fillableFields = $skrining->getFillable();
            
            foreach ($fillableFields as $field) {
                if ($request->has($field) && $field !== 'id_pkg') {
                    $skrining->{$field} = $request->{$field};
                }
            }
            
            // Simpan data
            $skrining->save();
            \Log::info('Data skrining berhasil disimpan untuk NIK: ' . $request->nik . ', ID: ' . $skrining->id_pkg);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal menyimpan data skrining: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Debug endpoint untuk testing
     */
    public function debug(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'message' => 'Debug endpoint berhasil',
            'csrf_token' => csrf_token(),
            'headers' => $request->headers->all(),
            'method' => $request->method(),
            'request_data' => $request->all()
        ]);
    }
    
    /**
     * Cek NIK untuk validasi dan mendapatkan data form yang sudah diisi
     */
    public function cekNikSkrining(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            \Log::info('Cek NIK Skrining: ' . $request->nik);
            
            // Cari data skrining dengan NIK yang sama
            $skrining = SkriningPkg::where('nik', $request->nik)->first();
            
            // Validasi apakah sudah mengisi dalam 1 tahun terakhir
            $validasi = $this->validasiNikTahunan($request->nik);
            
            // Jika sudah mengisi dalam 1 tahun terakhir
            if (!$validasi['status']) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validasi['message'],
                    'allow_update' => true, // Izinkan update/edit data
                    'data' => $validasi['data']
                ]);
            }
            
            // Jika belum mengisi dalam 1 tahun terakhir tapi data NIK ada
            if ($skrining) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Data ditemukan dan dapat diisi kembali tahun ini',
                    'data' => $skrining
                ]);
            }
            
            // Jika belum pernah mengisi sama sekali
            return response()->json([
                'status' => 'success',
                'message' => 'NIK valid untuk skrining',
                'data' => null
            ]);
        } catch (\Exception $e) {
            \Log::error('Gagal memeriksa NIK skrining: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memeriksa NIK',
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 