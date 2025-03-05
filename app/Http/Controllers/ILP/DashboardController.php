<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Carbon\Carbon;

class DashboardController extends Controller
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

    /**
     * Show the ILP dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Ambil filter posyandu jika ada
        $posyandu_filter = $request->input('posyandu');
        
        // Ambil filter desa jika ada
        $desa_filter = $request->input('desa');
        
        // Ambil filter periode (default: bulan)
        $periode_filter = $request->input('periode', 'bulan');
        
        // Ambil daftar desa/kelurahan dari database
        $daftar_desa = $this->getDaftarDesa();
        
        // Ambil daftar posyandu dari database berdasarkan filter desa jika ada
        $daftar_posyandu = $this->getDaftarPosyandu($desa_filter);
        
        // Hitung jumlah pasien berdasarkan kelompok umur dan filter posyandu
        $balita = $this->hitungPasienByUmur(0, 5, $posyandu_filter, $desa_filter);
        $pra_sekolah = $this->hitungPasienByUmur(6, 9, $posyandu_filter, $desa_filter);
        $remaja = $this->hitungPasienByUmur(10, 18, $posyandu_filter, $desa_filter);
        $produktif = $this->hitungPasienByUmur(19, 59, $posyandu_filter, $desa_filter);
        $lansia = $this->hitungPasienByUmur(60, 200, $posyandu_filter, $desa_filter); // Asumsi maksimal umur 200 tahun
        
        // Ambil data kunjungan posyandu dari ilp_dewasa
        $kunjungan_posyandu = $this->getKunjunganPosyandu($posyandu_filter, $periode_filter, $desa_filter);
        
        // Ambil data kunjungan berdasarkan posyandu
        $kunjungan_by_posyandu = $this->getKunjunganByPosyandu($desa_filter, $periode_filter);
        
        // Ambil data faktor risiko berdasarkan IMT dan tekanan darah
        $faktor_risiko = $this->getFaktorRisiko($posyandu_filter, $desa_filter, $periode_filter);
        
        // Jika permintaan AJAX untuk mendapatkan daftar posyandu berdasarkan desa
        if ($request->ajax() && $request->has('get_posyandu_by_desa')) {
            return response()->json([
                'daftar_posyandu' => $daftar_posyandu
            ]);
        }
        
        // Jika permintaan AJAX, kembalikan data dalam format JSON
        if ($request->ajax() || $request->has('ajax')) {
            // Siapkan data untuk grafik kunjungan berdasarkan umur
            $kunjunganUmurData = [
                'labels' => $kunjungan_posyandu['labels'],
                'datasets' => [
                    [
                        'label' => 'Balita (0-5)',
                        'data' => $kunjungan_posyandu['balita'],
                        'backgroundColor' => 'rgba(23, 162, 184, 0.2)',
                        'borderColor' => 'rgba(23, 162, 184, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Pra Sekolah (6-9)',
                        'data' => $kunjungan_posyandu['pra_sekolah'],
                        'backgroundColor' => 'rgba(40, 167, 69, 0.2)',
                        'borderColor' => 'rgba(40, 167, 69, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Remaja (10-18)',
                        'data' => $kunjungan_posyandu['remaja'],
                        'backgroundColor' => 'rgba(0, 123, 255, 0.2)',
                        'borderColor' => 'rgba(0, 123, 255, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Produktif (19-59)',
                        'data' => $kunjungan_posyandu['produktif'],
                        'backgroundColor' => 'rgba(255, 193, 7, 0.2)',
                        'borderColor' => 'rgba(255, 193, 7, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.4
                    ],
                    [
                        'label' => 'Lansia (>60)',
                        'data' => $kunjungan_posyandu['lansia'],
                        'backgroundColor' => 'rgba(220, 53, 69, 0.2)',
                        'borderColor' => 'rgba(220, 53, 69, 1)',
                        'borderWidth' => 2,
                        'tension' => 0.4
                    ]
                ]
            ];
            
            // Siapkan data untuk grafik kunjungan berdasarkan posyandu
            $kunjunganPosyanduData = [
                'labels' => $kunjungan_by_posyandu['labels'],
                'datasets' => [
                    [
                        'label' => 'Jumlah Kunjungan',
                        'data' => $kunjungan_by_posyandu['data'],
                        'backgroundColor' => 'rgba(40, 167, 69, 0.7)',
                        'borderColor' => 'rgba(40, 167, 69, 1)',
                        'borderWidth' => 1,
                        'borderRadius' => 5,
                        'barThickness' => 25,
                        'maxBarThickness' => 40
                    ]
                ]
            ];
            
            return response()->json([
                'success' => true,
                'data' => [
                    'chartKunjunganByUmur' => $kunjunganUmurData,
                    'chartKunjunganByPosyandu' => $kunjunganPosyanduData
                ],
                'periode_filter' => $periode_filter,
                'message' => 'Data berhasil dimuat'
            ]);
        }
        
        return view('ilp.dashboard', [
            'nm_dokter' => $this->getDokter($kd_dokter),
            'balita' => $balita,
            'pra_sekolah' => $pra_sekolah,
            'remaja' => $remaja,
            'produktif' => $produktif,
            'lansia' => $lansia,
            'daftar_posyandu' => $daftar_posyandu,
            'daftar_desa' => $daftar_desa,
            'kunjungan_posyandu' => $kunjungan_posyandu,
            'kunjungan_by_posyandu' => $kunjungan_by_posyandu,
            'faktor_risiko' => $faktor_risiko,
            'periode_filter' => $periode_filter,
        ]);
    }
    
    /**
     * Ambil daftar posyandu dari database
     * 
     * @param string|null $desa Filter berdasarkan desa
     * @return array
     */
    private function getDaftarPosyandu($desa = null)
    {
        $query = DB::table('data_posyandu')
            ->whereNotNull('nama_posyandu')
            ->where('nama_posyandu', '!=', '')
            ->where('nama_posyandu', '!=', '-');
            
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('desa', $desa);
        }
        
        return $query->distinct()
            ->pluck('nama_posyandu')
            ->toArray();
    }
    
    /**
     * Ambil daftar desa/kelurahan dari database
     * 
     * @return array
     */
    private function getDaftarDesa()
    {
        return DB::table('pasien')
            ->whereNotNull('data_posyandu')
            ->join('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.nama_posyandu')
            ->whereNotNull('data_posyandu.desa')
            ->where('data_posyandu.desa', '!=', '')
            ->where('data_posyandu.desa', '!=', '-')
            ->distinct()
            ->pluck('data_posyandu.desa')
            ->toArray();
    }
    
    /**
     * Hitung jumlah pasien berdasarkan rentang umur dan posyandu
     * 
     * @param int $min_umur
     * @param int $max_umur
     * @param string|null $posyandu
     * @param string|null $desa
     * @return int
     */
    private function hitungPasienByUmur($min_umur, $max_umur, $posyandu = null, $desa = null)
    {
        $query = DB::table('pasien')
            ->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.nama_posyandu')
            ->whereRaw('umur >= ? AND umur <= ?', [$min_umur, $max_umur])
            ->where('pasien.data_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('pasien.data_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('data_posyandu.desa', $desa);
        }
        
        return $query->count();
    }
    
    /**
     * Ambil data kunjungan posyandu dari tabel ilp_dewasa
     * 
     * @param string|null $posyandu
     * @param string $periode (minggu, bulan, tahun)
     * @param string|null $desa
     * @return array
     */
    private function getKunjunganPosyandu($posyandu = null, $periode = 'bulan', $desa = null)
    {
        // Query dasar untuk mengambil data dari ilp_dewasa
        $query = DB::table('ilp_dewasa as id')
            ->join('pasien as p', 'id.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_posyandu as dp', 'id.data_posyandu', '=', 'dp.nama_posyandu')
            ->select(
                DB::raw('COUNT(CASE WHEN p.umur BETWEEN 0 AND 5 THEN 1 END) as balita'),
                DB::raw('COUNT(CASE WHEN p.umur BETWEEN 6 AND 9 THEN 1 END) as pra_sekolah'),
                DB::raw('COUNT(CASE WHEN p.umur BETWEEN 10 AND 18 THEN 1 END) as remaja'),
                DB::raw('COUNT(CASE WHEN p.umur BETWEEN 19 AND 59 THEN 1 END) as produktif'),
                DB::raw('COUNT(CASE WHEN p.umur >= 60 THEN 1 END) as lansia')
            )
            ->whereNotNull('id.data_posyandu')
            ->where('id.data_posyandu', '!=', '')
            ->where('id.data_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('id.data_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Tentukan interval waktu berdasarkan periode
        $interval = 6; // Default 6 bulan
        $groupByFormat = '';
        $dateFormat = '';
        
        switch ($periode) {
            case 'minggu':
                $interval = 12; // 12 minggu terakhir
                $query->addSelect(DB::raw('YEARWEEK(id.tanggal, 1) as periode_waktu'));
                $groupByFormat = 'YEARWEEK(id.tanggal, 1)';
                $dateFormat = 'Minggu %v %Y';
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' WEEK)'));
                break;
                
            case 'tahun':
                $interval = 5; // 5 tahun terakhir
                $query->addSelect(DB::raw('YEAR(id.tanggal) as periode_waktu'));
                $groupByFormat = 'YEAR(id.tanggal)';
                $dateFormat = '%Y';
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' YEAR)'));
                break;
                
            case 'bulan':
            default:
                $interval = 6; // 6 bulan terakhir
                $query->addSelect(
                    DB::raw('YEAR(id.tanggal) as tahun'),
                    DB::raw('MONTH(id.tanggal) as bulan'),
                    DB::raw('CONCAT(YEAR(id.tanggal), MONTH(id.tanggal)) as periode_waktu')
                );
                $groupByFormat = 'YEAR(id.tanggal), MONTH(id.tanggal)';
                $dateFormat = '%M %Y';
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' MONTH)'));
                break;
        }
        
        // Kelompokkan berdasarkan periode waktu
        $query->groupBy(DB::raw($groupByFormat));
        
        // Urutkan berdasarkan periode waktu
        $query->orderBy(DB::raw('periode_waktu'), 'asc');
        
        $result = $query->get();
        
        // Format data untuk chart
        $labels = [];
        $balita_data = [];
        $pra_sekolah_data = [];
        $remaja_data = [];
        $produktif_data = [];
        $lansia_data = [];
        
        foreach ($result as $row) {
            // Format label berdasarkan periode
            if ($periode === 'minggu') {
                // Format minggu: Minggu ke-X Tahun
                $year = substr($row->periode_waktu, 0, 4);
                $week = substr($row->periode_waktu, 4);
                $labels[] = "Minggu ke-{$week} {$year}";
            } elseif ($periode === 'tahun') {
                // Format tahun: Tahun
                $labels[] = $row->periode_waktu;
            } else {
                // Format bulan: Bulan Tahun
                $bulan_tahun = $this->getNamaBulan($row->bulan) . ' ' . $row->tahun;
                $labels[] = $bulan_tahun;
            }
            
            $balita_data[] = $row->balita;
            $pra_sekolah_data[] = $row->pra_sekolah;
            $remaja_data[] = $row->remaja;
            $produktif_data[] = $row->produktif;
            $lansia_data[] = $row->lansia;
        }
        
        return [
            'labels' => $labels,
            'balita' => $balita_data,
            'pra_sekolah' => $pra_sekolah_data,
            'remaja' => $remaja_data,
            'produktif' => $produktif_data,
            'lansia' => $lansia_data,
            'periode' => $periode,
            'interval' => $interval
        ];
    }
    
    /**
     * Mendapatkan nama bulan dari angka bulan
     * 
     * @param int $bulan
     * @return string
     */
    private function getNamaBulan($bulan)
    {
        $nama_bulan = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];
        
        return $nama_bulan[$bulan] ?? 'Bulan ' . $bulan;
    }
    
    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('pegawai')->where('nik', $kd_dokter)->first();
        return $dokter ? $dokter->nama : 'Dokter';
    }

    /**
     * Ambil data kunjungan berdasarkan posyandu
     * 
     * @param string|null $desa Filter berdasarkan desa
     * @param string $periode (minggu, bulan, tahun)
     * @return array
     */
    private function getKunjunganByPosyandu($desa = null, $periode = 'bulan')
    {
        // Query dasar untuk mengambil data dari ilp_dewasa
        $query = DB::table('ilp_dewasa as id')
            ->join('pasien as p', 'id.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_posyandu as dp', 'id.data_posyandu', '=', 'dp.nama_posyandu')
            ->select(
                'id.data_posyandu as nama_posyandu',
                DB::raw('COUNT(*) as jumlah_kunjungan')
            )
            ->whereNotNull('id.data_posyandu')
            ->where('id.data_posyandu', '!=', '')
            ->where('id.data_posyandu', '!=', '-');
            
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Tentukan interval waktu berdasarkan periode
        switch ($periode) {
            case 'minggu':
                $interval = 12; // 12 minggu terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' WEEK)'));
                break;
                
            case 'tahun':
                $interval = 5; // 5 tahun terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' YEAR)'));
                break;
                
            case 'bulan':
            default:
                $interval = 6; // 6 bulan terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' MONTH)'));
                break;
        }
        
        // Kelompokkan berdasarkan posyandu
        $query->groupBy('id.data_posyandu');
        
        // Urutkan berdasarkan jumlah kunjungan (terbanyak dulu)
        $query->orderBy('jumlah_kunjungan', 'desc');
        
        // Batasi hanya 10 posyandu teratas
        $query->limit(10);
        
        $result = $query->get();
        
        // Format data untuk chart
        $labels = [];
        $data = [];
        
        foreach ($result as $row) {
            $labels[] = $row->nama_posyandu;
            $data[] = $row->jumlah_kunjungan;
        }
        
        return [
            'labels' => $labels,
            'data' => $data,
            'periode' => $periode,
            'interval' => $interval
        ];
    }

    /**
     * Ambil data faktor risiko berdasarkan IMT dan tekanan darah
     * 
     * @param string|null $posyandu Filter berdasarkan posyandu
     * @param string|null $desa Filter berdasarkan desa
     * @param string $periode (minggu, bulan, tahun)
     * @return array
     */
    private function getFaktorRisiko($posyandu = null, $desa = null, $periode = 'bulan')
    {
        // Query dasar untuk mengambil data dari ilp_dewasa
        $query = DB::table('ilp_dewasa as id')
            ->join('pasien as p', 'id.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_posyandu as dp', 'id.data_posyandu', '=', 'dp.nama_posyandu')
            ->select(
                'id.imt',
                'id.td'
            )
            ->whereNotNull('id.data_posyandu')
            ->where('id.data_posyandu', '!=', '')
            ->where('id.data_posyandu', '!=', '-')
            ->whereNotNull('id.imt')
            ->whereNotNull('id.td');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('id.data_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Tentukan interval waktu berdasarkan periode
        switch ($periode) {
            case 'minggu':
                $interval = 12; // 12 minggu terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' WEEK)'));
                break;
                
            case 'tahun':
                $interval = 5; // 5 tahun terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' YEAR)'));
                break;
                
            case 'bulan':
            default:
                $interval = 6; // 6 bulan terakhir
                $query->where('id.tanggal', '>=', DB::raw('DATE_SUB(CURDATE(), INTERVAL ' . $interval . ' MONTH)'));
                break;
        }
        
        $result = $query->get();
        
        // Inisialisasi data untuk kategori IMT
        $imt_categories = [
            'kurus' => 0,
            'normal' => 0,
            'kelebihan_bb' => 0,
            'obesitas' => 0
        ];
        
        // Inisialisasi data untuk kategori tekanan darah
        $td_categories = [
            'normal' => 0,
            'pra_hipertensi' => 0,
            'hipertensi_1' => 0,
            'hipertensi_2' => 0,
            'hipertensi_sistolik' => 0
        ];
        
        // Hitung jumlah untuk setiap kategori
        foreach ($result as $row) {
            // Kategorisasi IMT
            $imt_value = (float) $row->imt;
            if ($imt_value < 18.5) {
                $imt_categories['kurus']++;
            } elseif ($imt_value >= 18.5 && $imt_value <= 24.9) {
                $imt_categories['normal']++;
            } elseif ($imt_value >= 25 && $imt_value <= 29.9) {
                $imt_categories['kelebihan_bb']++;
            } elseif ($imt_value >= 30) {
                $imt_categories['obesitas']++;
            }
            
            // Kategorisasi tekanan darah
            $td_parts = explode('/', $row->td);
            if (count($td_parts) == 2) {
                $sistolik = (int) $td_parts[0];
                $diastolik = (int) $td_parts[1];
                
                if ($sistolik < 120 && $diastolik < 80) {
                    $td_categories['normal']++;
                } elseif (($sistolik >= 120 && $sistolik <= 139) || ($diastolik >= 80 && $diastolik <= 89)) {
                    $td_categories['pra_hipertensi']++;
                } elseif (($sistolik >= 140 && $sistolik <= 159) || ($diastolik >= 90 && $diastolik <= 99)) {
                    $td_categories['hipertensi_1']++;
                } elseif ($sistolik >= 160 || $diastolik >= 100) {
                    $td_categories['hipertensi_2']++;
                }
                
                // Cek hipertensi sistolik terisolasi
                if ($sistolik > 140 && $diastolik < 90) {
                    $td_categories['hipertensi_sistolik']++;
                }
            }
        }
        
        // Ambil data pemeriksaan terakhir
        $last_check = $this->getLastCheck($posyandu, $desa);
        
        return [
            'imt' => $imt_categories,
            'td' => $td_categories,
            'total' => count($result),
            'last_check' => $last_check
        ];
    }
    
    /**
     * Ambil data pemeriksaan terakhir
     * 
     * @param string|null $posyandu Filter berdasarkan posyandu
     * @param string|null $desa Filter berdasarkan desa
     * @return array|null
     */
    private function getLastCheck($posyandu = null, $desa = null)
    {
        // Query dasar untuk mengambil data dari ilp_dewasa
        $query = DB::table('ilp_dewasa as id')
            ->join('pasien as p', 'id.no_rkm_medis', '=', 'p.no_rkm_medis')
            ->leftJoin('data_posyandu as dp', 'id.data_posyandu', '=', 'dp.nama_posyandu')
            ->select(
                'id.imt',
                'id.td',
                'id.berat_badan',
                'id.tinggi_badan',
                'id.tanggal'
            )
            ->whereNotNull('id.data_posyandu')
            ->where('id.data_posyandu', '!=', '')
            ->where('id.data_posyandu', '!=', '-')
            ->whereNotNull('id.imt')
            ->whereNotNull('id.td');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('id.data_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Ambil data terbaru
        $query->orderBy('id.tanggal', 'desc');
        $query->limit(1);
        
        $result = $query->first();
        
        if (!$result) {
            return null;
        }
        
        // Kategorisasi IMT
        $imt_value = (float) $result->imt;
        $imt_category = '';
        $imt_class = '';
        
        if ($imt_value < 18.5) {
            $imt_category = 'Kurus';
            $imt_class = 'info';
        } elseif ($imt_value >= 18.5 && $imt_value <= 24.9) {
            $imt_category = 'Normal';
            $imt_class = 'success';
        } elseif ($imt_value >= 25 && $imt_value <= 29.9) {
            $imt_category = 'Kelebihan Berat Badan';
            $imt_class = 'warning';
        } elseif ($imt_value >= 30) {
            $imt_category = 'Obesitas';
            $imt_class = 'danger';
        }
        
        // Kategorisasi tekanan darah
        $td_parts = explode('/', $result->td);
        $td_category = '';
        $td_class = '';
        
        if (count($td_parts) == 2) {
            $sistolik = (int) $td_parts[0];
            $diastolik = (int) $td_parts[1];
            
            if ($sistolik < 120 && $diastolik < 80) {
                $td_category = 'Normal';
                $td_class = 'success';
            } elseif (($sistolik >= 120 && $sistolik <= 139) || ($diastolik >= 80 && $diastolik <= 89)) {
                $td_category = 'Pra-hipertensi';
                $td_class = 'warning';
            } elseif (($sistolik >= 140 && $sistolik <= 159) || ($diastolik >= 90 && $diastolik <= 99)) {
                $td_category = 'Hipertensi 1';
                $td_class = 'danger';
            } elseif ($sistolik >= 160 || $diastolik >= 100) {
                $td_category = 'Hipertensi 2';
                $td_class = 'danger';
            }
            
            // Cek hipertensi sistolik terisolasi
            if ($sistolik > 140 && $diastolik < 90) {
                $td_category = 'Hipertensi Sistolik Terisolasi';
                $td_class = 'danger';
            }
        }
        
        return [
            'imt' => $result->imt,
            'imt_category' => $imt_category,
            'imt_class' => $imt_class,
            'td' => $result->td,
            'td_category' => $td_category,
            'td_class' => $td_class,
            'berat_badan' => $result->berat_badan,
            'tinggi_badan' => $result->tinggi_badan,
            'tanggal' => $result->tanggal
        ];
    }
} 