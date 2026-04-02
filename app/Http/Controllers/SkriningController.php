<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
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
            'status_hamil' => 'nullable|string|in:Ya,Tidak',
            'status_disabilitas' => 'nullable|string|in:Non disabilitas,Penyandang disabilitas',
            'kode_posyandu' => 'nullable|string|max:100',
            'petugas_entri' => 'nullable|string|max:100',
            'status_petugas' => 'nullable|string|in:CKG Umum,Kunjungan Rumah,Tindak Lanjut Posyandu,Lainnya',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            
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
            $skrining->status_hamil = $request->status_hamil;
            $skrining->status_disabilitas = $request->status_disabilitas;
            if (Schema::hasColumn('skrining_pkg', 'kode_posyandu')) {
                $skrining->kode_posyandu = $request->kode_posyandu;
            }
            if (Schema::hasColumn('skrining_pkg', 'petugas_entri')) {
                $skrining->petugas_entri = $request->petugas_entri;
            }
            if (Schema::hasColumn('skrining_pkg', 'status_petugas')) {
                $allowedStatusDb = ['Pegawai', 'Kader', 'Mahasiswa', 'Lainnya'];
                if (in_array($request->status_petugas, $allowedStatusDb)) {
                    $skrining->status_petugas = $request->status_petugas;
                } else {
                    $skrining->status_petugas = null;
                }
            }
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            
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
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data perilaku merokok: ' . $e->getMessage(), [
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            'kolesterol' => 'nullable|string',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data kanker leher rahim: ' . $e->getMessage(), [
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
        $rules = [
            'nik' => 'required|string|max:25',
            'frekuensi_olahraga' => 'required|string|in:Ya,Tidak',
            'frekuensi_olahraga_1' => 'nullable|integer|required_if:frekuensi_olahraga,Ya',
            'frekuensi_olahraga_2' => 'nullable|integer|required_if:frekuensi_olahraga,Ya',
        ];

        // Add rules for questions 2-6
        for ($i = 2; $i <= 6; $i++) {
            $rules["aktivitas_fisik_{$i}"] = 'required|string|in:Ya,Tidak';
            $rules["aktivitas_fisik_{$i}_hari"] = "nullable|integer|required_if:aktivitas_fisik_{$i},Ya";
            $rules["aktivitas_fisik_{$i}_menit"] = "nullable|integer|required_if:aktivitas_fisik_{$i},Ya";
        }

        $validator = Validator::make($request->all(), $rules);

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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Simpan Q1
            $skrining->frekuensi_olahraga = $request->frekuensi_olahraga;
            if ($request->frekuensi_olahraga === 'Ya') {
                $skrining->frekuensi_olahraga_1 = $request->frekuensi_olahraga_1;
                $skrining->frekuensi_olahraga_2 = $request->frekuensi_olahraga_2;
            } else {
                $skrining->frekuensi_olahraga_1 = null;
                $skrining->frekuensi_olahraga_2 = null;
            }

            // Simpan Q2-Q6
            for ($i = 2; $i <= 6; $i++) {
                $fieldUtama = "aktivitas_fisik_{$i}";
                $fieldHari = "aktivitas_fisik_{$i}_hari";
                $fieldMenit = "aktivitas_fisik_{$i}_menit";

                $skrining->$fieldUtama = $request->$fieldUtama;
                if ($request->$fieldUtama === 'Ya') {
                    $skrining->$fieldHari = $request->$fieldHari;
                    $skrining->$fieldMenit = $request->$fieldMenit;
                } else {
                    $skrining->$fieldHari = null;
                    $skrining->$fieldMenit = null;
                }
            }
            
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data aktivitas fisik berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data aktivitas fisik: ' . $e->getMessage(), [
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
            'riwayat_tbc' => 'required|string|in:Riwayat kontak serumah,Riwayat kontak erat,Tidak ada,Tidak diketahui',
            'jenis_tbc' => 'nullable|string|in:Bakteriologis,Klinis|required_if:riwayat_tbc,Riwayat kontak serumah,Riwayat kontak erat',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            $skrining->riwayat_tbc = $request->riwayat_tbc;
            $skrining->jenis_tbc = $request->jenis_tbc;
            $skrining->batuk = $request->batuk_berdahak;
            $skrining->demam = $request->demam;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tuberkulosis berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data tuberkulosis: ' . $e->getMessage(), [
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
            'riwayat_dm' => 'required|in:Ya,Tidak',
            'lama_riwayat_dm_dewasa' => 'nullable|required_if:riwayat_dm,Ya|integer|min:0',
            'riwayat_ht' => 'required|in:Ya,Tidak',
            'lama_riwayat_ht_dewasa' => 'nullable|required_if:riwayat_ht,Ya|integer|min:0',
            'tinggi_badan' => 'nullable|numeric',
            'berat_badan' => 'nullable|numeric',
            'lingkar_perut' => 'nullable|numeric',
            'tekanan_sistolik' => 'nullable|integer',
            'tekanan_diastolik' => 'nullable|integer',
            'tekanan_sistolik_2' => 'nullable|integer',
            'tekanan_diastolik_2' => 'nullable|integer',
            'gds' => 'nullable|numeric',
            'gdp' => 'nullable|numeric',
            'kolesterol_lab' => 'nullable|numeric',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            $skrining->riwayat_dm = $request->riwayat_dm;
            $skrining->lama_riwayat_dm_dewasa = $request->riwayat_dm === 'Ya'
                ? ($request->lama_riwayat_dm_dewasa !== null && $request->lama_riwayat_dm_dewasa !== '' ? $request->lama_riwayat_dm_dewasa : null)
                : null;
            $skrining->riwayat_ht = $request->riwayat_ht;
            $skrining->lama_riwayat_ht_dewasa = $request->riwayat_ht === 'Ya'
                ? ($request->lama_riwayat_ht_dewasa !== null && $request->lama_riwayat_ht_dewasa !== '' ? $request->lama_riwayat_ht_dewasa : null)
                : null;
            $skrining->tinggi_badan = $request->tinggi_badan ?: null;
            $skrining->berat_badan = $request->berat_badan ?: null;
            $skrining->lingkar_perut = $request->lingkar_perut ?: null;
            $skrining->tekanan_sistolik = $request->tekanan_sistolik ?: null;
            $skrining->tekanan_diastolik = $request->tekanan_diastolik ?: null;
            $skrining->tekanan_sistolik_2 = $request->tekanan_sistolik_2 ?: null;
            $skrining->tekanan_diastolik_2 = $request->tekanan_diastolik_2 ?: null;
            
            // Atur nilai default ke 0 untuk mencegah error validasi
            $skrining->gds = $request->gds !== null && $request->gds !== '' ? $request->gds : 0;
            $skrining->gdp = $request->gdp !== null && $request->gdp !== '' ? $request->gdp : 0;
            $skrining->kolesterol_lab = $request->kolesterol_lab !== null && $request->kolesterol_lab !== '' ? $request->kolesterol_lab : 0;
            $skrining->trigliserida = $request->trigliserida !== null && $request->trigliserida !== '' ? $request->trigliserida : 0;
            
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data antropometri dan laboratorium berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data antropometri dan laboratorium: ' . $e->getMessage(), [
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
            'hasil_serumen' => 'required|string|in:Tidak ada serumen impaksi,Ada serumen impaksi',
            'hasil_infeksi_telinga' => 'required|string|in:Tidak ada infeksi telinga,Ada infeksi telinga',
            'pendengaran' => 'required|string|in:Normal,Curiga gangguan pendengaran',
            'penglihatan' => 'required|string|in:Normal (visus 6/6 - 6/12),Curiga gangguan penglihatan (visus <6/12)',
            'pupil' => 'required|string|in:Curiga Katarak,Normal',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            $skrining->hasil_serumen = $request->hasil_serumen;
            $skrining->hasil_infeksi_telinga = $request->hasil_infeksi_telinga;
            $skrining->pendengaran = $request->pendengaran;
            $skrining->penglihatan = $request->penglihatan;
            if (Schema::hasColumn('skrining_pkg', 'pupil')) {
                $skrining->pupil = $request->pupil;
            } elseif (Schema::hasColumn('skrining_pkg', 'selaput_mata')) {
                // fallback kompatibilitas skema lama
                $skrining->selaput_mata = $request->pupil === 'Curiga Katarak' ? 'Curiga kelainan mata' : 'Normal';
            }
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining indra berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data skrining indra: ' . $e->getMessage(), [
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
            'jumlah_karies' => 'required|string|in:Tidak ada,1,2,3,> 3',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            $skrining->jumlah_karies = $request->jumlah_karies;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining gigi berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data skrining gigi: ' . $e->getMessage(), [
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
     * Menyimpan data gangguan fungsional/barthel index
     */
    public function simpanGangguanFungsional(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'bab' => 'nullable|string|max:100',
            'bak' => 'nullable|string|max:100',
            'membersihkan_diri' => 'nullable|string|max:100',
            'penggunaan_jamban' => 'nullable|string|max:100',
            'makan_minum' => 'nullable|string|max:100',
            'berubah_sikap' => 'nullable|string|max:100',
            'berpindah' => 'nullable|string|max:100',
            'memakai_baju' => 'nullable|string|max:100',
            'naik_tangga' => 'nullable|string|max:100',
            'mandi' => 'nullable|string|max:100',
            'total_skor_barthel' => 'nullable|integer',
            'tingkat_ketergantungan' => 'nullable|string|max:50',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data gangguan fungsional
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->bab = $request->bab;
            $skrining->bak = $request->bak;
            $skrining->membersihkan_diri = $request->membersihkan_diri;
            $skrining->penggunaan_jamban = $request->penggunaan_jamban;
            $skrining->makan_minum = $request->makan_minum;
            $skrining->berubah_sikap = $request->berubah_sikap;
            $skrining->berpindah = $request->berpindah;
            $skrining->memakai_baju = $request->memakai_baju;
            $skrining->naik_tangga = $request->naik_tangga;
            $skrining->mandi = $request->mandi;
            $skrining->total_skor_barthel = $request->total_skor_barthel;
            $skrining->tingkat_ketergantungan = $request->tingkat_ketergantungan;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data gangguan fungsional berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data gangguan fungsional: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data gangguan fungsional',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data skrining PUMA
     */
    public function simpanSkriningPuma(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'riwayat_merokok' => 'nullable|string|in:Ya,Tidak',
            'napas_pendek' => 'nullable|string|in:Ya,Tidak',
            'dahak' => 'nullable|string|in:Ya,Tidak',
            'batuk' => 'nullable|string|in:Ya,Tidak',
            'spirometri' => 'nullable|string|in:Ya,Tidak',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data skrining PUMA
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->riwayat_merokok = $request->riwayat_merokok;
            $skrining->napas_pendek = $request->napas_pendek;
            $skrining->dahak = $request->dahak;
            $skrining->batuk = $request->batuk;
            $skrining->spirometri = $request->spirometri;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining PUMA berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data skrining PUMA: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining PUMA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Validasi NIK untuk memastikan hanya dapat mengisi form 1 kali dalam 1 tahun
     */
    private function validasiNikTahunan($nik)
    {
        $skriningTahunIni = SkriningPkg::where('nik', $nik)
            ->whereYear('tanggal_skrining', now()->year)
            ->orderByDesc('updated_at')
            ->orderByDesc('id_pkg')
            ->first();
        \Illuminate\Support\Facades\Log::info('Validasi NIK tahunan: ' . $nik, [
            'status' => $skriningTahunIni ? 'sudah pernah skrining' : 'belum pernah skrining',
            'tanggal_skrining' => $skriningTahunIni ? $skriningTahunIni->tanggal_skrining : null
        ]);
        if ($skriningTahunIni) {
            return [
                'status' => false,
                'message' => 'NIK ini sudah melakukan skrining pada tahun ini',
                'data' => $skriningTahunIni
            ];
        }
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
            \Illuminate\Support\Facades\Log::info('Permintaan simpanSkrining diterima:', $request->all());

            // Validasi data dasar
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'nama_lengkap' => 'required|string|max:100',
                'tanggal_lahir' => 'required|date',
                'jenis_kelamin' => 'required|in:L,P',
            ]);

            if ($validator->fails()) {
                \Illuminate\Support\Facades\Log::warning('Validasi gagal pada simpanSkrining:', $validator->errors()->toArray());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            // Cari no_rkm_medis berdasarkan NIK di tabel pasien
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;

            // Sinkronkan perubahan identitas ke tabel pasien jika data pasien ditemukan
            if ($pasien) {
                $updatePasien = [];
                if (Schema::hasColumn('pasien', 'nm_pasien')) {
                    $updatePasien['nm_pasien'] = $request->nama_lengkap;
                }
                if (Schema::hasColumn('pasien', 'tgl_lahir')) {
                    $updatePasien['tgl_lahir'] = $request->tanggal_lahir;
                }
                if (Schema::hasColumn('pasien', 'jk')) {
                    $updatePasien['jk'] = $request->jenis_kelamin;
                }
                if (Schema::hasColumn('pasien', 'no_tlp')) {
                    $updatePasien['no_tlp'] = $request->no_handphone;
                }

                if (!empty($updatePasien)) {
                    DB::table('pasien')->where('no_ktp', $request->nik)->update($updatePasien);
                }
            }
            
            // Data identitas
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            
            if (!$skrining) {
                \Illuminate\Support\Facades\Log::info('Membuat data skrining baru untuk NIK: ' . $request->nik);
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
                \Illuminate\Support\Facades\Log::info('Update data skrining yang sudah ada untuk NIK: ' . $request->nik);
                // Update no_rkm_medis jika ditemukan di tabel pasien
                if ($no_rkm_medis) {
                    $skrining->no_rkm_medis = $no_rkm_medis;
                }
                
                // Update data identitas dasar
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
            }
            
            // Update semua data yang tersedia dari request
            $fillableFields = $skrining->getFillable();
            $existingColumns = Schema::getColumnListing($skrining->getTable());

            // Gangguan Fungsional / Barthel Index
            if ($request->has('bab')) {
                $skrining->bab = $request->bab;
                $skrining->bak = $request->bak;
                $skrining->membersihkan_diri = $request->membersihkan_diri;
                $skrining->penggunaan_jamban = $request->penggunaan_jamban;
                $skrining->makan_minum = $request->makan_minum;
                $skrining->berubah_sikap = $request->berubah_sikap;
                $skrining->berpindah = $request->berpindah;
                $skrining->memakai_baju = $request->memakai_baju;
                $skrining->naik_tangga = $request->naik_tangga;
                $skrining->mandi = $request->mandi;
                
                // Hitung total skor dan tingkat ketergantungan jika belum ada
                if (!$request->has('total_skor_barthel') || !$request->has('tingkat_ketergantungan')) {
                    $totalRaw = 0;
                    if ($request->bab) $totalRaw += (int)$request->bab;
                    if ($request->bak) $totalRaw += (int)$request->bak;
                    if ($request->membersihkan_diri) $totalRaw += (int)$request->membersihkan_diri;
                    if ($request->penggunaan_jamban) $totalRaw += (int)$request->penggunaan_jamban;
                    if ($request->makan_minum) $totalRaw += (int)$request->makan_minum;
                    if ($request->berubah_sikap) $totalRaw += (int)$request->berubah_sikap;
                    if ($request->berpindah) $totalRaw += (int)$request->berpindah;
                    if ($request->memakai_baju) $totalRaw += (int)$request->memakai_baju;
                    if ($request->naik_tangga) $totalRaw += (int)$request->naik_tangga;
                    if ($request->mandi) $totalRaw += (int)$request->mandi;
                    
                    // Kalikan dengan 5 untuk mendapatkan skor total 100
                    $totalFinal = $totalRaw * 5;
                    $skrining->total_skor_barthel = $totalFinal;
                    
                    // Tentukan tingkat ketergantungan berdasarkan skor
                    $tingkatKetergantungan = '';
                    if ($totalFinal >= 0 && $totalFinal <= 20) {
                        $tingkatKetergantungan = 'Ketergantungan Total';
                    } else if ($totalFinal >= 21 && $totalFinal <= 60) {
                        $tingkatKetergantungan = 'Ketergantungan Berat';
                    } else if ($totalFinal >= 61 && $totalFinal <= 90) {
                        $tingkatKetergantungan = 'Ketergantungan Sedang';
                    } else if ($totalFinal >= 91 && $totalFinal <= 99) {
                        $tingkatKetergantungan = 'Ketergantungan Ringan';
                    } else if ($totalFinal == 100) {
                        $tingkatKetergantungan = 'Mandiri';
                    }
                    $skrining->tingkat_ketergantungan = $tingkatKetergantungan;
                }
            }
            
            // Skrining PUMA
            if ($request->has('riwayat_merokok') || $request->has('napas_pendek') || 
                $request->has('dahak') || $request->has('batuk_puma') || $request->has('spirometri')) {
                $skrining->riwayat_merokok = $request->riwayat_merokok;
                $skrining->napas_pendek = $request->napas_pendek;
                $skrining->dahak = $request->dahak;
                $skrining->batuk = $request->batuk_puma ?? $request->batuk; // Menggunakan batuk_puma tetapi menyimpan ke field batuk
                $skrining->spirometri = $request->spirometri;
            }
            
            // Update dengan semua field lainnya yang ada dalam fillable
            foreach ($request->all() as $field => $value) {
                if (in_array($field, $fillableFields) && in_array($field, $existingColumns) && $field !== 'id_pkg') {
                    // Jangan timpa field yang sudah diproses khusus di atas
                    if (!in_array($field, ['bab', 'bak', 'membersihkan_diri', 'penggunaan_jamban', 
                                          'makan_minum', 'berubah_sikap', 'berpindah', 'memakai_baju', 
                                          'naik_tangga', 'mandi', 'total_skor_barthel', 'tingkat_ketergantungan',
                                          'riwayat_merokok', 'napas_pendek', 'dahak', 'batuk', 'spirometri'])) {
                        $skrining->{$field} = $value;
                    }
                }
            }
            
            // Simpan data
            $skrining->save();
            \Illuminate\Support\Facades\Log::info('Data skrining berhasil disimpan untuk NIK: ' . $request->nik . ', ID: ' . $skrining->id_pkg);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data skrining: ' . $e->getMessage(), [
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
     * Menyimpan data demografi anak
     */
    public function simpanDemografiAnak(Request $request)
    {
        // Validasi data identitas anak dan wali
        $rules = [
            'nik' => 'required|string|max:25',
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'no_handphone' => 'nullable|string|max:15',
            'umur' => 'nullable|string',
            'umur_tahun' => 'nullable|integer',
            'status_disabilitas_anak' => 'nullable|string|in:Non disabilitas,Penyandang disabilitas',
        ];

        // Jika anak di bawah 6 tahun, data wali wajib diisi
        if ($request->umur_tahun && $request->umur_tahun < 6) {
            $rules['nik_wali'] = 'required|string|max:25';
            $rules['nama_wali'] = 'required|string|max:100';
            $rules['tanggal_lahir_wali'] = 'required|date';
            $rules['jenis_kelamin_wali'] = 'required|in:L,P';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari data skrining untuk tahun berjalan, buat baru jika belum ada
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Update data identitas anak
            $skrining->nama_lengkap = $request->nama_lengkap;
            $skrining->tanggal_lahir = $request->tanggal_lahir;
            $skrining->jenis_kelamin = $request->jenis_kelamin;
            $skrining->no_handphone = $request->no_handphone;
            $skrining->umur = $request->umur;
            
            // Update data demografi anak
            if ($request->status_disabilitas_anak) {
                $skrining->status_disabilitas_anak = $request->status_disabilitas_anak;
            }
            
            if ($request->umur_tahun && $request->umur_tahun < 6) {
                $skrining->nik_wali = $request->nik_wali;
                $skrining->nama_wali = $request->nama_wali;
                $skrining->tanggal_lahir_wali = $request->tanggal_lahir_wali;
                $skrining->jenis_kelamin_wali = $request->jenis_kelamin_wali;
            } else {
                $skrining->nik_wali = null;
                $skrining->nama_wali = null;
                $skrining->tanggal_lahir_wali = null;
                $skrining->jenis_kelamin_wali = null;
            }
            
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data identitas dan demografi anak berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data demografi anak: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data identitas dan demografi anak',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data gejala DM anak
     */
    public function simpanGejalaDMAnak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'pernah_dm_oleh_dokter' => 'required|string|in:Ya,Tidak',
            'lama_anak_dm' => 'nullable|string|max:4|required_if:pernah_dm_oleh_dokter,Ya',
            // Pertanyaan lanjutan hanya wajib jika jawab "Tidak" pada Q1
            'sering_lapar' => 'nullable|string|in:Ya,Tidak|required_if:pernah_dm_oleh_dokter,Tidak',
            'sering_haus' => 'nullable|string|in:Ya,Tidak|required_if:pernah_dm_oleh_dokter,Tidak',
            'berat_turun' => 'nullable|string|in:Ya,Tidak|required_if:pernah_dm_oleh_dokter,Tidak',
            'riwayat_diabetes_ortu' => 'nullable|string|in:Ya,Tidak|required_if:pernah_dm_oleh_dokter,Tidak',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Cari data skrining untuk tahun berjalan, buat baru jika belum ada
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            // Pertanyaan inti
            $skrining->pernah_dm_oleh_dokter = $request->pernah_dm_oleh_dokter;

            if ($request->pernah_dm_oleh_dokter === 'Ya') {
                $skrining->lama_anak_dm = $request->lama_anak_dm;

                // Reset gejala lanjutan jika sebelumnya pernah terisi
                $skrining->sering_lapar = null;
                $skrining->sering_haus = null;
                $skrining->berat_turun = null;
                $skrining->riwayat_diabetes_ortu = null;
            } else {
                $skrining->lama_anak_dm = null;

                // Simpan jawaban gejala lanjutan jika Q1 = Tidak
                $skrining->sering_lapar = $request->sering_lapar;
                $skrining->sering_haus = $request->sering_haus;
                $skrining->berat_turun = $request->berat_turun;
                $skrining->riwayat_diabetes_ortu = $request->riwayat_diabetes_ortu;
            }
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data gejala DM anak berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data gejala DM anak: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data gejala DM anak',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data riwayat imunisasi rutin balita
     */
    public function simpanRiwayatImunisasiBalita(Request $request)
    {
        $rules = [
            'nik' => 'required|string|max:25',
            'imunisasi_inti' => 'required|string|in:Ya,Tidak',
            'imunisasi_lanjutan' => 'nullable|string|in:Ya,Tidak|required_if:imunisasi_inti,Ya',
        ];

        // Q3-Q20 hanya wajib jika Q1=Ya dan Q2=Ya
        for ($i = 1; $i <= 18; $i++) {
            $rules["imunisasi_lanjutan_{$i}"] = 'nullable|string|in:Sudah,Belum|required_if:imunisasi_lanjutan,Ya';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->imunisasi_inti = $request->imunisasi_inti;

            if ($request->imunisasi_inti === 'Ya') {
                $skrining->imunisasi_lanjutan = $request->imunisasi_lanjutan;

                if ($request->imunisasi_lanjutan === 'Ya') {
                    for ($i = 1; $i <= 18; $i++) {
                        $field = "imunisasi_lanjutan_{$i}";
                        $skrining->$field = $request->$field;
                    }
                } else {
                    for ($i = 1; $i <= 18; $i++) {
                        $field = "imunisasi_lanjutan_{$i}";
                        $skrining->$field = null;
                    }
                }
            } else {
                $skrining->imunisasi_lanjutan = null;
                for ($i = 1; $i <= 18; $i++) {
                    $field = "imunisasi_lanjutan_{$i}";
                    $skrining->$field = null;
                }
            }

            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data riwayat imunisasi rutin balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data riwayat imunisasi balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data riwayat imunisasi rutin balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data riwayat imunisasi Hepatitis B untuk bayi/balita < 1 tahun
     */
    public function simpanHepatitisBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'imunisasi_lanjutan_1' => 'required|in:Sudah,Belum',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->imunisasi_lanjutan_1 = $request->imunisasi_lanjutan_1;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data imunisasi Hepatitis B balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data imunisasi Hepatitis B balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data imunisasi Hepatitis B balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data berat lahir dan berat badan saat ini untuk bayi/balita < 1 tahun
     */
    public function simpanBeratLahirBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'berat_lahir' => 'required|string|max:20',
                'berat_badan_balita' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $beratLahirRaw = str_replace(',', '.', preg_replace('/\s+/', '', (string) $request->berat_lahir));
            if (!is_numeric($beratLahirRaw) || (float) $beratLahirRaw < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['berat_lahir' => ['Berat lahir harus berupa angka yang valid']]
                ], 422);
            }

            // Kolom berat_lahir bertipe decimal(5,0), simpan sebagai angka bulat
            $beratLahir = round((float) $beratLahirRaw);
            if ($beratLahir > 99999) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['berat_lahir' => ['Berat lahir melebihi batas nilai yang diizinkan']]
                ], 422);
            }

            $beratBadanSaatIni = null;
            if ($request->filled('berat_badan_balita')) {
                $beratSaatIniRaw = str_replace(',', '.', preg_replace('/\s+/', '', (string) $request->berat_badan_balita));
                if (!is_numeric($beratSaatIniRaw) || (float) $beratSaatIniRaw < 0) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validasi gagal',
                        'errors' => ['berat_badan_balita' => ['Berat badan saat ini harus berupa angka yang valid']]
                    ], 422);
                }

                $beratBadanSaatIni = (float) $beratSaatIniRaw;

                // Kolom berat_badan_balita decimal(5,2): antisipasi input gram (contoh 4500 => 4.50 kg)
                if ($beratBadanSaatIni > 999.99) {
                    $beratBadanSaatIni = $beratBadanSaatIni / 1000;
                }

                $beratBadanSaatIni = round($beratBadanSaatIni, 2);

                if ($beratBadanSaatIni > 999.99) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Validasi gagal',
                        'errors' => ['berat_badan_balita' => ['Berat badan saat ini melebihi batas nilai yang diizinkan']]
                    ], 422);
                }
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->berat_lahir = $beratLahir;
            $skrining->berat_badan_balita = $beratBadanSaatIni;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berat lahir balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data berat lahir balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data berat lahir balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data pemeriksaan jantung bawaan (PJB) untuk bayi/balita < 1 tahun
     */
    public function simpanPjbBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'pjb_tangan_kanan' => 'required|string|max:10',
                'pjb_kaki' => 'required|string|max:10',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pjbTanganKananRaw = str_replace(',', '.', preg_replace('/\s+/', '', (string) $request->pjb_tangan_kanan));
            $pjbKakiRaw = str_replace(',', '.', preg_replace('/\s+/', '', (string) $request->pjb_kaki));

            if (!is_numeric($pjbTanganKananRaw) || (float) $pjbTanganKananRaw < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['pjb_tangan_kanan' => ['Pemeriksaan PJB tangan kanan harus berupa angka yang valid']]
                ], 422);
            }

            if (!is_numeric($pjbKakiRaw) || (float) $pjbKakiRaw < 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['pjb_kaki' => ['Pemeriksaan PJB kaki harus berupa angka yang valid']]
                ], 422);
            }

            // Kolom pjb_* bertipe decimal(4,0), simpan sebagai angka bulat
            $pjbTanganKanan = round((float) $pjbTanganKananRaw);
            $pjbKaki = round((float) $pjbKakiRaw);

            if ($pjbTanganKanan > 9999) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['pjb_tangan_kanan' => ['Nilai PJB tangan kanan melebihi batas nilai yang diizinkan']]
                ], 422);
            }

            if ($pjbKaki > 9999) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => ['pjb_kaki' => ['Nilai PJB kaki melebihi batas nilai yang diizinkan']]
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->pjb_tangan_kanan = $pjbTanganKanan;
            $skrining->pjb_kaki = $pjbKaki;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pemeriksaan PJB balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data pemeriksaan PJB balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data pemeriksaan PJB balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data pengambilan darah tumit untuk bayi/balita < 1 tahun
     */
    public function simpanDarahTumitBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'darah_tumit' => 'required|in:Ya,Tidak',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->darah_tumit = $request->darah_tumit;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data pengambilan darah tumit balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data pengambilan darah tumit balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data pengambilan darah tumit balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data hasil pemeriksaan SHK, G6PD, HAK untuk bayi/balita < 1 tahun
     */
    public function simpanShkG6pdHakBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'shk' => 'required|in:Positif,Negatif',
                'g6pd' => 'required|in:Positif,Negatif',
                'hak' => 'required|in:Positif,Negatif',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->shk = $request->shk;
            if (Schema::hasColumn('skrining_pkg', 'G6PD')) {
                $skrining->setAttribute('G6PD', $request->g6pd);
            } else {
                $skrining->g6pd = $request->g6pd;
            }
            $skrining->hak = $request->hak;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data hasil pemeriksaan SHK, G6PD, HAK balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data hasil pemeriksaan SHK, G6PD, HAK balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data hasil pemeriksaan SHK, G6PD, HAK balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data tes konfirmasi SHK, G6PD, HAK untuk bayi/balita < 1 tahun
     */
    public function simpanKonfirmasiShkG6pdHakBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'konfirmasi_shk' => 'required|in:Ya,Tidak',
                'konfirmasi_g6pd' => 'required|in:Ya,Tidak',
                'konfirmasi_hak' => 'required|in:Ya,Tidak',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->konfirmasi_shk = $request->konfirmasi_shk;
            $skrining->konfirmasi_g6pd = $request->konfirmasi_g6pd;
            $skrining->konfirmasi_hak = $request->konfirmasi_hak;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tes konfirmasi SHK, G6PD, HAK balita berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data tes konfirmasi SHK, G6PD, HAK balita: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data tes konfirmasi SHK, G6PD, HAK balita',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data edukasi warna kulit dan tinja bayi untuk bayi/balita < 1 tahun
     */
    public function simpanEdukasiWarnaKulitBalita(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'edukasi_warna_kulit' => 'required|in:Ya,Tidak',
                'hasil_kreamer' => 'required|in:Normal,Bayi Kuning Kramer 1-3,Bayi Kuning Kramer >3',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->orderByDesc('updated_at')
                ->orderByDesc('id_pkg')
                ->first();

            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            $skrining->edukasi_warna_kulit = $request->edukasi_warna_kulit;
            $skrining->hasil_kreamer = $request->hasil_kreamer;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data edukasi warna kulit dan tinja bayi berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data edukasi warna kulit dan tinja bayi: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data edukasi warna kulit dan tinja bayi',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data perkembangan anak 3-6 tahun
     */
    public function simpanPerkembangan3_6Tahun(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'gangguan_emosi' => 'required|in:Ya,Tidak',
                'hiperaktif' => 'required|in:Ya,Tidak'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari data skrining untuk tahun berjalan, buat baru jika belum ada
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            // Update data perkembangan 3-6 tahun
            $skrining->gangguan_emosi = $request->gangguan_emosi;
            $skrining->hiperaktif = $request->hiperaktif;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data perkembangan 3-6 tahun berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data perkembangan 3-6 tahun: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data perkembangan 3-6 tahun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data talasemia
     */
    public function simpanTalasemia(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'riwayat_keluarga' => 'required|in:Ya,Tidak',
                'pembawa_sifat' => 'required|in:Ya,Tidak'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari data skrining untuk tahun berjalan, buat baru jika belum ada
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            // Update data talasemia
            $skrining->riwayat_keluarga = $request->riwayat_keluarga;
            $skrining->pembawa_sifat = $request->pembawa_sifat;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data talasemia berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data talasemia: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data talasemia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan data tuberkulosis bayi & anak pra sekolah
     */
    public function simpanTuberkulosisBayiAnak(Request $request)
    {
        try {
            // Validasi input
            $validator = Validator::make($request->all(), [
                'nik' => 'required|string|max:25',
                'batuk_lama' => 'required|in:Ya,Tidak',
                'berat_turun_tbc' => 'required|in:Ya,Tidak',
                'berat_tidak_naik' => 'required|in:Ya,Tidak',
                'nafsu_makan_berkurang' => 'required|in:Ya,Tidak',
                'kontak_tbc' => 'required|in:Ya,Tidak'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validasi gagal',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Cari data skrining untuk tahun berjalan, buat baru jika belum ada
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->tanggal_skrining = date('Y-m-d');
            }

            // Update data tuberkulosis bayi anak
            $skrining->batuk_lama = $request->batuk_lama;
            $skrining->berat_turun_tbc = $request->berat_turun_tbc;
            $skrining->berat_tidak_naik = $request->berat_tidak_naik;
            $skrining->nafsu_makan_berkurang = $request->nafsu_makan_berkurang;
            $skrining->kontak_tbc = $request->kontak_tbc;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data tuberkulosis bayi & anak berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data tuberkulosis bayi anak: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data tuberkulosis bayi & anak',
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
            \Illuminate\Support\Facades\Log::info('Cek NIK Skrining: ' . $request->nik);
            $useSkriningKel = Schema::hasColumn('skrining_pkg', 'kd_kel');

            $qbYear = DB::table('skrining_pkg');
            if (!$useSkriningKel) {
                $qbYear = $qbYear->leftJoin('pasien', 'pasien.no_ktp', '=', 'skrining_pkg.nik');
            }
            $skriningTahunIni = $qbYear
                ->leftJoin('kelurahan', $useSkriningKel ? 'skrining_pkg.kd_kel' : 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
                ->where('skrining_pkg.nik', $request->nik)
                ->whereYear('skrining_pkg.tanggal_skrining', now()->year)
                ->orderByDesc('skrining_pkg.updated_at')
                ->orderByDesc('skrining_pkg.id_pkg')
                ->select('skrining_pkg.*', DB::raw('kelurahan.nm_kel as nm_kel'))
                ->first();

            // Validasi apakah sudah mengisi dalam 1 tahun terakhir
            $validasi = $this->validasiNikTahunan($request->nik);
            
            if (!$validasi['status']) {
                return response()->json([
                    'status' => 'warning',
                    'message' => $validasi['message'],
                    'allow_update' => true,
                    'data' => $skriningTahunIni
                ]);
            }
            $qbPrev = DB::table('skrining_pkg');
            if (!$useSkriningKel) {
                $qbPrev = $qbPrev->leftJoin('pasien', 'pasien.no_ktp', '=', 'skrining_pkg.nik');
            }
            $skriningSebelumnya = $qbPrev
                ->leftJoin('kelurahan', $useSkriningKel ? 'skrining_pkg.kd_kel' : 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
                ->where('skrining_pkg.nik', $request->nik)
                ->orderByDesc('skrining_pkg.tanggal_skrining')
                ->orderByDesc('skrining_pkg.updated_at')
                ->orderByDesc('skrining_pkg.id_pkg')
                ->select('skrining_pkg.*', DB::raw('kelurahan.nm_kel as nm_kel'))
                ->first();
            if ($skriningSebelumnya) {
                return response()->json([
                    'status' => 'info',
                    'message' => 'Data skrining sebelumnya tersedia',
                    'data' => $skriningSebelumnya
                ]);
            }
            return response()->json([
                'status' => 'success',
                'message' => 'NIK valid untuk skrining',
                'data' => null
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal memeriksa NIK skrining: ' . $e->getMessage(), [
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

    /**
     * Menyimpan data keluhan lain
     */
    public function simpanKeluhanLain(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'keluhan_lain' => 'nullable|string',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data keluhan lain
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->keluhan_lain = $request->keluhan_lain;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data keluhan lain berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data keluhan lain',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan data skrining pertumbuhan balita dan anak prasekolah
     */
    public function simpanSkriningPertumbuhan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'berat_badan' => 'required|numeric|min:0|max:50',
            'tinggi_badan' => 'required|integer|min:50|max:200',
            'posisi_ukur' => 'required|string|in:Berdiri,Terlentang',
            'status_gizi_bb_u' => 'required|string',
            'status_gizi_pb_u' => 'required|string',
            'status_gizi_bb_pb' => 'required|string',
            'hasil_imt_u' => 'required|string',
            'status_lingkar_kepala' => 'required|string|in:Normal,Makrosefali,Mikrosefali',
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
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : null;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data skrining pertumbuhan
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->berat_badan_balita = $request->berat_badan;
            $skrining->tinggi_badan_balita = $request->tinggi_badan;
            $skrining->posisi_ukur = $request->posisi_ukur;
            $skrining->status_gizi_bb_u = $request->status_gizi_bb_u;
            $skrining->status_gizi_pb_u = $request->status_gizi_pb_u;
            $skrining->status_gizi_bb_pb = $request->status_gizi_bb_pb;
            $skrining->hasil_imt_u = $request->hasil_imt_u;
            $skrining->status_lingkar_kepala = $request->status_lingkar_kepala;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining pertumbuhan berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving skrining pertumbuhan: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining pertumbuhan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function simpanSkriningKPSP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'hasil_kpsp' => 'required|string|in:Perkembangan sesuai usia,Perkembangan meragukan,Kemungkinan ada penyimpangan',
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
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : null;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data skrining KPSP
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->hasil_kpsp = $request->hasil_kpsp;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining KPSP berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving skrining KPSP: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining KPSP',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function simpanSkriningTelingaMata(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'hasil_tes_dengar' => 'required|string',
            'hasil_tes_lihat' => 'required|string',
            'hasil_serumen' => 'required|string|in:Tidak ada serumen impaksi,Ada serumen impaksi',
            'hasil_infeksi_telinga' => 'required|string|in:Tidak ada infeksi telinga,Ada infeksi telinga',
            'selaput_mata' => 'required|string|in:Normal,Curiga kelainan mata'
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
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : null;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data skrining telinga dan mata
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->hasil_tes_dengar = $request->hasil_tes_dengar;
            $skrining->hasil_tes_lihat = $request->hasil_tes_lihat;
            $skrining->hasil_serumen = $request->hasil_serumen;
            $skrining->hasil_infeksi_telinga = $request->hasil_infeksi_telinga;
            $skrining->selaput_mata = $request->selaput_mata;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining telinga dan mata berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving skrining telinga mata: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining telinga dan mata',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function simpanSkriningGigiAnak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'nama_lengkap' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'karies' => 'required|in:Ya,Tidak',
            'hilang' => 'required|in:Ya,Tidak',
            'goyang' => 'required|in:Ya,Tidak',
            'jumlah_karies' => 'required|string|in:Tidak ada,1,2,3,> 3',
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
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : null;
            
            // Cari dulu apakah sudah ada data untuk pasien ini
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data skrining gigi anak
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->karies = $request->karies;
            $skrining->hilang = $request->hilang;
            $skrining->goyang = $request->goyang;
            $skrining->jumlah_karies = $request->jumlah_karies;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data skrining gigi anak berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error saving skrining gigi anak: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data skrining gigi anak',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data faktor resiko kanker usus/kolorektal
     */
    public function simpanKankerUsus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'kanker_usus_1' => 'required|string|in:Ya,Tidak',
            'kanker_usus_2' => 'required|string|in:Ya,Tidak',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data faktor resiko kanker usus
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->kanker_usus_1 = $request->kanker_usus_1;
            $skrining->kanker_usus_2 = $request->kanker_usus_2;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data faktor resiko kanker usus berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data kanker usus: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data faktor resiko kanker usus',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data faktor resiko TB
     */
    public function simpanFaktorResikoTB(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'faktor_resiko_tb' => 'required|string|in:Ya\, lebih dari 2 minggu,Ya\, kurang dari 2 minggu,Tidak batuk',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data faktor resiko TB
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->faktor_resiko_tb = $request->faktor_resiko_tb;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data faktor resiko TB berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data faktor resiko TB: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data faktor resiko TB',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Menyimpan data penapisan resiko kanker paru
     */
    public function simpanKankerParu(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'kanker_paru_1' => 'required|string|in:Ya,Tidak',
            'kanker_paru_2' => 'required|string|in:Ya,Tidak',
            'kanker_paru_3' => 'required|string|in:Ya,Tidak',
            'kanker_paru_4' => 'required|string|in:Ya,Tidak',
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
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
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
            
            // Update data kanker paru
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->kanker_paru_1 = $request->kanker_paru_1;
            $skrining->kanker_paru_2 = $request->kanker_paru_2;
            $skrining->kanker_paru_3 = $request->kanker_paru_3;
            $skrining->kanker_paru_4 = $request->kanker_paru_4;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data penapisan resiko kanker paru berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menyimpan data kanker paru: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data penapisan resiko kanker paru',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    /**
     * Menyimpan data penyakit tropis
     */
    public function simpanPenyakitTropis(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nik' => 'required|string|max:25',
            'frambusia' => 'required|string|in:Suspek frambusia,Bukan frambusia,Tidak Ada',
            'kusta' => 'required|string|in:Kusta,Bukan kusta,Meragukan,Tidak Ada',
            'skabies' => 'required|string|in:Skabies,Meragukan,Bukan Skabies,Tidak Ada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $pasien = DB::table('pasien')->where('no_ktp', $request->nik)->first();
            $no_rkm_medis = $pasien ? $pasien->no_rkm_medis : $request->no_rkm_medis;
            
            $skrining = SkriningPkg::where('nik', $request->nik)
                ->whereYear('tanggal_skrining', now()->year)
                ->first();
            
            if (!$skrining) {
                $skrining = new SkriningPkg();
                $skrining->nik = $request->nik;
                $skrining->nama_lengkap = $request->nama_lengkap;
                $skrining->tanggal_lahir = $request->tanggal_lahir;
                $skrining->jenis_kelamin = $request->jenis_kelamin;
                $skrining->no_handphone = $request->no_handphone;
                $skrining->tanggal_skrining = date('Y-m-d');
            }
            
            $skrining->no_rkm_medis = $no_rkm_medis;
            $skrining->frambusia = $request->frambusia;
            $skrining->kusta = $request->kusta;
            $skrining->skabies = $request->skabies;
            $skrining->save();

            return response()->json([
                'status' => 'success',
                'message' => 'Data penyakit tropis berhasil disimpan',
                'data' => $skrining
            ]);
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data penyakit tropis: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data penyakit tropis',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
