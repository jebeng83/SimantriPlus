<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\DataSiswaSekolah;
use App\Models\DataSekolah;
use App\Models\JenisSekolah;
use App\Models\DataKelas;
use App\Exports\DataSiswaSekolahExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Session;
use Illuminate\Support\Facades\View;

class DashboardSekolahController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    public function index(Request $request)
    {
        // Set page title
        View::share('title', 'Dashboard Sekolah');
        
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Get filter parameters
        $sekolahId = $request->get('sekolah') ?: null;
        $jenisSekolahId = $request->get('jenis_sekolah') ?: null;
        $kelasId = $request->get('kelas') ?: null;
        
        // Base query for dashboard statistics
        $query = DataSiswaSekolah::select(
            'data_siswa_sekolah.*',
            'pasien.nm_pasien',
            'pasien.jk',
            'pasien.tgl_lahir',
            'pasien.alamat',
            'data_sekolah.nama_sekolah',
            'data_kelas.kelas',
            'jenis_sekolah.nama'
        )
        ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
        ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
        ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id');
        
        // Apply filters
        if ($sekolahId) {
            $query->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        
        if ($jenisSekolahId) {
            $query->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
        }
        
        if ($kelasId) {
            $query->where('data_siswa_sekolah.id_kelas', $kelasId);
        }
        
        // Get statistics
        $totalSiswa = $query->count();
        
        // Total siswa by gender
        $siswaLakiLaki = (clone $query)->where('pasien.jk', 'L')->count();
        $siswaPerempuan = (clone $query)->where('pasien.jk', 'P')->count();
        
        // Total siswa by status
        $siswaAktif = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Aktif')->count();
        $siswaPindah = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Pindah')->count();
        $siswaLulus = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Lulus')->count();
        $siswaDropOut = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Drop Out')->count();
        
        // Total siswa by disability
        $siswaDisabilitas = (clone $query)->where('data_siswa_sekolah.jenis_disabilitas', '!=', 'Non Disabilitas')->count();
        $siswaNonDisabilitas = (clone $query)->where('data_siswa_sekolah.jenis_disabilitas', 'Non Disabilitas')->count();
        
        // Statistics by school
        $statistikSekolah = DB::table('data_siswa_sekolah')
            ->select(
                'data_sekolah.nama_sekolah',
                'jenis_sekolah.nama',
                DB::raw('COUNT(*) as total_siswa'),
                DB::raw('SUM(CASE WHEN pasien.jk = "L" THEN 1 ELSE 0 END) as siswa_laki'),
                DB::raw('SUM(CASE WHEN pasien.jk = "P" THEN 1 ELSE 0 END) as siswa_perempuan'),
                DB::raw('SUM(CASE WHEN data_siswa_sekolah.status_siswa = "Aktif" THEN 1 ELSE 0 END) as siswa_aktif'),
                DB::raw('SUM(CASE WHEN data_siswa_sekolah.jenis_disabilitas != "Non Disabilitas" THEN 1 ELSE 0 END) as siswa_disabilitas')
            )
            ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
            ->groupBy('data_sekolah.id_sekolah', 'data_sekolah.nama_sekolah', 'jenis_sekolah.nama')
            ->orderBy('total_siswa', 'desc');
            
        if ($sekolahId) {
            $statistikSekolah->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        
        if ($jenisSekolahId) {
            $statistikSekolah->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
        }
        
        $statistikSekolah = $statistikSekolah->get();
        
        // Statistics by class
        $statistikKelas = DB::table('data_siswa_sekolah')
            ->select(
                'data_kelas.kelas',
                'data_sekolah.nama_sekolah',
                DB::raw('COUNT(*) as total_siswa'),
                DB::raw('SUM(CASE WHEN pasien.jk = "L" THEN 1 ELSE 0 END) as siswa_laki'),
                DB::raw('SUM(CASE WHEN pasien.jk = "P" THEN 1 ELSE 0 END) as siswa_perempuan')
            )
            ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->groupBy('data_kelas.id_kelas', 'data_kelas.kelas', 'data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->orderBy('data_kelas.kelas');
            
        if ($sekolahId) {
            $statistikKelas->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        
        if ($kelasId) {
            $statistikKelas->where('data_siswa_sekolah.id_kelas', $kelasId);
        }
        
        $statistikKelas = $statistikKelas->get();
        
        // Age distribution
        $distribusiUmur = DB::table('data_siswa_sekolah')
            ->select(
                DB::raw('CASE 
                    WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) < 6 THEN "< 6 tahun"
                    WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 6 AND 12 THEN "6-12 tahun"
                    WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 13 AND 15 THEN "13-15 tahun"
                    WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 16 AND 18 THEN "16-18 tahun"
                    ELSE "> 18 tahun"
                END as kelompok_umur'),
                DB::raw('COUNT(*) as jumlah')
            )
            ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->groupBy('kelompok_umur')
            ->orderBy('kelompok_umur');
            
        if ($sekolahId) {
            $distribusiUmur->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        
        $distribusiUmur = $distribusiUmur->get();
        
        // Get filter options
        $daftarSekolah = DataSekolah::select('id_sekolah', 'nama_sekolah')
            ->orderBy('nama_sekolah')
            ->get();
            
        $daftarJenisSekolah = JenisSekolah::select('id', 'nama')
            ->orderBy('nama')
            ->get();
            
        $daftarKelas = DataKelas::select('id_kelas', 'kelas')
            ->orderBy('kelas')
            ->get();
        
        return view('ilp.data-siswa-sekolah.dashboard', compact(
            'totalSiswa',
            'siswaLakiLaki',
            'siswaPerempuan',
            'siswaAktif',
            'siswaPindah',
            'siswaLulus',
            'siswaDropOut',
            'siswaDisabilitas',
            'distribusiUmur',
            'statistikSekolah',
            'statistikKelas',
            'daftarSekolah',
            'daftarJenisSekolah',
            'daftarKelas',
            'sekolahId',
            'jenisSekolahId',
            'kelasId'
        ));
    }
    
    public function exportExcel(Request $request)
    {
        try {
            // Get filter parameters
            $filters = [
                'sekolah' => $request->get('sekolah'),
                'jenis_sekolah' => $request->get('jenis_sekolah'),
                'kelas' => $request->get('kelas'),
                'search' => $request->get('search')
            ];
            
            // Create export instance with filters
            $export = new DataSiswaSekolahExport($filters);
            
            // Generate filename with timestamp
            $filename = 'dashboard_siswa_sekolah_' . date('YmdHis') . '.xlsx';
            
            // Return Excel download
            return Excel::download($export, $filename);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengexport data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    public function exportPdf(Request $request)
    {
        try {
            // Get filter parameters
            $sekolahId = $request->get('sekolah');
            $jenisSekolahId = $request->get('jenis_sekolah');
            $kelasId = $request->get('kelas');
            
            // Build query with joins (same as index method)
            $query = DataSiswaSekolah::select(
                'data_siswa_sekolah.*',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'data_sekolah.nama_sekolah',
                'data_kelas.kelas',
                'jenis_sekolah.nama'
            )
            ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id');
            
            // Apply filters
            if ($sekolahId) {
                $query->where('data_siswa_sekolah.id_sekolah', $sekolahId);
            }
            
            if ($jenisSekolahId) {
                $query->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
            }
            
            if ($kelasId) {
                $query->where('data_siswa_sekolah.id_kelas', $kelasId);
            }
            
            // Get statistics
            $totalSiswa = $query->count();
            
            // Total siswa by gender
            $siswaLakiLaki = (clone $query)->where('pasien.jk', 'L')->count();
            $siswaPerempuan = (clone $query)->where('pasien.jk', 'P')->count();
            
            // Total siswa by status
            $siswaAktif = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Aktif')->count();
            $siswaPindah = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Pindah')->count();
            $siswaLulus = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Lulus')->count();
            $siswaDropOut = (clone $query)->where('data_siswa_sekolah.status_siswa', 'Drop Out')->count();
            
            // Get age distribution
            $distribusiUmur = DB::table('data_siswa_sekolah')
                ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    DB::raw('CASE 
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) < 6 THEN "< 6 tahun"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 6 AND 12 THEN "6-12 tahun"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 13 AND 15 THEN "13-15 tahun"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, CURDATE()) BETWEEN 16 AND 18 THEN "16-18 tahun"
                        ELSE "> 18 tahun"
                    END as kelompok_umur'),
                    DB::raw('COUNT(*) as jumlah')
                )
                ->when($sekolahId, function($q) use ($sekolahId) {
                    return $q->where('data_siswa_sekolah.id_sekolah', $sekolahId);
                })
                ->when($jenisSekolahId, function($q) use ($jenisSekolahId) {
                    return $q->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
                             ->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
                })
                ->when($kelasId, function($q) use ($kelasId) {
                    return $q->where('data_siswa_sekolah.id_kelas', $kelasId);
                })
                ->groupBy('kelompok_umur')
                ->orderBy('kelompok_umur')
                ->get();
            
            // Get school statistics
            $statistikSekolah = DB::table('data_siswa_sekolah')
                ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
                ->join('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
                ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'data_sekolah.nama_sekolah',
                    'jenis_sekolah.nama',
                    DB::raw('COUNT(*) as total_siswa'),
                    DB::raw('SUM(CASE WHEN pasien.jk = "L" THEN 1 ELSE 0 END) as siswa_laki'),
                    DB::raw('SUM(CASE WHEN pasien.jk = "P" THEN 1 ELSE 0 END) as siswa_perempuan'),
                    DB::raw('SUM(CASE WHEN data_siswa_sekolah.status_siswa = "Aktif" THEN 1 ELSE 0 END) as siswa_aktif'),
                    DB::raw('SUM(CASE WHEN data_siswa_sekolah.jenis_disabilitas != "Non Disabilitas" THEN 1 ELSE 0 END) as siswa_disabilitas')
                )
                ->when($sekolahId, function($q) use ($sekolahId) {
                    return $q->where('data_siswa_sekolah.id_sekolah', $sekolahId);
                })
                ->when($jenisSekolahId, function($q) use ($jenisSekolahId) {
                    return $q->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
                })
                ->when($kelasId, function($q) use ($kelasId) {
                    return $q->where('data_siswa_sekolah.id_kelas', $kelasId);
                })
                ->groupBy('data_sekolah.id_sekolah', 'data_sekolah.nama_sekolah', 'jenis_sekolah.nama')
                ->orderBy('data_sekolah.nama_sekolah')
                ->get();
            
            // Get class statistics
            $statistikKelas = DB::table('data_siswa_sekolah')
                ->join('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
                ->join('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
                ->join('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'data_kelas.kelas',
                    'data_sekolah.nama_sekolah',
                    DB::raw('COUNT(*) as total_siswa'),
                    DB::raw('SUM(CASE WHEN pasien.jk = "L" THEN 1 ELSE 0 END) as siswa_laki'),
                    DB::raw('SUM(CASE WHEN pasien.jk = "P" THEN 1 ELSE 0 END) as siswa_perempuan')
                )
                ->when($sekolahId, function($q) use ($sekolahId) {
                    return $q->where('data_siswa_sekolah.id_sekolah', $sekolahId);
                })
                ->when($jenisSekolahId, function($q) use ($jenisSekolahId) {
                    return $q->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
                })
                ->when($kelasId, function($q) use ($kelasId) {
                    return $q->where('data_siswa_sekolah.id_kelas', $kelasId);
                })
                ->groupBy('data_kelas.id_kelas', 'data_kelas.kelas', 'data_sekolah.nama_sekolah')
                ->orderBy('data_kelas.kelas')
                ->get();
            
            // Prepare filter labels
            $filterLabels = [
                'sekolah' => 'Semua Sekolah',
                'jenis_sekolah' => 'Semua Jenis',
                'kelas' => 'Semua Kelas'
            ];
            
            if ($sekolahId) {
                $sekolah = DataSekolah::find($sekolahId);
                $filterLabels['sekolah'] = $sekolah !== null ? $sekolah->nama_sekolah : 'Sekolah tidak ditemukan';
            }
            
            if ($jenisSekolahId) {
                $jenisSekolah = JenisSekolah::find($jenisSekolahId);
                $filterLabels['jenis_sekolah'] = $jenisSekolah !== null ? $jenisSekolah->nama : 'Jenis tidak ditemukan';
            }
            
            if ($kelasId) {
                $kelas = DataKelas::find($kelasId);
                $filterLabels['kelas'] = $kelas !== null ? $kelas->kelas : 'Kelas tidak ditemukan';
            }
            
            // Prepare data for PDF
            $data = [
                'hospital_info' => [
                    'name' => config('app.name', 'RUMAH SAKIT'),
                    'address' => 'Alamat Rumah Sakit',
                    'phone' => 'Nomor Telepon',
                    'email' => 'email@rumahsakit.com'
                ],
                'tanggal_cetak' => now()->format('d F Y H:i:s'),
                'filter_labels' => $filterLabels,
                'totalSiswa' => $totalSiswa,
                'siswaLakiLaki' => $siswaLakiLaki,
                'siswaPerempuan' => $siswaPerempuan,
                'siswaAktif' => $siswaAktif,
                'siswaPindah' => $siswaPindah,
                'siswaLulus' => $siswaLulus,
                'siswaDropOut' => $siswaDropOut,
                'distribusiUmur' => $distribusiUmur,
                'statistikSekolah' => $statistikSekolah,
                'statistikKelas' => $statistikKelas
            ];
            
            // Generate PDF
            $pdf = Pdf::loadView('exports.dashboard-sekolah-pdf', $data);
            
            if ($pdf === null) {
                throw new \Exception('Gagal membuat PDF');
            }
            
            $pdf->setPaper('A4', 'portrait');
            
            // Generate filename
            $filename = 'Dashboard_Sekolah_' . date('Y-m-d_H-i-s') . '.pdf';
            
            // Return PDF for download
            return $pdf->download($filename);
            
        } catch (\Exception $e) {
            return redirect()->route('ilp.dashboard-sekolah')->with('error', 'Terjadi kesalahan saat mengekspor PDF: ' . $e->getMessage());
        }
    }
}