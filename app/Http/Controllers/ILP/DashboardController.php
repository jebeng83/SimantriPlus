<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
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
        // Ambil daftar posyandu dari skrining_pkg menggunakan kode_posyandu
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-')
            ->whereNotNull('dp.nama_posyandu')
            ->where('dp.nama_posyandu', '!=', '')
            ->where('dp.nama_posyandu', '!=', '-');

        // Filter berdasarkan desa jika ada
        // Gunakan desa dari tabel data_posyandu agar pemetaan Posyandu ↔ Desa konsisten
        if ($desa) {
            $query->where('dp.desa', $desa);
        }

        return $query->distinct()
            ->pluck('dp.nama_posyandu')
            ->toArray();
    }
    
    /**
     * Ambil daftar desa/kelurahan dari database
     * 
     * @return array
     */
    private function getDaftarDesa()
    {
        // Ambil daftar desa dari tabel data_posyandu agar konsisten dengan filter posyandu
        return DB::table('data_posyandu')
            ->whereNotNull('desa')
            ->where('desa', '!=', '')
            ->where('desa', '!=', '-')
            ->distinct()
            ->orderBy('desa', 'asc')
            ->pluck('desa')
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

     /**
      * Show the PWS (Pemantauan Wilayah Setempat) dashboard for PKG analysis.
      *
      * @return \Illuminate\Contracts\Support\Renderable
      */
    public function dashboardPws(Request $request)
    {
        // Ambil filter posyandu jika ada
        $posyandu_filter = $request->input('posyandu');
        
        // Ambil filter desa jika ada
        $desa_filter = $request->input('desa');
        
        // Ambil filter periode dari UI (bulan_ini, 3_bulan, 6_bulan, tahun_ini)
        $periode_filter = $request->input('periode', 'bulan_ini');
        
        // Tentukan rentang tanggal berdasarkan periode yang dipilih
        switch ($periode_filter) {
            case '3_bulan':
                $tanggal_awal = Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case '6_bulan':
                $tanggal_awal = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'tahun_ini':
                $tanggal_awal = Carbon::now()->startOfYear()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'bulan_ini':
            default:
                $tanggal_awal = Carbon::now()->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
        }
        
        // Ambil daftar desa/kelurahan dari database
        $daftar_desa = $this->getDaftarDesa();
        
        // Ambil daftar posyandu dari database berdasarkan filter desa jika ada
        $daftar_posyandu = $this->getDaftarPosyandu($desa_filter);
        
        // Analisis data PKG berdasarkan posyandu
        $analisis_pkg = $this->getAnalisisPkg($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Analisis faktor risiko dari PKG
        $faktor_risiko_pkg = $this->getFaktorRisikoPkg($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Analisis distribusi umur dan jenis kelamin
        $distribusi_demografi = $this->getDistribusiDemografi($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Analisis status kesehatan berdasarkan hasil skrining
        $status_kesehatan = $this->getStatusKesehatan($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Trend skrining PKG per periode
        $trend_skrining = $this->getTrendSkrining($posyandu_filter, $desa_filter, $periode_filter);
        
        // Summary statistics for dashboard cards
        $summary = $this->getSummaryPkg($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Chart data for visualizations - menggunakan method perbaikan
        $chart_data = $this->getFaktorRisikoFixed($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        return view('ilp.dashboard_ilp', compact(
            'daftar_desa',
            'daftar_posyandu',
            'analisis_pkg',
            'faktor_risiko_pkg',
            'distribusi_demografi',
            'status_kesehatan',
            'trend_skrining',
            'summary',
            'chart_data',
            'posyandu_filter',
            'desa_filter',
            'periode_filter',
            'tanggal_awal',
            'tanggal_akhir'
        ));
    }

    /**
     * AJAX: Ambil Analisis per Posyandu dengan pagination
     * Params: desa, posyandu, periode, page, per_page
     */
    public function getAnalisisPkgAjax(Request $request)
    {
        $posyandu_filter = $request->input('posyandu');
        $desa_filter = $request->input('desa');
        $periode_filter = $request->input('periode', 'bulan_ini');
        $perPage = (int) $request->input('per_page', 10);
        $page = (int) $request->input('page', 1);

        // Rentang tanggal sesuai periode
        switch ($periode_filter) {
            case '3_bulan':
                $tanggal_awal = Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case '6_bulan':
                $tanggal_awal = Carbon::now()->subMonths(5)->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'tahun_ini':
                $tanggal_awal = Carbon::now()->startOfYear()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            case 'bulan_ini':
            default:
                $tanggal_awal = Carbon::now()->startOfMonth()->format('Y-m-d');
                $tanggal_akhir = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
        }

        // Query dengan agregasi dan groupBy
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->leftJoin('skrining_siswa_sd as ssd', 'ssd.id_pkg', '=', 'sp.id_pkg')
            ->select(
                'dp.nama_posyandu',
                'dp.desa as desa',
                DB::raw('COUNT(sp.id_pkg) as total_skrining'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "L" THEN 1 END) as laki_laki'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "P" THEN 1 END) as perempuan'),
                // Risiko Tinggi: klinis tinggi ATAU kombinasi riwayat kuat
                DB::raw('COUNT(CASE 
                    WHEN (
                        (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                        OR (sp.gds >= 200 OR sp.gdp >= 126)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                        OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                            OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                            OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                    ) THEN 1 END) as risiko_tinggi'),
                // Risiko Sedang: klinis sedang ATAU riwayat tunggal, tetapi bukan risiko tinggi
                DB::raw('COUNT(CASE 
                    WHEN (
                        (
                            (sp.tekanan_sistolik BETWEEN 120 AND 139)
                            OR (sp.tekanan_diastolik BETWEEN 80 AND 89)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30)
                            OR sp.status_merokok = "Ya"
                            OR sp.riwayat_hipertensi = "Ya"
                            OR sp.riwayat_diabetes = "Ya"
                        )
                        AND NOT (
                            (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                            OR (sp.gds >= 200 OR sp.gdp >= 126)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                            OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                                OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                                OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                        )
                    ) THEN 1 END) as risiko_sedang'),
                // Risiko Rendah: bukan tinggi dan bukan sedang
                DB::raw('COUNT(CASE 
                    WHEN NOT (
                        (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                        OR (sp.gds >= 200 OR sp.gdp >= 126)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                        OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                            OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                            OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                        OR (
                            (sp.tekanan_sistolik BETWEEN 120 AND 139)
                            OR (sp.tekanan_diastolik BETWEEN 80 AND 89)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30)
                            OR sp.status_merokok = "Ya"
                            OR sp.riwayat_hipertensi = "Ya"
                            OR sp.riwayat_diabetes = "Ya"
                        )
                    ) THEN 1 END) as risiko_rendah'),
                // Definisi klinis tambahan per posyandu
                DB::raw('COUNT(CASE WHEN COALESCE(sp.tekanan_sistolik, ssd.sistole) >= 140 THEN 1 END) as td_ge_140'),
                DB::raw('COUNT(CASE WHEN COALESCE(sp.gds, ssd.hasil_gds) >= 200 THEN 1 END) as gds_ge_200'),
                DB::raw('COUNT(CASE WHEN sp.gdp >= 126 THEN 1 END) as gdp_ge_126'),
                DB::raw('COUNT(CASE WHEN (
                    (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                    OR (ssd.imt >= 30)
                ) THEN 1 END) as bmi_ge_30')
            )
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-');

        if ($posyandu_filter) {
            $query->where('dp.nama_posyandu', $posyandu_filter);
        }
        if ($desa_filter) {
            $query->where('dp.desa', $desa_filter);
        }
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }

        $query->groupBy('dp.nama_posyandu', 'dp.desa');
        // Urutkan berdasarkan total skrining desc agar lebih informatif
        $query->orderByDesc(DB::raw('COUNT(sp.id_pkg)'));

        // Manual pagination (karena agregasi GROUP BY)
        $all = $query->get();
        $total = $all->count();
        $lastPage = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $lastPage));
        $items = $all->slice(($page - 1) * $perPage, $perPage)->values();

        // Tambahkan persen risiko tinggi untuk tiap item
        $items = $items->map(function($item){
            $total = (int) ($item->total_skrining ?? 0);
            $tinggi = (int) ($item->risiko_tinggi ?? 0);
            $item->persen_tinggi = $total > 0 ? round(($tinggi / $total) * 100, 1) : 0;
            return $item;
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => $lastPage,
            ],
        ]);
    }

    /**
     * Analisis data PKG berdasarkan posyandu
     */
    private function getAnalisisPkg($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                'dp.nama_posyandu',
                'dp.desa as desa',
                DB::raw('COUNT(sp.id_pkg) as total_skrining'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "L" THEN 1 END) as laki_laki'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "P" THEN 1 END) as perempuan'),
                DB::raw('AVG(sp.umur) as rata_rata_umur'),
                DB::raw('COUNT(CASE WHEN sp.status = "1" THEN 1 END) as selesai'),
                DB::raw('COUNT(CASE WHEN sp.status = "0" THEN 1 END) as belum_selesai'),
                DB::raw('COUNT(CASE WHEN sp.riwayat_hipertensi = "Ya" THEN 1 END) as hipertensi'),
                DB::raw('COUNT(CASE WHEN sp.riwayat_diabetes = "Ya" THEN 1 END) as diabetes'),
                DB::raw('COUNT(CASE WHEN sp.status_merokok = "Ya" THEN 1 END) as merokok'),
                // Risk level calculations seragam: gabungan klinis + riwayat + usia
                DB::raw('COUNT(CASE 
                    WHEN (
                        (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                        OR (sp.gds >= 200 OR sp.gdp >= 126)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                        OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                            OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                            OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                    ) THEN 1 END) as risiko_tinggi'),
                DB::raw('COUNT(CASE 
                    WHEN (
                        (
                            (sp.tekanan_sistolik BETWEEN 120 AND 139)
                            OR (sp.tekanan_diastolik BETWEEN 80 AND 89)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30)
                            OR sp.status_merokok = "Ya"
                            OR sp.riwayat_hipertensi = "Ya"
                            OR sp.riwayat_diabetes = "Ya"
                        )
                        AND NOT (
                            (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                            OR (sp.gds >= 200 OR sp.gdp >= 126)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                            OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                                OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                                OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                        )
                    ) THEN 1 END) as risiko_sedang'),
                DB::raw('COUNT(CASE 
                    WHEN NOT (
                        (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                        OR (sp.gds >= 200 OR sp.gdp >= 126)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                        OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                            OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                            OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                        OR (
                            (sp.tekanan_sistolik BETWEEN 120 AND 139)
                            OR (sp.tekanan_diastolik BETWEEN 80 AND 89)
                            OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30)
                            OR sp.status_merokok = "Ya"
                            OR sp.riwayat_hipertensi = "Ya"
                            OR sp.riwayat_diabetes = "Ya"
                        )
                    ) THEN 1 END) as risiko_rendah')
            )
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada (konsisten: dp.desa atau k.nm_kel)
        if ($desa) {
            $query->where('dp.desa', $desa);
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        return $query->groupBy('dp.nama_posyandu', 'dp.desa')->get();
    }

    /**
     * Analisis faktor risiko dari data PKG
     */
    private function getFaktorRisikoPkg($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('COUNT(CASE WHEN sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90 THEN 1 END) as hipertensi_terdeteksi'),
                DB::raw('COUNT(CASE WHEN sp.gds >= 200 OR sp.gdp >= 126 THEN 1 END) as diabetes_terdeteksi'),
                DB::raw('COUNT(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 THEN 1 END) as obesitas'),
                DB::raw('COUNT(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 18.5 THEN 1 END) as underweight'),
                DB::raw('COUNT(CASE WHEN sp.status_merokok = "Ya" THEN 1 END) as perokok_aktif'),
                DB::raw('COUNT(CASE WHEN sp.paparan_asap = "Ya" THEN 1 END) as perokok_pasif'),
                DB::raw('COUNT(CASE WHEN sp.frekuensi_olahraga = "Tidak Pernah" THEN 1 END) as tidak_olahraga'),
                DB::raw('COUNT(CASE WHEN sp.kolesterol_lab > 200 THEN 1 END) as kolesterol_tinggi'),
                DB::raw('COUNT(sp.id_pkg) as total_skrining')
            )
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada (konsisten dengan pemetaan di data_posyandu)
        if ($desa) {
            $query->where(function($q) use ($desa) {
                $q->where('dp.desa', $desa)
                  ->orWhere('k.nm_kel', $desa);
            });
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        return $query->first();
    }

    /**
     * Analisis distribusi demografi
     */
    private function getDistribusiDemografi($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('COUNT(CASE WHEN sp.umur BETWEEN 0 AND 17 THEN 1 END) as anak'),
                DB::raw('COUNT(CASE WHEN sp.umur BETWEEN 18 AND 59 THEN 1 END) as dewasa'),
                DB::raw('COUNT(CASE WHEN sp.umur >= 60 THEN 1 END) as lansia'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "L" THEN 1 END) as laki_laki'),
                DB::raw('COUNT(CASE WHEN sp.jenis_kelamin = "P" THEN 1 END) as perempuan'),
                DB::raw('COUNT(CASE WHEN sp.status_perkawinan = "Menikah" THEN 1 END) as menikah'),
                DB::raw('COUNT(CASE WHEN sp.status_perkawinan = "Belum Menikah" THEN 1 END) as belum_menikah'),
                DB::raw('COUNT(sp.id_pkg) as total')
            )
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('k.nm_kel', $desa);
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        return $query->first();
    }

    /**
     * Analisis status kesehatan berdasarkan hasil skrining
     */
    private function getStatusKesehatan($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('COUNT(CASE WHEN sp.sedih = "Ya" OR sp.cemas = "Ya" OR sp.khawatir = "Ya" THEN 1 END) as masalah_mental'),
                DB::raw('COUNT(CASE WHEN sp.karies = "Ya" OR sp.hilang = "Ya" OR sp.goyang = "Ya" THEN 1 END) as masalah_gigi'),
                DB::raw('COUNT(CASE WHEN sp.pendengaran = "Terganggu" THEN 1 END) as gangguan_pendengaran'),
                DB::raw('COUNT(CASE WHEN sp.penglihatan = "Terganggu" THEN 1 END) as gangguan_penglihatan'),
                DB::raw('COUNT(CASE WHEN sp.batuk = "Ya" OR sp.dahak = "Ya" OR sp.napas_pendek = "Ya" THEN 1 END) as gejala_tb'),
                DB::raw('COUNT(CASE WHEN sp.riwayat_hepatitis = "Ya" OR sp.riwayat_kuning = "Ya" THEN 1 END) as risiko_hepatitis'),
                DB::raw('COUNT(sp.id_pkg) as total')
            )
            ->whereNotNull('sp.kode_posyandu')
            ->where('sp.kode_posyandu', '!=', '')
            ->where('sp.kode_posyandu', '!=', '-');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('k.nm_kel', $desa);
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        return $query->first();
    }

    /**
     * Trend skrining PKG per periode
     */
    private function getTrendSkrining($posyandu = null, $desa = null, $periode = 'bulan')
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel');
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada
        if ($desa) {
            $query->where('k.nm_kel', $desa);
        }
        
        // Grouping berdasarkan periode
        switch ($periode) {
            case 'minggu':
                $query->select(
                    DB::raw('YEARWEEK(sp.tanggal_skrining) as periode'),
                    DB::raw('CONCAT("Minggu ", WEEK(sp.tanggal_skrining), " - ", YEAR(sp.tanggal_skrining)) as label'),
                    DB::raw('COUNT(sp.id_pkg) as total_skrining')
                )->groupBy(DB::raw('YEARWEEK(sp.tanggal_skrining)'));
                break;
            case 'tahun':
                $query->select(
                    DB::raw('YEAR(sp.tanggal_skrining) as periode'),
                    DB::raw('YEAR(sp.tanggal_skrining) as label'),
                    DB::raw('COUNT(sp.id_pkg) as total_skrining')
                )->groupBy(DB::raw('YEAR(sp.tanggal_skrining)'));
                break;
            default: // bulan
                $query->select(
                    DB::raw('DATE_FORMAT(sp.tanggal_skrining, "%Y-%m") as periode'),
                    DB::raw('DATE_FORMAT(sp.tanggal_skrining, "%M %Y") as label'),
                    DB::raw('COUNT(sp.id_pkg) as total_skrining')
                )->groupBy(DB::raw('DATE_FORMAT(sp.tanggal_skrining, "%Y-%m")'));
                break;
        }
        
        return $query->orderBy('periode')->get();
    }

    /**
     * Get summary statistics for PKG screening
     */
    private function getSummaryPkg($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as dp', 'sp.kode_posyandu', '=', 'dp.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('COUNT(sp.id_pkg) as total_skrining'),
                // Risiko Tinggi seragam: klinis tinggi ATAU kombinasi riwayat kuat
                DB::raw('COUNT(CASE 
                    WHEN (
                        (sp.tekanan_sistolik >= 140 OR sp.tekanan_diastolik >= 90)
                        OR (sp.gds >= 200 OR sp.gdp >= 126)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30)
                        OR ((sp.riwayat_hipertensi = "Ya" AND sp.riwayat_diabetes = "Ya")
                            OR (sp.status_merokok = "Ya" AND sp.riwayat_hipertensi = "Ya")
                            OR (sp.umur >= 60 AND sp.riwayat_diabetes = "Ya"))
                    ) THEN 1 END) as risiko_tinggi'),
                // Risiko Sedang kandidat: klinis sedang ATAU riwayat tunggal (akan dikurangi risiko_tinggi)
                DB::raw('COUNT(CASE 
                    WHEN (
                        (sp.tekanan_sistolik BETWEEN 120 AND 139)
                        OR (sp.tekanan_diastolik BETWEEN 80 AND 89)
                        OR (sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 30)
                        OR sp.status_merokok = "Ya"
                        OR sp.riwayat_hipertensi = "Ya"
                        OR sp.riwayat_diabetes = "Ya"
                    ) THEN 1 END) as risiko_sedang_temp')
            );
            // Untuk summary kartu, sertakan seluruh data skrining tanpa mengecualikan kode_posyandu kosong atau tanda '-'
            // agar total dan distribusi risiko tetap muncul meski belum terpetakan ke posyandu tertentu.
            
        // Filter berdasarkan posyandu jika ada
        if ($posyandu) {
            $query->where('dp.nama_posyandu', $posyandu);
        }
        
        // Filter berdasarkan desa jika ada (konsisten: dp.desa atau k.nm_kel)
        if ($desa) {
            $query->where(function($q) use ($desa) {
                $q->where('dp.desa', $desa)
                  ->orWhere('k.nm_kel', $desa);
            });
        }
        
        // Filter berdasarkan tanggal jika ada
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }

        $result = $query->first();
        
        $total_skrining = (int) ($result->total_skrining ?? 0);
        $risiko_tinggi = (int) ($result->risiko_tinggi ?? 0);
        
        // Calculate medium risk excluding those already in high risk
        $risiko_sedang = max(0, (int) ($result->risiko_sedang_temp ?? 0) - $risiko_tinggi);
        
        // Calculate low risk
        $risiko_rendah = max(0, $total_skrining - $risiko_tinggi - $risiko_sedang);
        
        return [
            'total_skrining' => $total_skrining,
            'risiko_tinggi' => $risiko_tinggi,
            'risiko_sedang' => $risiko_sedang,
            'risiko_rendah' => $risiko_rendah
        ];
    }

    /**
     * Generate chart data for dashboard visualizations
     */
    private function getChartData($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir)
    {
        // Get summary data for risk distribution
        $summary = $this->getSummaryPkg($posyandu_filter, $desa_filter, $tanggal_awal, $tanggal_akhir);
        
        // Get trend data (simplified - using monthly data for the last 6 months)
        $trend_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month_start = now()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $month_end = now()->subMonths($i)->endOfMonth()->format('Y-m-d');
            $month_summary = $this->getSummaryPkg($posyandu_filter, $desa_filter, $month_start, $month_end);
            
            $trend_data[] = [
                'bulan' => now()->subMonths($i)->format('M Y'),
                'total' => $month_summary['total_skrining']
            ];
        }
        
        // Get factor risk data
        $faktor_risiko = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('SUM(CASE WHEN sp.riwayat_hipertensi = "Ya" THEN 1 ELSE 0 END) as hipertensi'),
                DB::raw('SUM(CASE WHEN sp.riwayat_diabetes = "Ya" THEN 1 ELSE 0 END) as diabetes'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" THEN 1 ELSE 0 END) as merokok'),
                DB::raw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as lansia')
            );
            
        if ($posyandu_filter && $posyandu_filter != 'semua') {
            $faktor_risiko->where('p.nama_posyandu', $posyandu_filter);
        }
        
        if ($desa_filter && $desa_filter != 'semua') {
            // Filter desa berdasarkan data_posyandu agar sesuai dengan daftar posyandu
            $faktor_risiko->where('p.desa', $desa_filter);
        }
        
        if ($tanggal_awal) {
            $faktor_risiko->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $faktor_risiko->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $faktor_data = $faktor_risiko->first();
        
        // Get age distribution (CKG) berdasarkan Sasaran Usia
        $distribusi_umur = DB::table('skrining_pkg as sp')
            ->leftJoin('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->leftJoin('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('SUM(CASE WHEN sp.umur < 6 THEN 1 ELSE 0 END) as balita'),
                DB::raw('SUM(CASE WHEN sp.umur >= 6 AND sp.umur <= 10 THEN 1 ELSE 0 END) as pra_sekolah'),
                DB::raw('SUM(CASE WHEN sp.umur > 10 AND sp.umur <= 18 THEN 1 ELSE 0 END) as remaja'),
                DB::raw('SUM(CASE WHEN sp.umur > 18 AND sp.umur <= 59 THEN 1 ELSE 0 END) as dewasa'),
                DB::raw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as lansia')
            );
            
        if ($posyandu_filter && $posyandu_filter != 'semua') {
            $distribusi_umur->where('p.nama_posyandu', $posyandu_filter);
        }
        
        if ($desa_filter && $desa_filter != 'semua') {
            // Filter desa berdasarkan data_posyandu agar sesuai dengan daftar posyandu
            $distribusi_umur->where('p.desa', $desa_filter);
        }
        
        if ($tanggal_awal) {
            $distribusi_umur->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $distribusi_umur->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $umur_data = $distribusi_umur->first();
        
        return [
            'distribusi_risiko' => [
                'risiko_tinggi' => $summary['risiko_tinggi'],
                'risiko_sedang' => $summary['risiko_sedang'],
                'risiko_rendah' => $summary['risiko_rendah']
            ],
            'trend_skrining' => $trend_data,
            'faktor_risiko' => [
                ['faktor' => 'Hipertensi', 'jumlah' => $faktor_data->hipertensi ?? 0],
                ['faktor' => 'Diabetes', 'jumlah' => $faktor_data->diabetes ?? 0],
                ['faktor' => 'Merokok', 'jumlah' => $faktor_data->merokok ?? 0],
                ['faktor' => 'Lansia (≥60 tahun)', 'jumlah' => $faktor_data->lansia ?? 0]
            ],
            'distribusi_umur' => [
                ['kelompok_umur' => 'Balita (<6 th)', 'jumlah' => $umur_data->balita ?? 0],
                ['kelompok_umur' => 'Pra Sekolah (6-10 th)', 'jumlah' => $umur_data->pra_sekolah ?? 0],
                ['kelompok_umur' => 'Remaja (11-18 th)', 'jumlah' => $umur_data->remaja ?? 0],
                ['kelompok_umur' => 'Dewasa (19-59 th)', 'jumlah' => $umur_data->dewasa ?? 0],
                ['kelompok_umur' => 'Lansia (≥60 th)', 'jumlah' => $umur_data->lansia ?? 0]
            ]
        ];
    }

    /**
     * Method perbaikan untuk menggantikan getChartData yang bermasalah
     * Menggunakan join dengan data_posyandu dan kolom dengan prefix yang benar
     */
    private function getFaktorRisikoFixed($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        // Get summary data untuk distribusi risiko
        $summary = $this->getSummaryPkg($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get trend data
        $trend_data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month_start = now()->subMonths($i)->startOfMonth()->format('Y-m-d');
            $month_end = now()->subMonths($i)->endOfMonth()->format('Y-m-d');
            $month_summary = $this->getSummaryPkg($posyandu, $desa, $month_start, $month_end);
            
            $trend_data[] = [
                 'bulan' => now()->subMonths($i)->format('M Y'),
                 'total' => (int) ($month_summary['total_skrining'] ?? 0)
             ];
        }
        
        // Get factor risk data dengan query yang diperbaiki
        $faktor_risiko = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->join('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('SUM(CASE WHEN sp.riwayat_hipertensi = "Ya" THEN 1 ELSE 0 END) as hipertensi'),
                DB::raw('SUM(CASE WHEN sp.riwayat_diabetes = "Ya" THEN 1 ELSE 0 END) as diabetes'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" THEN 1 ELSE 0 END) as merokok'),
                DB::raw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as lansia')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $faktor_risiko->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            // Filter desa berdasarkan data_posyandu agar sesuai dengan daftar posyandu
            $faktor_risiko->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $faktor_risiko->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $faktor_risiko->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $faktor_data = $faktor_risiko->first();
        
        // Get age distribution (CKG) berdasarkan Sasaran Usia dengan query yang diperbaiki
        $distribusi_umur = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->join('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('SUM(CASE WHEN sp.umur < 6 THEN 1 ELSE 0 END) as balita'),
                DB::raw('SUM(CASE WHEN sp.umur >= 6 AND sp.umur <= 10 THEN 1 ELSE 0 END) as pra_sekolah'),
                DB::raw('SUM(CASE WHEN sp.umur > 10 AND sp.umur <= 18 THEN 1 ELSE 0 END) as remaja'),
                DB::raw('SUM(CASE WHEN sp.umur > 18 AND sp.umur <= 59 THEN 1 ELSE 0 END) as dewasa'),
                DB::raw('SUM(CASE WHEN sp.umur >= 60 THEN 1 ELSE 0 END) as lansia')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $distribusi_umur->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            // Filter desa berdasarkan data_posyandu agar sesuai dengan daftar posyandu
            $distribusi_umur->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $distribusi_umur->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $distribusi_umur->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $umur_data = $distribusi_umur->first();
        
        // Get gender distribution (CKG) berdasarkan Sasaran Jenis Kelamin dengan query yang diperbaiki
        $distribusi_jenis_kelamin = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->join('kelurahan as k', 'sp.kd_kel', '=', 'k.kd_kel')
            ->select(
                DB::raw('SUM(CASE WHEN sp.jenis_kelamin = "L" THEN 1 ELSE 0 END) as laki_laki'),
                DB::raw('SUM(CASE WHEN sp.jenis_kelamin = "P" THEN 1 ELSE 0 END) as perempuan')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $distribusi_jenis_kelamin->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            // Filter desa berdasarkan data_posyandu agar sesuai dengan daftar posyandu
            $distribusi_jenis_kelamin->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $distribusi_jenis_kelamin->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $distribusi_jenis_kelamin->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $jenis_kelamin_data = $distribusi_jenis_kelamin->first();
        
        // Get PKG visits by village (desa)
        $kunjungan_per_desa = $this->getKunjunganPerDesa($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get PKG visits by posyandu from villages
        $kunjungan_per_posyandu_desa = $this->getKunjunganPerPosyanduDesa($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get smoking analysis data
        $analisa_rokok = $this->getAnalisaRokok($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get BMI distribution data
        $distribusi_bmi = $this->getDistribusiBMI($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get hearing and vision analysis data
        $analisa_pendengaran_penglihatan = $this->getAnalisaPendengaranPenglihatan($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Get dental health analysis data
        $analisa_kesehatan_gigi = $this->getAnalisaKesehatanGigi($posyandu, $desa, $tanggal_awal, $tanggal_akhir);
        
        // Return struktur data yang sama dengan getChartData
         return [
             'distribusi_risiko' => [
                 'risiko_tinggi' => $summary['risiko_tinggi'] ?? 0,
                 'risiko_sedang' => $summary['risiko_sedang'] ?? 0,
                 'risiko_rendah' => $summary['risiko_rendah'] ?? 0
             ],
            'trend_skrining' => $trend_data,
            'faktor_risiko' => [
                ['faktor' => 'Hipertensi', 'jumlah' => (int) ($faktor_data->hipertensi ?? 0)],
                ['faktor' => 'Diabetes', 'jumlah' => (int) ($faktor_data->diabetes ?? 0)],
                ['faktor' => 'Merokok', 'jumlah' => (int) ($faktor_data->merokok ?? 0)],
                ['faktor' => 'Lansia (≥60 tahun)', 'jumlah' => (int) ($faktor_data->lansia ?? 0)]
            ],
            'distribusi_umur' => [
                ['kelompok_umur' => 'Balita (<6 th)', 'jumlah' => (int) ($umur_data->balita ?? 0)],
                ['kelompok_umur' => 'Pra Sekolah (6-10 th)', 'jumlah' => (int) ($umur_data->pra_sekolah ?? 0)],
                ['kelompok_umur' => 'Remaja (11-18 th)', 'jumlah' => (int) ($umur_data->remaja ?? 0)],
                ['kelompok_umur' => 'Dewasa (19-59 th)', 'jumlah' => (int) ($umur_data->dewasa ?? 0)],
                ['kelompok_umur' => 'Lansia (≥60 th)', 'jumlah' => (int) ($umur_data->lansia ?? 0)]
            ],
            'distribusi_jenis_kelamin' => [
                ['jenis_kelamin' => 'Laki-laki', 'jumlah' => (int) ($jenis_kelamin_data->laki_laki ?? 0)],
                ['jenis_kelamin' => 'Perempuan', 'jumlah' => (int) ($jenis_kelamin_data->perempuan ?? 0)]
            ],
            'kunjungan_per_desa' => $kunjungan_per_desa,
            'kunjungan_per_posyandu_desa' => $kunjungan_per_posyandu_desa,
            'analisa_rokok' => $analisa_rokok,
            'distribusi_bmi' => $distribusi_bmi,
            'analisa_pendengaran_penglihatan' => $analisa_pendengaran_penglihatan,
            'analisa_kesehatan_gigi' => $analisa_kesehatan_gigi
        ];
    }

    /**
     * Fungsi utilitas untuk menentukan label Sasaran Usia berdasarkan umur (tahun)
     */
    private function sasaranUsia($umur)
    {
        if ($umur === null) {
            return 'Tidak Diketahui';
        }
        if ($umur < 6) {
            return 'Balita';
        } elseif ($umur <= 10) {
            return 'Pra Sekolah';
        } elseif ($umur <= 18) {
            return 'Remaja';
        } elseif ($umur <= 59) {
            return 'Dewasa';
        }
        return 'Lansia';
    }

    /**
     * Get PKG visits per village (desa)
     */
    private function getKunjunganPerDesa($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                'p.desa',
                DB::raw('COUNT(sp.id_pkg) as total_kunjungan')
            )
            ->whereNotNull('p.desa')
            ->where('p.desa', '!=', '')
            ->where('p.desa', '!=', '-');
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $result = $query->groupBy('p.desa')
                       ->orderByDesc('total_kunjungan')
                       ->limit(10) // Top 10 villages
                       ->get();
        
        return $result->map(function($item) {
            return [
                'desa' => $item->desa,
                'jumlah' => (int) ($item->total_kunjungan ?? 0)
            ];
        })->toArray();
    }

    /**
     * Get PKG visits per posyandu from villages
     */
    private function getKunjunganPerPosyanduDesa($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                'p.nama_posyandu',
                'p.desa',
                DB::raw('COUNT(sp.id_pkg) as total_kunjungan')
            )
            ->whereNotNull('p.nama_posyandu')
            ->where('p.nama_posyandu', '!=', '')
            ->where('p.nama_posyandu', '!=', '-')
            ->whereNotNull('p.desa')
            ->where('p.desa', '!=', '')
            ->where('p.desa', '!=', '-');
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $result = $query->groupBy('p.nama_posyandu', 'p.desa')
                       ->orderByDesc('total_kunjungan')
                       ->limit(15) // Top 15 posyandu
                       ->get();
        
        return $result->map(function($item) {
            return [
                'posyandu' => $item->nama_posyandu,
                'desa' => $item->desa,
                'label' => $item->nama_posyandu . ' (' . $item->desa . ')',
                'jumlah' => (int) ($item->total_kunjungan ?? 0)
            ];
        })->toArray();
    }

    /**
     * Get hearing and vision analysis from skrining_pkg table
     */
    private function getAnalisaPendengaranPenglihatan($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                // Hearing analysis
                DB::raw('SUM(CASE WHEN sp.pendengaran = "Normal" THEN 1 ELSE 0 END) as pendengaran_normal'),
                DB::raw('SUM(CASE WHEN sp.pendengaran = "Terganggu" THEN 1 ELSE 0 END) as pendengaran_terganggu'),
                DB::raw('SUM(CASE WHEN sp.pendengaran IS NULL OR sp.pendengaran = "" THEN 1 ELSE 0 END) as pendengaran_tidak_diperiksa'),
                
                // Vision analysis
                DB::raw('SUM(CASE WHEN sp.penglihatan = "Normal" THEN 1 ELSE 0 END) as penglihatan_normal'),
                DB::raw('SUM(CASE WHEN sp.penglihatan = "Terganggu" THEN 1 ELSE 0 END) as penglihatan_terganggu'),
                DB::raw('SUM(CASE WHEN sp.penglihatan IS NULL OR sp.penglihatan = "" THEN 1 ELSE 0 END) as penglihatan_tidak_diperiksa'),
                
                // Combined issues analysis
                DB::raw('SUM(CASE WHEN sp.pendengaran = "Terganggu" AND sp.penglihatan = "Terganggu" THEN 1 ELSE 0 END) as gangguan_ganda'),
                DB::raw('SUM(CASE WHEN (sp.pendengaran = "Terganggu" OR sp.penglihatan = "Terganggu") THEN 1 ELSE 0 END) as ada_gangguan'),
                DB::raw('SUM(CASE WHEN sp.pendengaran = "Normal" AND sp.penglihatan = "Normal" THEN 1 ELSE 0 END) as kedua_normal'),
                
                // Total count for validation
                DB::raw('COUNT(sp.id_pkg) as total_skrining')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $raw_data = $query->first();
        
        // Process data into chart-friendly format with integer casting (following memory lessons)
        return [
            'pendengaran' => [
                ['kategori' => 'Normal', 'jumlah' => (int) ($raw_data->pendengaran_normal ?? 0)],
                ['kategori' => 'Terganggu', 'jumlah' => (int) ($raw_data->pendengaran_terganggu ?? 0)],
                ['kategori' => 'Tidak Diperiksa', 'jumlah' => (int) ($raw_data->pendengaran_tidak_diperiksa ?? 0)]
            ],
            'penglihatan' => [
                ['kategori' => 'Normal', 'jumlah' => (int) ($raw_data->penglihatan_normal ?? 0)],
                ['kategori' => 'Terganggu', 'jumlah' => (int) ($raw_data->penglihatan_terganggu ?? 0)],
                ['kategori' => 'Tidak Diperiksa', 'jumlah' => (int) ($raw_data->penglihatan_tidak_diperiksa ?? 0)]
            ],
            'gangguan_gabungan' => [
                ['kategori' => 'Kedua Normal', 'jumlah' => (int) ($raw_data->kedua_normal ?? 0)],
                ['kategori' => 'Ada Gangguan', 'jumlah' => (int) ($raw_data->ada_gangguan ?? 0)],
                ['kategori' => 'Gangguan Ganda', 'jumlah' => (int) ($raw_data->gangguan_ganda ?? 0)]
            ],
            'summary' => [
                'total_gangguan_pendengaran' => (int) ($raw_data->pendengaran_terganggu ?? 0),
                'total_gangguan_penglihatan' => (int) ($raw_data->penglihatan_terganggu ?? 0),
                'total_gangguan_ganda' => (int) ($raw_data->gangguan_ganda ?? 0),
                'total_skrining' => (int) ($raw_data->total_skrining ?? 0)
            ]
        ];
    }

    /**
     * Get dental health analysis from skrining_pkg table only (without skrining_siswa_sd join)
     */
    private function getAnalisaKesehatanGigi($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                // Dental problems from skrining_pkg only
                DB::raw('SUM(CASE WHEN sp.karies = "Ya" THEN 1 ELSE 0 END) as karies'),
                DB::raw('SUM(CASE WHEN sp.hilang = "Ya" THEN 1 ELSE 0 END) as hilang'),
                DB::raw('SUM(CASE WHEN sp.goyang = "Ya" THEN 1 ELSE 0 END) as goyang'),
                
                // Multiple problems analysis
                DB::raw('SUM(CASE WHEN sp.karies = "Ya" AND sp.hilang = "Ya" THEN 1 ELSE 0 END) as karies_dan_hilang'),
                DB::raw('SUM(CASE WHEN sp.karies = "Ya" AND sp.goyang = "Ya" THEN 1 ELSE 0 END) as karies_dan_goyang'),
                DB::raw('SUM(CASE WHEN sp.hilang = "Ya" AND sp.goyang = "Ya" THEN 1 ELSE 0 END) as hilang_dan_goyang'),
                DB::raw('SUM(CASE WHEN sp.karies = "Ya" AND sp.hilang = "Ya" AND sp.goyang = "Ya" THEN 1 ELSE 0 END) as tiga_masalah'),
                
                // Healthy teeth (no dental problems)
                DB::raw('SUM(CASE WHEN (sp.karies != "Ya" OR sp.karies IS NULL) AND (sp.hilang != "Ya" OR sp.hilang IS NULL) AND (sp.goyang != "Ya" OR sp.goyang IS NULL) THEN 1 ELSE 0 END) as gigi_sehat'),
                
                // At least one problem
                DB::raw('SUM(CASE WHEN (sp.karies = "Ya" OR sp.hilang = "Ya" OR sp.goyang = "Ya") THEN 1 ELSE 0 END) as ada_masalah'),
                
                // Total count for validation
                DB::raw('COUNT(sp.id_pkg) as total_skrining')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $raw_data = $query->first();
        
        // Process data into chart-friendly format with integer casting (following memory lessons)
        return [
            'masalah_gigi' => [
                ['kategori' => 'Karies', 'jumlah' => (int) ($raw_data->karies ?? 0)],
                ['kategori' => 'Gigi Hilang', 'jumlah' => (int) ($raw_data->hilang ?? 0)],
                ['kategori' => 'Gigi Goyang', 'jumlah' => (int) ($raw_data->goyang ?? 0)],
                ['kategori' => 'Gigi Sehat', 'jumlah' => (int) ($raw_data->gigi_sehat ?? 0)]
            ],
            'kombinasi_masalah' => [
                ['kategori' => 'Hanya Karies', 'jumlah' => max(0, (int) ($raw_data->karies ?? 0) - (int) ($raw_data->karies_dan_hilang ?? 0) - (int) ($raw_data->karies_dan_goyang ?? 0) + (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Hanya Hilang', 'jumlah' => max(0, (int) ($raw_data->hilang ?? 0) - (int) ($raw_data->karies_dan_hilang ?? 0) - (int) ($raw_data->hilang_dan_goyang ?? 0) + (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Hanya Goyang', 'jumlah' => max(0, (int) ($raw_data->goyang ?? 0) - (int) ($raw_data->karies_dan_goyang ?? 0) - (int) ($raw_data->hilang_dan_goyang ?? 0) + (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Karies + Hilang', 'jumlah' => max(0, (int) ($raw_data->karies_dan_hilang ?? 0) - (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Karies + Goyang', 'jumlah' => max(0, (int) ($raw_data->karies_dan_goyang ?? 0) - (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Hilang + Goyang', 'jumlah' => max(0, (int) ($raw_data->hilang_dan_goyang ?? 0) - (int) ($raw_data->tiga_masalah ?? 0))],
                ['kategori' => 'Tiga Masalah', 'jumlah' => (int) ($raw_data->tiga_masalah ?? 0)],
                ['kategori' => 'Gigi Sehat', 'jumlah' => (int) ($raw_data->gigi_sehat ?? 0)]
            ],
            'summary' => [
                'total_karies' => (int) ($raw_data->karies ?? 0),
                'total_hilang' => (int) ($raw_data->hilang ?? 0),
                'total_goyang' => (int) ($raw_data->goyang ?? 0),
                'total_dengan_masalah' => (int) ($raw_data->ada_masalah ?? 0),
                'total_gigi_sehat' => (int) ($raw_data->gigi_sehat ?? 0),
                'total_skrining' => (int) ($raw_data->total_skrining ?? 0)
            ]
        ];
    }

    /**
     * Get BMI distribution analysis from skrining_pkg table
     * BMI = berat_badan (kg) / (tinggi_badan (cm) / 100)^2
     */
    private function getDistribusiBMI($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                // BMI categories based on WHO standards
                // Underweight: BMI < 18.5
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) < 18.5 THEN 1 ELSE 0 END) as underweight'),
                // Normal weight: BMI 18.5-24.9
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 18.5 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) <= 24.9 THEN 1 ELSE 0 END) as normal_weight'),
                // Overweight: BMI 25-29.9
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 25 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) <= 29.9 THEN 1 ELSE 0 END) as overweight'),
                // Obese Class I: BMI 30-34.9
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 30 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) <= 34.9 THEN 1 ELSE 0 END) as obese_1'),
                // Obese Class II: BMI 35-39.9
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 35 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) <= 39.9 THEN 1 ELSE 0 END) as obese_2'),
                // Obese Class III: BMI >= 40
                DB::raw('SUM(CASE WHEN sp.berat_badan > 0 AND sp.tinggi_badan > 0 AND (sp.berat_badan / POWER(sp.tinggi_badan/100, 2)) >= 40 THEN 1 ELSE 0 END) as obese_3'),
                // Invalid/missing data
                DB::raw('SUM(CASE WHEN sp.berat_badan <= 0 OR sp.tinggi_badan <= 0 OR sp.berat_badan IS NULL OR sp.tinggi_badan IS NULL THEN 1 ELSE 0 END) as data_tidak_valid'),
                // Total count for validation
                DB::raw('COUNT(sp.id_pkg) as total_skrining')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $raw_data = $query->first();
        
        // Process data into chart-friendly format with integer casting (following memory lessons)
        return [
            'distribusi_kategori' => [
                ['kategori' => 'Kurus (< 18.5)', 'jumlah' => (int) ($raw_data->underweight ?? 0), 'color' => '#17a2b8'], // info blue
                ['kategori' => 'Normal (18.5-24.9)', 'jumlah' => (int) ($raw_data->normal_weight ?? 0), 'color' => '#28a745'], // success green
                ['kategori' => 'Kelebihan BB (25-29.9)', 'jumlah' => (int) ($raw_data->overweight ?? 0), 'color' => '#ffc107'], // warning yellow
                ['kategori' => 'Obesitas I (30-34.9)', 'jumlah' => (int) ($raw_data->obese_1 ?? 0), 'color' => '#fd7e14'], // orange
                ['kategori' => 'Obesitas II (35-39.9)', 'jumlah' => (int) ($raw_data->obese_2 ?? 0), 'color' => '#dc3545'], // danger red
                ['kategori' => 'Obesitas III (≥ 40)', 'jumlah' => (int) ($raw_data->obese_3 ?? 0), 'color' => '#6f42c1'] // purple
            ],
            'summary' => [
                'total_valid' => (int) ($raw_data->total_skrining ?? 0) - (int) ($raw_data->data_tidak_valid ?? 0),
                'total_underweight' => (int) ($raw_data->underweight ?? 0),
                'total_normal' => (int) ($raw_data->normal_weight ?? 0),
                'total_overweight_obese' => (int) ($raw_data->overweight ?? 0) + (int) ($raw_data->obese_1 ?? 0) + (int) ($raw_data->obese_2 ?? 0) + (int) ($raw_data->obese_3 ?? 0),
                'total_skrining' => (int) ($raw_data->total_skrining ?? 0),
                'data_tidak_valid' => (int) ($raw_data->data_tidak_valid ?? 0)
            ]
        ];
    }

    /**
     * Get smoking analysis data from skrining_pkg table
     */
    private function getAnalisaRokok($posyandu = null, $desa = null, $tanggal_awal = null, $tanggal_akhir = null)
    {
        $query = DB::table('skrining_pkg as sp')
            ->join('data_posyandu as p', 'sp.kode_posyandu', '=', 'p.kode_posyandu')
            ->select(
                // Status merokok distribution
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" THEN 1 ELSE 0 END) as perokok_aktif'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Tidak" THEN 1 ELSE 0 END) as tidak_merokok'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Mantan Perokok" THEN 1 ELSE 0 END) as mantan_perokok'),
                
                // Paparan asap rokok
                DB::raw('SUM(CASE WHEN sp.paparan_asap = "Ya" THEN 1 ELSE 0 END) as terpapar_asap'),
                DB::raw('SUM(CASE WHEN sp.paparan_asap = "Tidak" THEN 1 ELSE 0 END) as tidak_terpapar_asap'),
                
                // Durasi merokok (untuk perokok aktif)
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.lama_merokok < 5 THEN 1 ELSE 0 END) as merokok_kurang_5tahun'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.lama_merokok >= 5 AND sp.lama_merokok < 10 THEN 1 ELSE 0 END) as merokok_5_10tahun'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.lama_merokok >= 10 AND sp.lama_merokok < 20 THEN 1 ELSE 0 END) as merokok_10_20tahun'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.lama_merokok >= 20 THEN 1 ELSE 0 END) as merokok_lebih_20tahun'),
                
                // Jumlah rokok per hari (untuk perokok aktif)
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.jumlah_rokok < 10 THEN 1 ELSE 0 END) as rokok_kurang_10batang'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.jumlah_rokok >= 10 AND sp.jumlah_rokok < 20 THEN 1 ELSE 0 END) as rokok_10_20batang'),
                DB::raw('SUM(CASE WHEN sp.status_merokok = "Ya" AND sp.jumlah_rokok >= 20 THEN 1 ELSE 0 END) as rokok_lebih_20batang'),
                
                // Total count for validation
                DB::raw('COUNT(sp.id_pkg) as total_skrining')
            );
            
        if ($posyandu && $posyandu != 'semua') {
            $query->where('p.nama_posyandu', $posyandu);
        }
        
        if ($desa && $desa != 'semua') {
            $query->where('p.desa', $desa);
        }
        
        if ($tanggal_awal) {
            $query->whereDate('sp.tanggal_skrining', '>=', $tanggal_awal);
        }
        
        if ($tanggal_akhir) {
            $query->whereDate('sp.tanggal_skrining', '<=', $tanggal_akhir);
        }
        
        $raw_data = $query->first();
        
        // Process data into chart-friendly format with integer casting (following memory lessons)
        return [
            'status_merokok' => [
                ['kategori' => 'Perokok Aktif', 'jumlah' => (int) ($raw_data->perokok_aktif ?? 0)],
                ['kategori' => 'Tidak Merokok', 'jumlah' => (int) ($raw_data->tidak_merokok ?? 0)],
                ['kategori' => 'Mantan Perokok', 'jumlah' => (int) ($raw_data->mantan_perokok ?? 0)]
            ],
            'paparan_asap' => [
                ['kategori' => 'Terpapar Asap', 'jumlah' => (int) ($raw_data->terpapar_asap ?? 0)],
                ['kategori' => 'Tidak Terpapar', 'jumlah' => (int) ($raw_data->tidak_terpapar_asap ?? 0)]
            ],
            'durasi_merokok' => [
                ['kategori' => '< 5 Tahun', 'jumlah' => (int) ($raw_data->merokok_kurang_5tahun ?? 0)],
                ['kategori' => '5-10 Tahun', 'jumlah' => (int) ($raw_data->merokok_5_10tahun ?? 0)],
                ['kategori' => '10-20 Tahun', 'jumlah' => (int) ($raw_data->merokok_10_20tahun ?? 0)],
                ['kategori' => '≥ 20 Tahun', 'jumlah' => (int) ($raw_data->merokok_lebih_20tahun ?? 0)]
            ],
            'konsumsi_harian' => [
                ['kategori' => '< 10 Batang/Hari', 'jumlah' => (int) ($raw_data->rokok_kurang_10batang ?? 0)],
                ['kategori' => '10-20 Batang/Hari', 'jumlah' => (int) ($raw_data->rokok_10_20batang ?? 0)],
                ['kategori' => '≥ 20 Batang/Hari', 'jumlah' => (int) ($raw_data->rokok_lebih_20batang ?? 0)]
            ],
            'summary' => [
                'total_perokok_aktif' => (int) ($raw_data->perokok_aktif ?? 0),
                'total_terpapar_asap' => (int) ($raw_data->terpapar_asap ?? 0),
                'total_skrining' => (int) ($raw_data->total_skrining ?? 0)
            ]
        ];
    }

    /**
     * Method untuk clear cache dan memastikan query terbaru digunakan
     */
    public function clearDashboardCache()
    {
        try {
            // Clear application cache
            Artisan::call('cache:clear');
            
            // Clear config cache
            Artisan::call('config:clear');
            
            // Clear route cache
            Artisan::call('route:clear');
            
            // Clear view cache
            Artisan::call('view:clear');
            
            return response()->json([
                'success' => true,
                'message' => 'Cache berhasil dibersihkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ]);
        }
    }

}