<?php

namespace App\Http\Controllers\ANC;

use App\Http\Controllers\Controller;
use App\Models\DataIbuHamil;
use App\Models\Pasien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Schema;

class DataIbuHamilController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = DataIbuHamil::with('pasien')->latest()->get();
            
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $actionBtn = '
                        <div class="btn-group">
                            <a href="'.route('anc.data-ibu-hamil.edit', $row->id_hamil).'" class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger btn-hapus" data-id="'.$row->id_hamil.'">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    ';
                    return $actionBtn;
                })
                ->addColumn('usia_kehamilan', function($row) {
                    if ($row->hari_perkiraan_lahir) {
                        $hpl = \Carbon\Carbon::parse($row->hari_perkiraan_lahir);
                        $today = \Carbon\Carbon::now();
                        
                        if ($today->lt($hpl)) {
                            $diffInDays = $today->diffInDays($hpl);
                            $weeks = floor((280 - $diffInDays) / 7);
                            $days = (280 - $diffInDays) % 7;
                            return $weeks . ' minggu ' . $days . ' hari';
                        } else {
                            return 'Sudah lewat HPL';
                        }
                    }
                    return '-';
                })
                ->editColumn('status', function ($row) {
                    $statusClass = [
                        'Hamil' => 'badge bg-primary',
                        'Melahirkan' => 'badge bg-success',
                        'Abortus' => 'badge bg-danger'
                    ];
                    
                    $class = $statusClass[$row->status] ?? 'badge bg-secondary';
                    return '<span class="'.$class.'">'.$row->status.'</span>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }
        
        return view('anc.data-ibu-hamil');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nik' => 'required|string',
            'no_rkm_medis' => 'required|string',
            'kehamilan_ke' => 'required|string',
            'tgl_lahir' => 'required|date',
            'nomor_kk' => 'required|string',
            'nama' => 'required|string',
            'berat_badan_sebelum_hamil' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lila' => 'nullable|numeric',
            'imt_sebelum_hamil' => 'nullable|numeric',
            'status_gizi' => 'nullable|string',
            'jumlah_janin' => 'nullable|string',
            'jarak_kehamilan_tahun' => 'nullable|string',
            'jarak_kehamilan_bulan' => 'nullable|string',
            'hari_pertama_haid' => 'nullable|date',
            'hari_perkiraan_lahir' => 'nullable|date',
            'golongan_darah' => 'nullable|string',
            'rhesus' => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'riwayat_alergi' => 'nullable|string',
            'kepemilikan_buku_kia' => 'required|boolean',
            'jaminan_kesehatan' => 'nullable|string',
            'no_jaminan_kesehatan' => 'nullable|string',
            'faskes_tk1' => 'nullable|string',
            'faskes_rujukan' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'status' => 'required|string|in:Hamil,Melahirkan,Abortus',
            'nama_suami' => 'nullable|string',
            'nik_suami' => 'nullable|string',
            'telp_suami' => 'nullable|string',
            'provinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'kecamatan' => 'required|integer',
            'puskesmas' => 'required|string',
            'desa' => 'required|integer',
            'data_posyandu' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
        ]);

        try {
            // Cek apakah pasien tersebut ada
            $pasien = Pasien::where('no_rkm_medis', $request->no_rkm_medis)->first();
            if (!$pasien) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien dengan No. RM '. $request->no_rkm_medis .' tidak ditemukan'
                ], 404);
            }
            
            $dataIbuHamil = DataIbuHamil::create($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data ibu hamil berhasil disimpan',
                'data' => $dataIbuHamil
            ]);
        } catch (\Exception $e) {
            Log::error('Error simpan data ibu hamil: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan data ibu hamil: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    public function show($nik)
    {
        $dataIbuHamil = DataIbuHamil::where('nik', $nik)->first();
        if (!$dataIbuHamil) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ibu hamil tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data' => $dataIbuHamil
        ]);
    }

    public function update(Request $request, $id)
    {
        $dataIbuHamil = DataIbuHamil::find($id);
        if (!$dataIbuHamil) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ibu hamil tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'nik' => 'required|string',
            'no_rkm_medis' => 'required|string',
            'kehamilan_ke' => 'required|string',
            'tgl_lahir' => 'required|date',
            'nomor_kk' => 'required|string',
            'nama' => 'required|string',
            'berat_badan_sebelum_hamil' => 'nullable|numeric',
            'tinggi_badan' => 'nullable|numeric',
            'lila' => 'nullable|numeric',
            'imt_sebelum_hamil' => 'nullable|numeric',
            'status_gizi' => 'nullable|string',
            'jumlah_janin' => 'nullable|string',
            'jarak_kehamilan_tahun' => 'nullable|string',
            'jarak_kehamilan_bulan' => 'nullable|string',
            'hari_pertama_haid' => 'nullable|date',
            'hari_perkiraan_lahir' => 'nullable|date',
            'golongan_darah' => 'nullable|string',
            'rhesus' => 'nullable|string',
            'riwayat_penyakit' => 'nullable|string',
            'riwayat_alergi' => 'nullable|string',
            'kepemilikan_buku_kia' => 'required|boolean',
            'jaminan_kesehatan' => 'nullable|string',
            'no_jaminan_kesehatan' => 'nullable|string',
            'faskes_tk1' => 'nullable|string',
            'faskes_rujukan' => 'nullable|string',
            'pendidikan' => 'nullable|string',
            'pekerjaan' => 'nullable|string',
            'status' => 'required|string|in:Hamil,Melahirkan,Abortus',
            'nama_suami' => 'nullable|string',
            'nik_suami' => 'nullable|string',
            'telp_suami' => 'nullable|string',
            'provinsi' => 'required|integer',
            'kabupaten' => 'required|integer',
            'kecamatan' => 'required|integer',
            'puskesmas' => 'required|string',
            'desa' => 'required|integer',
            'data_posyandu' => 'required|string',
            'alamat_lengkap' => 'required|string',
            'rt' => 'nullable|string',
            'rw' => 'nullable|string',
        ]);

        try {
            // Cek apakah pasien tersebut ada
            $pasien = Pasien::where('no_rkm_medis', $request->no_rkm_medis)->first();
            if (!$pasien) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien dengan No. RM '. $request->no_rkm_medis .' tidak ditemukan'
                ], 404);
            }
            
            $dataIbuHamil->update($validated);
            
            return response()->json([
                'status' => 'success',
                'message' => 'Data ibu hamil berhasil diperbarui',
                'data' => $dataIbuHamil
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui data ibu hamil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        $dataIbuHamil = DataIbuHamil::find($id);
        if (!$dataIbuHamil) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data ibu hamil tidak ditemukan'
            ], 404);
        }

        try {
            $dataIbuHamil->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Data ibu hamil berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus data ibu hamil: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $dataIbuHamil = DataIbuHamil::findOrFail($id);
            return view('anc.data-ibu-hamil', compact('dataIbuHamil'));
        } catch (\Exception $e) {
            return redirect()->route('anc.data-ibu-hamil.index')
                ->with('error', 'Data tidak ditemukan: ' . $e->getMessage());
        }
    }

    public function getDataPasien($nik)
    {
        try {
            Log::info('Mencari data pasien dengan NIK: ' . $nik);
            
            // Debug query
            $queryLog = DB::connection()->enableQueryLog();
            
            $pasien = Pasien::where('no_ktp', $nik)->first();
            
            Log::info('Query logs: ', DB::getQueryLog());
            DB::connection()->disableQueryLog();

            if (!$pasien) {
                Log::warning('Data pasien tidak ditemukan untuk NIK: ' . $nik);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Data pasien tidak ditemukan'
                ], 404);
            }
            
            // Cek jika pasien sudah terdaftar sebagai ibu hamil
            $dataIbuHamil = DataIbuHamil::where('no_rkm_medis', $pasien->no_rkm_medis)
                                        ->where('status', 'Hamil')
                                        ->first();
            
            if ($dataIbuHamil) {
                Log::info('Pasien sudah terdaftar sebagai ibu hamil dengan ID: ' . $dataIbuHamil->id_hamil);
                
                // Tambahkan data status ibu hamil
                $ibuHamilStatus = [
                    'id_hamil' => $dataIbuHamil->id_hamil,
                    'status' => $dataIbuHamil->status,
                    'kehamilan_ke' => $dataIbuHamil->kehamilan_ke,
                    'hari_perkiraan_lahir' => $dataIbuHamil->hari_perkiraan_lahir
                ];
            } else {
                $ibuHamilStatus = null;
            }
            
            // Cek struktur tabel kabupaten
            $kabupatenColumns = Schema::getColumnListing('kabupaten');
            $kecamatanColumns = Schema::getColumnListing('kecamatan');
            $kelurahanColumns = Schema::getColumnListing('kelurahan');
            
            // Ambil data propinsi
            $propinsi = DB::table('propinsi')
                ->where('kd_prop', $pasien->kd_prop)
                ->first();
                
            // Ambil data kabupaten dengan pengecekan kolom
            $kabupatenQuery = DB::table('kabupaten')->where('kd_kab', $pasien->kd_kab);
            if (in_array('kd_prop', $kabupatenColumns)) {
                $kabupatenQuery->where('kd_prop', $pasien->kd_prop);
            }
            $kabupaten = $kabupatenQuery->first();
                
            // Ambil data kecamatan dengan pengecekan kolom
            $kecamatanQuery = DB::table('kecamatan')->where('kd_kec', $pasien->kd_kec);
            if (in_array('kd_kab', $kecamatanColumns)) {
                $kecamatanQuery->where('kd_kab', $pasien->kd_kab);
            }
            if (in_array('kd_prop', $kecamatanColumns)) {
                $kecamatanQuery->where('kd_prop', $pasien->kd_prop);
            }
            $kecamatan = $kecamatanQuery->first();
                
            // Ambil data kelurahan dengan pengecekan kolom
            $kelurahanQuery = DB::table('kelurahan')->where('kd_kel', $pasien->kd_kel);
            if (in_array('kd_kec', $kelurahanColumns)) {
                $kelurahanQuery->where('kd_kec', $pasien->kd_kec);
            }
            if (in_array('kd_kab', $kelurahanColumns)) {
                $kelurahanQuery->where('kd_kab', $pasien->kd_kab);
            }
            if (in_array('kd_prop', $kelurahanColumns)) {
                $kelurahanQuery->where('kd_prop', $pasien->kd_prop);
            }
            $kelurahan = $kelurahanQuery->first();

            Log::info('Data pasien ditemukan:', ['pasien' => $pasien]);

            return response()->json([
                'status' => 'success',
                'data' => [
                    'nama' => $pasien->nm_pasien,
                    'no_rkm_medis' => $pasien->no_rkm_medis,
                    'tgl_lahir' => $pasien->tgl_lahir,
                    'nomor_kk' => $pasien->no_kk,
                    'no_jaminan_kesehatan' => $pasien->no_peserta,
                    'alamat' => $pasien->alamat,
                    'provinsi' => [
                        'kode' => (int) $pasien->kd_prop,
                        'nama' => $propinsi ? $propinsi->nm_prop : 'Tidak Ada'
                    ],
                    'kabupaten' => [
                        'kode' => (int) $pasien->kd_kab,
                        'nama' => $kabupaten ? ($kabupaten->nm_kab ?? $kabupaten->nama_kabupaten ?? 'Tidak Ada') : 'Tidak Ada'
                    ],
                    'kecamatan' => [
                        'kode' => (int) $pasien->kd_kec,
                        'nama' => $kecamatan ? ($kecamatan->nm_kec ?? $kecamatan->nama_kecamatan ?? 'Tidak Ada') : 'Tidak Ada'
                    ],
                    'desa' => [
                        'kode' => (int) $pasien->kd_kel,
                        'nama' => $kelurahan ? ($kelurahan->nm_kel ?? $kelurahan->nama_kelurahan ?? 'Tidak Ada') : 'Tidak Ada'
                    ],
                    'puskesmas' => 'KERJO',
                    'data_posyandu' => $pasien->data_posyandu,
                    'ibu_hamil_status' => $ibuHamilStatus
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error saat mencari data pasien: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ], 500);
        }
    }
} 