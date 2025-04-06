<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth')->except(['logout']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Determine page title
        $pageTitle = 'Dashboard';
        
        // Share page title with all views
        View::share('title', $pageTitle);
        
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Statistik kunjungan
        $statistikKunjungan = $this->statistikKunjungan($kd_dokter);
        
        // Get poliklinik name
        $poliklinik = $this->getPoliklinik($kd_poli);
        
        // Total pasien
        $totalPasien = $this->getNoRKM();
        
        // Pasien bulan ini
        $pasienBulanIni = $this->getPatientCountThisMonth();
        
        // Pasien poli bulan ini
        $pasienPoliBulanIni = $this->getPatientCountThisMonthByPoli($kd_poli, $kd_dokter);
        
        // Pasien poli hari ini
        $pasienPoliHariIni = $this->getCountTodayPatient($kd_poli, $kd_dokter);
        
        // Data for pasien aktif table
        $headPasienAktif = ['No', 'Nama Pasien', 'RM', 'Jumlah Kunjungan'];
        $pasienAktif = $this->getMostActivePatients($kd_poli, $kd_dokter);
        
        // Data for pasien terakhir table
        $headPasienTerakhir = ['No', 'Nama Pasien', 'RM', 'Tanggal Kunjungan', 'Status'];
        $pasienTerakhir = $this->getLastPatients($kd_poli, $kd_dokter);
        
        return view('home', [
            'noRKM' => $this->getNoRKM(),
            'jmlPoli' => sizeof($this->getDaftarPoli()),
            'jmlPasien' => $this->getCountTodayPatient($kd_poli, $kd_dokter),
            'jmlPasienSelesai' => $this->getCountDonePatient($kd_poli, $kd_dokter),
            'poli_id' => $kd_poli,
            'userType' => $this->getUserType(),
            'statistikKunjungan' => $statistikKunjungan,
            'nm_dokter' => $this->getDokter($kd_dokter),
            'totalPasien' => $totalPasien,
            'pasienBulanIni' => $pasienBulanIni,
            'pasienPoliBulanIni' => $pasienPoliBulanIni,
            'pasienPoliHariIni' => $pasienPoliHariIni,
            'poliklinik' => $poliklinik,
            'headPasienAktif' => $headPasienAktif,
            'pasienAktif' => $pasienAktif,
            'headPasienTerakhir' => $headPasienTerakhir,
            'pasienTerakhir' => $pasienTerakhir
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        if ($poli) {
            return $poli->nm_poli;
        }
        return 'Poliklinik tidak ditemukan';
    }
    
    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('pegawai')->where('nik', $kd_dokter)->first();
        if ($dokter) {
            return $dokter->nama;
        }
        return 'Dokter tidak ditemukan';
    }
    
    public function statistikKunjungan($kd_dokter)
    {
        $data = DB::table('reg_periksa')
                    ->where('kd_dokter', $kd_dokter)
                    ->where('tgl_registrasi', 'like', date('Y').'-%')
                    ->selectRaw("MONTHNAME (tgl_registrasi) as bulan, COUNT(DISTINCT  no_rawat) as jumlah")
                    ->groupByRaw("MONTH(tgl_registrasi)")
                    ->get();
        return $data;
    }

    public function logout()
    {
        Session::flush();
        return response()->view('logout_cleanup');
    }
    
    private function getNoRKM()
    {
        try {
            return DB::table('pasien')->count();
        } catch (\Exception $e) {
            Log::error('Error getNoRKM: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function getDaftarPoli()
    {
        try {
            return DB::table('poliklinik')->get();
        } catch (\Exception $e) {
            Log::error('Error getDaftarPoli: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getCountTodayPatient($kd_poli, $kd_dokter)
    {
        try {
            $query = DB::table('reg_periksa')
                ->where('tgl_registrasi', date('Y-m-d'));
                
            if (!empty($kd_poli)) {
                $query->where('kd_poli', $kd_poli);
            }
            
            if (!empty($kd_dokter)) {
                $query->where('kd_dokter', $kd_dokter);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::error('Error getCountTodayPatient: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function getCountDonePatient($kd_poli, $kd_dokter)
    {
        try {
            $query = DB::table('reg_periksa')
                ->where('tgl_registrasi', date('Y-m-d'))
                ->where('stts', 'Sudah');
                
            if (!empty($kd_poli)) {
                $query->where('kd_poli', $kd_poli);
            }
            
            if (!empty($kd_dokter)) {
                $query->where('kd_dokter', $kd_dokter);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::error('Error getCountDonePatient: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function getUserType()
    {
        $userType = session()->get('user_type');
        return $userType ? $userType : 'guest';
    }
    
    private function getPatientCountThisMonth()
    {
        try {
            return DB::table('reg_periksa')
                ->whereRaw('MONTH(tgl_registrasi) = MONTH(CURRENT_DATE())')
                ->whereRaw('YEAR(tgl_registrasi) = YEAR(CURRENT_DATE())')
                ->count();
        } catch (\Exception $e) {
            Log::error('Error getPatientCountThisMonth: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function getPatientCountThisMonthByPoli($kd_poli, $kd_dokter)
    {
        try {
            $query = DB::table('reg_periksa')
                ->whereRaw('MONTH(tgl_registrasi) = MONTH(CURRENT_DATE())')
                ->whereRaw('YEAR(tgl_registrasi) = YEAR(CURRENT_DATE())');
                
            if (!empty($kd_poli)) {
                $query->where('kd_poli', $kd_poli);
            }
            
            if (!empty($kd_dokter)) {
                $query->where('kd_dokter', $kd_dokter);
            }
            
            return $query->count();
        } catch (\Exception $e) {
            Log::error('Error getPatientCountThisMonthByPoli: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function getMostActivePatients($kd_poli, $kd_dokter, $limit = 5)
    {
        try {
            $query = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->selectRaw('reg_periksa.no_rkm_medis, pasien.nm_pasien, COUNT(*) as total_kunjungan')
                ->groupBy('reg_periksa.no_rkm_medis', 'pasien.nm_pasien')
                ->orderBy('total_kunjungan', 'desc')
                ->limit($limit);
                
            if (!empty($kd_poli)) {
                $query->where('reg_periksa.kd_poli', $kd_poli);
            }
            
            if (!empty($kd_dokter)) {
                $query->where('reg_periksa.kd_dokter', $kd_dokter);
            }
            
            $patients = $query->get();
            
            $result = [];
            $counter = 1;
            
            foreach ($patients as $patient) {
                $result[] = [
                    $counter++,
                    $patient->nm_pasien,
                    $patient->no_rkm_medis,
                    $patient->total_kunjungan
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error getMostActivePatients: ' . $e->getMessage());
            return [];
        }
    }
    
    private function getLastPatients($kd_poli, $kd_dokter, $limit = 10)
    {
        try {
            $query = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select('reg_periksa.no_rkm_medis', 'pasien.nm_pasien', 'reg_periksa.tgl_registrasi', 'reg_periksa.stts')
                ->orderBy('reg_periksa.tgl_registrasi', 'desc')
                ->limit($limit);
                
            if (!empty($kd_poli)) {
                $query->where('reg_periksa.kd_poli', $kd_poli);
            }
            
            if (!empty($kd_dokter)) {
                $query->where('reg_periksa.kd_dokter', $kd_dokter);
            }
            
            $patients = $query->get();
            
            $result = [];
            $counter = 1;
            
            foreach ($patients as $patient) {
                $status = $patient->stts;
                $statusLabel = '';
                
                switch($status) {
                    case 'Belum':
                        $statusLabel = '<span class="badge badge-warning">Belum</span>';
                        break;
                    case 'Sudah':
                        $statusLabel = '<span class="badge badge-success">Sudah</span>';
                        break;
                    case 'Batal':
                        $statusLabel = '<span class="badge badge-danger">Batal</span>';
                        break;
                    default:
                        $statusLabel = '<span class="badge badge-secondary">' . $status . '</span>';
                }
                
                $result[] = [
                    $counter++,
                    $patient->nm_pasien,
                    $patient->no_rkm_medis,
                    $patient->tgl_registrasi,
                    $statusLabel
                ];
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Error getLastPatients: ' . $e->getMessage());
            return [];
        }
    }
}
