<?php

namespace App\Http\Controllers\Ralan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class PasienRalanController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        $tanggal = $request->get('tanggal') ?? date('Y-m-d');
        $sortOption = $request->get('sort', 'no_reg_asc');
        $heads = ['No. Reg', 'Nama Pasien', 'No Rawat', 'Telp', 'Dokter', 'Status'];
        $headsInternal = ['No. Reg', 'No. RM', 'Nama Pasien', 'Dokter', 'Status'];
        
        // Debug session dan parameter
        \Log::debug('Session index', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'sortOption' => $sortOption,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Hapus cache lama untuk mendapatkan data terbaru
        $this->clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal);
        
        // Ambil data pasien ralan langsung dari database tanpa cache
        $data = $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        
        // Ambil data rujukan internal
        $dataInternal = $this->getRujukInternal($tanggal);

        // Ambil mapping dokter PCare
        $dokterPcare = $this->getDokterPcare($kd_dokter);

        // Jika request AJAX, kembalikan hanya data dalam format JSON
        if ($request->ajax()) {
            // Hitung statistik dari data yang sama
            $totalPasien = $data->count();
            $selesai = $data->where('stts', 'Sudah')->count();
            $menunggu = $data->where('stts', 'Belum')->count();
            
            return response()->json([
                'pasienRalan' => $data,
                'rujukInternal' => $dataInternal,
                'statistik' => [
                    'total' => $totalPasien,
                    'selesai' => $selesai,
                    'menunggu' => $menunggu,
                    'persentaseSelesai' => $totalPasien > 0 ? round(($selesai / $totalPasien) * 100) : 0
                ],
                'timestamp' => now()->format('Y-m-d H:i:s'),
                'tanggal' => $tanggal,
                'poli' => $this->getPoliklinik($kd_poli),
                'success' => true
            ]);
        }

        // Log informasi data
        \Log::debug('Total data pasien pada index: ' . $data->count());

        // Hitung statistik dari $data yang sama untuk tampilan
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();

        \Log::debug('Statistik dari view: Total=' . $totalPasien . ', Selesai=' . $selesai . ', Menunggu=' . $menunggu);

        return view('ralan.pasien-ralan', [
            'nm_poli' => $this->getPoliklinik($kd_poli),
            'heads' => $heads,
            'data' => $data,
            'tanggal' => $tanggal,
            'headsInternal' => $headsInternal,
            'dataInternal' => $dataInternal,
            'dokter' => $dokterPcare ? $dokterPcare->kd_dokter : $kd_dokter,
            'totalPasien' => $totalPasien,
            'selesai' => $selesai,
            'menunggu' => $menunggu
        ]);
    }
    
    /**
     * Fungsi untuk mendapatkan data pasien rawat jalan
     * Digunakan oleh index() dan getDataForRefresh()
     * 
     * @param string $kd_poli
     * @param string $kd_dokter
     * @param string $tanggal
     * @param bool $useCache
     * @param string $sortOption
     * @return \Illuminate\Support\Collection
     */
    private function getPasienRalanData($kd_poli, $kd_dokter, $tanggal, $useCache = true, $sortOption = 'no_reg_asc')
    {
        // Buat key cache yang unik dengan timestamp untuk menghindari konflik
        $timestamp = now()->format('YmdHis');
        $cacheKey = "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_{$sortOption}";
        
        // Hapus semua cache terkait pasien untuk poli dan dokter ini
        $this->clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal);
        
        // Jika perlu selalu data terbaru, langsung query DB tanpa cache
        if (!$useCache) {
            \Log::debug('Force query tanpa cache');
            return $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        }
        
        // Cache hanya selama 15 detik untuk memastikan data selalu fresh
        \Log::debug('Menggunakan cache dengan key: ' . $cacheKey);
        return Cache::remember($cacheKey, 15, function() use ($kd_poli, $kd_dokter, $tanggal, $sortOption) {
            \Log::debug('Cache miss, melakukan query database');
            return $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        });
    }
    
    /**
     * Hapus semua cache yang terkait dengan pasien ralan untuk kombinasi poli, dokter, dan tanggal
     *
     * @param string $kd_poli
     * @param string $kd_dokter
     * @param string $tanggal
     * @return void
     */
    private function clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal)
    {
        $cachePattern = "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}";
        Cache::forget($cachePattern);
        
        // Hapus cache registrasi terakhir
        Cache::forget("last_registration_{$kd_poli}_{$kd_dokter}_{$tanggal}");
        
        \Log::debug('Menghapus cache dengan pattern: ' . $cachePattern);
    }
    
    /**
     * Query murni untuk mendapatkan data pasien rawat jalan
     * 
     * @param string $kd_poli
     * @param string $kd_dokter
     * @param string $tanggal
     * @param string $sortOption
     * @return \Illuminate\Support\Collection
     */
    private function queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption = 'no_reg_asc')
    {
        // Cek parameter untuk debugging
        \Log::debug('Parameter Query PasienRalan', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'sortOption' => $sortOption,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Hitung jumlah di database untuk log
        $count = DB::table('reg_periksa')
            ->where('reg_periksa.kd_poli', $kd_poli)
            ->where('tgl_registrasi', $tanggal)
            ->where('reg_periksa.kd_dokter', $kd_dokter)
            ->count();
            
        \Log::debug('Total record di reg_periksa: ' . $count);
        
        // Query detail data pasien - Pastikan konsisten dengan hitung jumlah di atas
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->leftJoin('resume_pasien', 'reg_periksa.no_rawat', '=', 'resume_pasien.no_rawat')
            ->where('reg_periksa.kd_poli', $kd_poli)
            ->where('tgl_registrasi', $tanggal)
            ->where('reg_periksa.kd_dokter', $kd_dokter);
            
        // Log query SQL untuk debugging
        $sql = $query->toSql();
        $bindings = $query->getBindings();
        \Log::debug('SQL Query: ' . $sql . ' with bindings: ' . json_encode($bindings));
        
        // Terapkan pengurutan berdasarkan sortOption
        switch ($sortOption) {
            case 'no_reg_desc':
                $query->orderBy('reg_periksa.no_reg', 'desc');
                break;
            case 'nm_pasien_asc':
                $query->orderBy('pasien.nm_pasien', 'asc');
                break;
            case 'nm_pasien_desc':
                $query->orderBy('pasien.nm_pasien', 'desc');
                break;
            case 'stts_asc':
                $query->orderBy('reg_periksa.stts', 'asc')
                      ->orderBy('reg_periksa.no_reg', 'asc');
                break;
            case 'stts_desc':
                $query->orderBy('reg_periksa.stts', 'desc')
                      ->orderBy('reg_periksa.no_reg', 'asc');
                break;
            case 'no_reg_asc':
            default:
                $query->orderBy('reg_periksa.no_reg', 'asc');
                break;
        }
        
        // Execute query untuk mendapatkan data
        $data = $query->select(
                'reg_periksa.no_reg', 
                'pasien.nm_pasien', 
                'reg_periksa.no_rawat', 
                'pasien.no_tlp', 
                'dokter.nm_dokter', 
                'reg_periksa.stts', 
                'reg_periksa.keputusan', 
                'pasien.no_rkm_medis', 
                'resume_pasien.diagnosa_utama'
            )
            ->get();
            
        \Log::debug('Total data pasien setelah query: ' . $data->count());
        
        // Verifikasi bahwa jumlah data sesuai dengan count awal
        if ($count !== $data->count()) {
            \Log::warning('Inconsistency detected: count before query: ' . $count . ', data returned: ' . $data->count());
        }
        
        // Cek statistik untuk debugging
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();
        
        \Log::debug('Statistik pasien setelah query:', [
            'total' => $totalPasien,
            'selesai' => $selesai,
            'menunggu' => $menunggu
        ]);
        
        return $data;
    }

    /**
     * API endpoint untuk mengambil data terbaru (auto refresh)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDataForRefresh(Request $request)
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        $tanggal = $request->get('tanggal') ?? date('Y-m-d');
        
        // Selalu refresh dari database untuk memastikan data konsisten
        $forceRefresh = true;
        
        // Opsi pengurutan data
        $sortOption = $request->get('sort', 'no_reg_asc');
        
        // Debug session dan parameter
        \Log::debug('Session getDataForRefresh', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'forceRefresh' => $forceRefresh,
            'sortOption' => $sortOption,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Hapus semua cache terkait untuk memastikan data fresh
        $this->clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal);
        
        // Query langsung ke database untuk mendapatkan data terbaru
        $data = $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        
        // Dapatkan data rujukan internal
        $dataInternal = $this->getRujukInternal($tanggal);
        
        // Hitung statistik dari $data yang sama
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();
        
        // Debug total data untuk memastikan konsistensi
        \Log::debug('Total data setelah getDataForRefresh', [
            'total' => $totalPasien,
            'selesai' => $selesai,
            'menunggu' => $menunggu,
            'data_count' => $data->count()
        ]);
        
        return response()->json([
            'pasienRalan' => $data,
            'rujukInternal' => $dataInternal,
            'statistik' => [
                'total' => $totalPasien,
                'selesai' => $selesai,
                'menunggu' => $menunggu,
                'persentaseSelesai' => $totalPasien > 0 ? round(($selesai / $totalPasien) * 100) : 0
            ],
            'timestamp' => now()->format('Y-m-d H:i:s'),
            'tanggal' => $tanggal,
            'poli' => $this->getPoliklinik($kd_poli),
            'lastUpdated' => now()->timestamp,
            'success' => true,
            'refreshForced' => $forceRefresh,
            'dataCount' => $data->count() // Tambahkan info tambahan untuk debugging
        ]);
    }
    
    /**
     * Fungsi untuk mendengarkan event pasien-saved dan memberikan respons
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function listenForNewPatients(Request $request)
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        $tanggal = $request->get('tanggal') ?? date('Y-m-d');
        $sortOption = $request->get('sort', 'no_reg_asc');
        
        // Debug session dan parameter
        \Log::debug('Session listenForNewPatients', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'sortOption' => $sortOption,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Validasi request
        if (!$kd_poli || !$kd_dokter) {
            return response()->json([
                'hasNewData' => false,
                'currentCount' => 0,
                'success' => false,
                'message' => 'Data sesi dokter atau poli tidak tersedia',
                'timestamp' => now()->format('Y-m-d H:i:s')
            ]);
        }
        
        // Mendapatkan jumlah data sebelum event
        $previousCount = (int) $request->get('currentCount', 0);
        
        // Selalu hapus cache untuk memastikan melihat data terbaru
        $this->clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal);
        
        // Query langsung untuk mendapatkan data dari database
        $data = $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        
        // Dapatkan count dari hasil query langsung
        $currentCount = $data->count();
            
        \Log::debug("Perbandingan data count: previous={$previousCount}, current={$currentCount}");
        
        // Membandingkan untuk melihat apakah ada data baru
        $hasNewData = $currentCount > $previousCount;
        
        // Jika ada data baru, perbarui waktu pembaruan terakhir
        if ($hasNewData) {
            // Simpan waktu terakhir pendaftaran di cache untuk polling
            Cache::put("last_registration_{$kd_poli}_{$kd_dokter}_{$tanggal}", now()->timestamp, 3600);
            
            // Log informasi penambahan data
            \Log::info('Data pasien baru terdeteksi', [
                'poli' => $kd_poli,
                'dokter' => $kd_dokter,
                'tanggal' => $tanggal,
                'sebelumnya' => $previousCount,
                'sekarang' => $currentCount,
                'selisih' => ($currentCount - $previousCount)
            ]);
        }
        
        return response()->json([
            'hasNewData' => $hasNewData,
            'currentCount' => $currentCount,
            'previousCount' => $previousCount,
            'success' => true,
            'lastUpdated' => now()->timestamp,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
    }

    private function getPoliklinik($kd_poli)
    {
        $poli = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poli->nm_poli;
    }

    private function getRujukInternal($tanggal)
    {
        return DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('rujukan_internal_poli', 'reg_periksa.no_rawat', '=', 'rujukan_internal_poli.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'rujukan_internal_poli.kd_dokter')
            ->where('rujukan_internal_poli.kd_poli', session()->get('kd_poli'))
            ->where('reg_periksa.tgl_registrasi', $tanggal)
            ->orderBy('reg_periksa.no_reg', 'asc')
            ->select('reg_periksa.no_reg', 'reg_periksa.no_rkm_medis', 'reg_periksa.no_rawat', 'pasien.nm_pasien', 'dokter.nm_dokter', 'reg_periksa.stts')
            ->get();
    }

    private function getDokterPcare($kd_dokter)
    {
        // Coba cari di mapping dokter PCare
        $dokterPcare = DB::table('maping_dokter_pcare')
            ->where('kd_dokter', $kd_dokter)
            ->first();

        if (!$dokterPcare) {
            // Jika tidak ditemukan, coba cari di tabel dokter untuk mendapatkan informasi tambahan
            $dokter = DB::table('dokter')
                ->where('kd_dokter', $kd_dokter)
                ->first();

            if ($dokter) {
                // Log informasi dokter yang belum memiliki mapping
                \Log::warning('Dokter belum memiliki mapping PCare', [
                    'kd_dokter' => $kd_dokter,
                    'nama_dokter' => $dokter->nm_dokter
                ]);
            }
        }

        return $dokterPcare;
    }

    public static function encryptData($data)
    {
        $data = Crypt::encrypt($data);
        return $data;
    }
}
