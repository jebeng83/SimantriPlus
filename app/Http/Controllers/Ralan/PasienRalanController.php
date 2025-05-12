<?php

namespace App\Http\Controllers\Ralan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use App\Models\Pasien;
use App\Events\PasienDipanggil;
use App\Events\AntrianDipanggil;
use GuzzleHttp\Client;
use App\Traits\BpjsTraits;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PasienRalanController extends Controller
{
    use BpjsTraits;

    // Flag untuk mengaktifkan/menonaktifkan log debug yang tidak kritis
    // Atur ke false untuk mengurangi jumlah log debug
    private $DEBUG = false;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
        $this->middleware('web');
        // Gunakan nilai dari .env jika ada
        $this->DEBUG = env('DEBUG_PATIENT_CONTROLLER', false);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        // Cek dan log status session
        $sessionOk = $this->checkSessionStatus();
        Log::info('PasienRalanController index called - session status: ' . ($sessionOk ? 'OK' : 'Invalid'));
        
        // Jika session belum terisi poliklinik, gunakan default
        if (!session()->has('kd_poli')) {
            // Default ke poli umum jika belum ada yang dipilih
            session()->put('kd_poli', 'UMUM');
            Log::info('Setting default kd_poli to UMUM');
        }

        $kdPoli = session('kd_poli');
        $kdDokter = session('username');
        
        $tanggal = $request->get('tanggal') ?? date('Y-m-d');
        $sortOption = $request->get('sort', 'no_reg_asc');
        $heads = ['No. Reg', 'Nama Pasien', 'No Rawat', 'Telp', 'Dokter', 'Status'];
        $headsInternal = ['No. Reg', 'No. RM', 'Nama Pasien', 'Dokter', 'Status'];
        
        // Ambil nama dokter
        $dokter = DB::table('dokter')->where('kd_dokter', $kdDokter)->first();
        $nmDokter = $dokter ? $dokter->nm_dokter : 'Dokter';
        
        // Ambil nama poliklinik
        $poliklinik = DB::table('poliklinik')->where('kd_poli', $kdPoli)->first();
        $nmPoli = $poliklinik ? $poliklinik->nm_poli : 'Poliklinik';
        
        Log::info('PasienRalanController preparing view with params: ', [
            'kd_poli' => $kdPoli,
            'kd_dokter' => $kdDokter,
            'tanggal' => $tanggal
        ]);
        
        // Hapus cache lama untuk mendapatkan data terbaru
        $this->clearAllRelatedCaches($kdPoli, $kdDokter, $tanggal);
        
        // Ambil data pasien ralan langsung dari database tanpa cache
        $data = $this->queryPasienRalanData($kdPoli, $kdDokter, $tanggal, $sortOption);
        
        // Ambil data rujukan internal
        $dataInternal = $this->getRujukInternal($tanggal);

        // Ambil mapping dokter PCare
        $dokterPcare = $this->getDokterPcare($kdDokter);

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
                'poli' => $this->getPoliklinik($kdPoli),
                'success' => true
            ]);
        }

        // Log informasi data
        Log::debug('Total data pasien pada index: ' . $data->count());

        // Hitung statistik dari $data yang sama untuk tampilan
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();

        Log::debug('Statistik dari view: Total=' . $totalPasien . ', Selesai=' . $selesai . ', Menunggu=' . $menunggu);

        return view('ralan.pasien-ralan', [
            'nm_poli' => $this->getPoliklinik($kdPoli),
            'heads' => $heads,
            'data' => $data,
            'tanggal' => $tanggal,
            'headsInternal' => $headsInternal,
            'dataInternal' => $dataInternal,
            'dokter' => $dokterPcare ? $dokterPcare->kd_dokter : $kdDokter,
            'totalPasien' => $totalPasien,
            'selesai' => $selesai,
            'menunggu' => $menunggu
        ]);
    }
    
    /**
     * Metode logging yang hanya menjalankan log jika debug diaktifkan
     *
     * @param string $message
     * @param array $context
     * @return void
     */
    private function debugLog($message, array $context = [])
    {
        if ($this->DEBUG) {
            Log::debug($message, $context);
        }
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
            $this->debugLog('Force query tanpa cache');
            return $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        }
        
        // Cache hanya selama 15 detik untuk memastikan data selalu fresh
        $this->debugLog('Menggunakan cache dengan key: ' . $cacheKey);
        return Cache::remember($cacheKey, 15, function() use ($kd_poli, $kd_dokter, $tanggal, $sortOption) {
            $this->debugLog('Cache miss, melakukan query database');
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
        
        $this->debugLog('Menghapus cache dengan pattern: ' . $cachePattern);
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
        $this->debugLog('Parameter Query PasienRalan', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'sortOption' => $sortOption,
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Hitung jumlah di database untuk log
        $count = DB::table('reg_periksa');
        
        // Pengecualian untuk admin dan poli khusus
        $isAdminWithAllPoli = ($kd_dokter === 'admin' && $kd_poli === 'U0011');
        
        // Jika di lingkungan production, jangan filter berdasarkan dokter untuk memastikan semua data tampil
        $isProduction = app()->environment('production');
        
        if ($isAdminWithAllPoli) {
            // Admin dengan poli khusus tidak perlu filter tambahan
            Log::info('Admin dengan poli khusus: menampilkan semua data');
        } elseif (!$isAdminWithAllPoli && !$isProduction) {
            $count = $count->where('reg_periksa.kd_poli', $kd_poli)
                            ->where('reg_periksa.kd_dokter', $kd_dokter);
        } elseif (!$isAdminWithAllPoli && $isProduction) {
            // Di production hanya filter berdasarkan poli
            $count = $count->where('reg_periksa.kd_poli', $kd_poli);
            Log::info('Mode production: hanya filter berdasarkan poli: ' . $kd_poli);
        }
        
        $count = $count->where('tgl_registrasi', $tanggal)
                      ->count();
            
        $this->debugLog('Total record di reg_periksa: ' . $count);
        
        // Query detail data pasien - HINDARI JOIN YANG TIDAK PERLU untuk performa
        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
            ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
            ->leftJoin('resume_pasien', 'reg_periksa.no_rawat', '=', 'resume_pasien.no_rawat')
            ->where('tgl_registrasi', $tanggal);
        
        // Jika bukan admin dengan semua poli, terapkan filter poli dan dokter
        // Jika di lingkungan production, jangan filter berdasarkan dokter untuk memastikan semua data tampil
        $isProduction = app()->environment('production');
        
        if ($isAdminWithAllPoli) {
            // Admin dengan poli khusus tidak perlu filter tambahan
            Log::info('Admin dengan poli khusus: menampilkan semua data pada query utama');
        } elseif (!$isAdminWithAllPoli && !$isProduction) {
            $query = $query->where('reg_periksa.kd_poli', $kd_poli)
                          ->where('reg_periksa.kd_dokter', $kd_dokter);
        } elseif (!$isAdminWithAllPoli && $isProduction) {
            // Di production hanya filter berdasarkan poli
            $query = $query->where('reg_periksa.kd_poli', $kd_poli);
            Log::info('Mode production query: hanya filter berdasarkan poli: ' . $kd_poli);
        }
        
        // Log query SQL untuk debugging - hanya jika debug sangat detail diperlukan
        if ($this->DEBUG) {
            $sql = $query->toSql();
            $bindings = $query->getBindings();
            $this->debugLog('SQL Query: ' . $sql . ' with bindings: ' . json_encode($bindings));
        }
        
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
        
        // Execute query untuk mendapatkan data dengan select yang jelas
        $data = $query->select(
                'reg_periksa.no_reg', 
                'pasien.nm_pasien', 
                'reg_periksa.no_rawat', 
                'pasien.no_tlp', 
                'dokter.nm_dokter', 
                'reg_periksa.stts', 
                'reg_periksa.keputusan', 
                'pasien.no_rkm_medis', 
                'resume_pasien.diagnosa_utama',
                'poliklinik.nm_poli'
            )
            ->get();
            
        $this->debugLog('Total data pasien setelah query: ' . $data->count());
        
        // Verifikasi bahwa jumlah data sesuai dengan count awal
        if ($count !== $data->count()) {
            $this->debugLog('Inkonsistensi terdeteksi: count before query: ' . $count . ', data returned: ' . $data->count());
            
            // Jika ada inkonsistensi, coba dengan query sederhana langsung tanpa join
            $simplifiedQuery = DB::table('reg_periksa')
                ->where('tgl_registrasi', $tanggal);
                
            // Terapkan filter yang sama dengan query utama
            if ($isAdminWithAllPoli) {
                // Admin dengan poli khusus tidak perlu filter tambahan
                Log::info('Admin dengan poli khusus: menampilkan semua data pada query recovery');
            } elseif (!$isAdminWithAllPoli && !$isProduction) {
                $simplifiedQuery = $simplifiedQuery->where('reg_periksa.kd_poli', $kd_poli)
                                  ->where('reg_periksa.kd_dokter', $kd_dokter);
            } elseif (!$isAdminWithAllPoli && $isProduction) {
                // Di production hanya filter berdasarkan poli
                $simplifiedQuery = $simplifiedQuery->where('reg_periksa.kd_poli', $kd_poli);
            }
            
            $simplifiedData = $simplifiedQuery->select('no_reg', 'no_rawat', 'no_rkm_medis', 'stts')
                ->get();
                
            $this->debugLog('Verifikasi dengan query sederhana: ' . $simplifiedData->count() . ' records');
            
            // Coba hubungkan kembali dengan data pasien dan dokter jika ada perbedaan
            if ($simplifiedData->count() > $data->count()) {
                $this->debugLog('Melakukan recovery data, ditemukan ' . $simplifiedData->count() . ' records di query sederhana');
                
                // Rekonstruksi data dengan cara lain (per satu record)
                $reconstructedData = collect();
                
                foreach ($simplifiedData as $simpleRecord) {
                    $pasien = DB::table('pasien')
                        ->where('no_rkm_medis', $simpleRecord->no_rkm_medis)
                        ->first();
                        
                    $dokter = DB::table('dokter')
                        ->where('kd_dokter', $kd_dokter)
                        ->first();
                        
                    $resumePasien = DB::table('resume_pasien')
                        ->where('no_rawat', $simpleRecord->no_rawat)
                        ->first();
                        
                    // Rekonstruksi objek data
                    $recordObj = (object)[
                        'no_reg' => $simpleRecord->no_reg,
                        'nm_pasien' => $pasien ? $pasien->nm_pasien : 'Unknown',
                        'no_rawat' => $simpleRecord->no_rawat,
                        'no_tlp' => $pasien ? $pasien->no_tlp : null,
                        'nm_dokter' => $dokter ? $dokter->nm_dokter : 'Unknown',
                        'stts' => $simpleRecord->stts,
                        'keputusan' => null,
                        'no_rkm_medis' => $simpleRecord->no_rkm_medis,
                        'diagnosa_utama' => $resumePasien ? $resumePasien->diagnosa_utama : null
                    ];
                    
                    $reconstructedData->push($recordObj);
                }
                
                if ($reconstructedData->count() > $data->count()) {
                    $this->debugLog('Menggunakan data hasil rekontruksi: ' . $reconstructedData->count() . ' records');
                    $data = $reconstructedData;
                }
            }
        }
        
        // Cek statistik untuk debugging
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();
        
        $this->debugLog('Statistik pasien setelah query:', [
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
        $tanggal = $request->input('tanggal', date('Y-m-d'));
        $forceRefresh = $request->input('forceRefresh', false);
        $lastCount = $request->input('lastCount', 0);
        $sortOption = $request->input('sortOption', 'no_reg_asc');
        
        $this->debugLog('Session getDataForRefresh', [
            'kd_poli' => $kd_poli,
            'kd_dokter' => $kd_dokter,
            'tanggal' => $tanggal,
            'forceRefresh' => $forceRefresh,
            'lastCount' => $lastCount,
            'sortOption' => $sortOption,
            'requestTime' => $request->input('requestTime', ''),
            'timestamp' => now()->format('Y-m-d H:i:s')
        ]);
        
        // Hapus semua cache terkait untuk memastikan data fresh
        $this->clearAllRelatedCaches($kd_poli, $kd_dokter, $tanggal);
        
        // Hapus semua cache lainnya yang mungkin terkait
        $cacheKeys = [
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_no_reg_asc",
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_no_reg_desc",
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_nm_pasien_asc",
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_nm_pasien_desc",
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_stts_asc",
            "pasien_ralan_{$kd_poli}_{$kd_dokter}_{$tanggal}_stts_desc",
        ];
        
        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }
        
        // Query langsung ke database untuk mendapatkan data terbaru - SELALU BYPASS CACHE
        $data = $this->queryPasienRalanData($kd_poli, $kd_dokter, $tanggal, $sortOption);
        
        // Dapatkan data rujukan internal
        $dataInternal = $this->getRujukInternal($tanggal);
        
        // Hitung statistik dari $data yang sama
        $totalPasien = $data->count();
        $selesai = $data->where('stts', 'Sudah')->count();
        $menunggu = $data->where('stts', 'Belum')->count();
        
        // Jika jumlah data sama dengan lastCount dan ini bukan forceRefresh,
        // kita bisa mengembalikan respons yang lebih ringan
        $returnFullData = $forceRefresh || $totalPasien != $lastCount;
        
        // Debug total data untuk memastikan konsistensi
        $this->debugLog('Total data setelah getDataForRefresh', [
            'total' => $totalPasien,
            'selesai' => $selesai,
            'menunggu' => $menunggu,
            'data_count' => $data->count(),
            'lastCount' => $lastCount,
            'returnFullData' => $returnFullData
        ]);
        
        // Respons dasar yang selalu dikembalikan
        $response = [
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
            'dataCount' => $data->count(),
            'dataSource' => 'direct_query',
            'requestTime' => $request->input('requestTime', ''),
            'responseTime' => now()->timestamp,
            'returnFullData' => $returnFullData
        ];
        
        // Tambahkan data lengkap jika diperlukan
        if ($returnFullData) {
            $response['pasienRalan'] = $data;
            $response['rujukInternal'] = $dataInternal;
        }
        
        return response()->json($response);
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
        
        $this->debugLog('Session listenForNewPatients', [
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
            
        $this->debugLog("Perbandingan data count: previous={$previousCount}, current={$currentCount}");
        
        // Membandingkan untuk melihat apakah ada data baru
        $hasNewData = $currentCount > $previousCount;
        
        // Jika ada data baru, perbarui waktu pembaruan terakhir
        if ($hasNewData) {
            // Simpan waktu terakhir pendaftaran di cache untuk polling
            Cache::put("last_registration_{$kd_poli}_{$kd_dokter}_{$tanggal}", now()->timestamp, 3600);
            
            // Log informasi penambahan data
            $this->debugLog('Data pasien baru terdeteksi', [
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

    /**
     * Fungsi untuk mendapatkan nama poliklinik dari kode poli
     *
     * @param string $kd_poli
     * @return string
     */
    private function getPoliklinik($kd_poli)
    {
        $poliklinik = DB::table('poliklinik')->where('kd_poli', $kd_poli)->first();
        return $poliklinik ? $poliklinik->nm_poli : 'Poliklinik';
    }

    /**
     * Ambil data rujukan internal
     *
     * @param string $tanggal
     * @return \Illuminate\Support\Collection
     */
    private function getRujukInternal($tanggal)
    {
        try {
            $data = DB::table('rujukan_internal_poli')
                ->join('reg_periksa', 'rujukan_internal_poli.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('dokter', 'dokter.kd_dokter', '=', 'reg_periksa.kd_dokter')
                ->join('poliklinik', 'poliklinik.kd_poli', '=', 'reg_periksa.kd_poli')
                ->where('reg_periksa.tgl_registrasi', $tanggal)
                ->select(
                    'reg_periksa.no_reg',
                    'pasien.no_rkm_medis',
                    'pasien.nm_pasien',
                    'dokter.nm_dokter',
                    'rujukan_internal_poli.kd_poli',
                    'poliklinik.nm_poli'
                )
                ->get();
            
            return $data;
        } catch (\Exception $e) {
            Log::error('Error getting rujuk internal data: ' . $e->getMessage());
            return collect(); // Return empty collection on error
        }
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
                Log::warning('Dokter belum memiliki mapping PCare', [
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

    /**
     * Periksa status sesi
     */
    private function checkSessionStatus()
    {
        $sessionData = [
            'id' => session()->getId(),
            'username' => session()->get('username', 'tidak ada'),
            'logged_in' => session()->get('logged_in', false),
            'kd_poli' => session()->get('kd_poli', 'tidak ada')
        ];
        
        // Logging session status masih diperlukan untuk debugging login issues
        Log::debug('Session status check: ', $sessionData);
        
        return $sessionData;
    }

    /**
     * Menangani panggilan pasien
     */
    public function panggilPasien(Request $request)
    {
        try {
            Log::info('Received panggil-pasien request', [
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'session_id' => session()->getId(),
                'data' => $request->all()
            ]);
            
            DB::beginTransaction();

            $no_rawat = $request->no_rawat;
            $is_ulang = $request->is_ulang === 'true';

            // Ambil data pasien
            $pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
                ->leftJoin('maping_poliklinik_pcare', 'poliklinik.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
                ->where('reg_periksa.no_rawat', $no_rawat)
                ->select(
                    'reg_periksa.no_reg',
                    'pasien.nm_pasien as nama',
                    'poliklinik.nm_poli as poli',
                    'reg_periksa.kd_dokter',
                    'reg_periksa.kd_poli',
                    'reg_periksa.tgl_registrasi',
                    'pasien.no_peserta',
                    'pasien.no_rkm_medis',
                    'maping_poliklinik_pcare.kd_poli_pcare'
                )
                ->first();

            if (!$pasien) {
                throw new \Exception('Data pasien tidak ditemukan');
            }

            // Update atau insert ke antripoli
            DB::table('antripoli')->updateOrInsert(
                ['no_rawat' => $no_rawat],
                [
                    'kd_dokter' => $pasien->kd_dokter,
                    'kd_poli' => $pasien->kd_poli,
                    'status' => '1',
                    'no_rawat' => $no_rawat
                ]
            );

            // Jika pasien memiliki nomor peserta BPJS, kirim data panggilan ke BPJS
            if (!empty($pasien->no_peserta)) {
                // Waktu saat ini dalam format timestamp millisecond
                $waktu = round(microtime(true) * 1000);
                
                // Data untuk BPJS
                $dataBpjs = [
                    'tanggalperiksa' => $pasien->tgl_registrasi,
                    'kodepoli' => $pasien->kd_poli_pcare ?? $pasien->kd_poli,
                    'nomorkartu' => $pasien->no_peserta,
                    'status' => 1, // 1 = Hadir, 2 = Tidak Hadir
                    'waktu' => $waktu
                ];
                
                Log::info('Mengirim data panggilan pasien ke BPJS', $dataBpjs);
                
                try {
                    // Gunakan BpjsTraits untuk mengirim data
                    $response = $this->requestPostBpjs('/antrean/panggil', $dataBpjs, 'mobilejkn');
                    
                    // Response dari requestPostBpjs adalah JsonResponse, ambil datanya
                    $responseData = $response instanceof \Illuminate\Http\JsonResponse ? 
                                   $response->getData(true) : json_decode($response, true);
                    
                    Log::info('Respons dari BPJS Mobile JKN', [
                        'response' => $responseData
                    ]);
                    
                    // Cek keberhasilan pengiriman
                    $isSuccess = isset($responseData['metadata']['code']) && 
                               $responseData['metadata']['code'] == 200;
                    
                    if (!$isSuccess) {
                        Log::warning('Gagal mengirim status antrean ke BPJS', [
                            'response' => $responseData
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Error saat mengirim data ke BPJS: ' . $e->getMessage(), [
                        'trace' => $e->getTraceAsString()
                    ]);
                    // Lanjutkan proses meskipun ada error dengan BPJS
                }
            } else {
                Log::info('Pasien tidak memiliki nomor BPJS, tidak mengirim data ke BPJS');
            }

            DB::commit();

            // Broadcast event
            event(new AntrianDipanggil([
                'no_reg' => $pasien->no_reg,
                'nama' => $pasien->nama,
                'poli' => $pasien->poli,
                'is_ulang' => $is_ulang
            ]));

            return response()->json([
                'success' => true,
                'message' => $is_ulang ? 'Pasien berhasil dipanggil ulang' : 'Pasien berhasil dipanggil'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error in panggilPasien: ' . $e->getMessage(), [
                'csrf_token' => $request->header('X-CSRF-TOKEN'),
                'session_id' => session()->getId(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal memanggil pasien: ' . $e->getMessage()
            ], 500);
        }
    }
}
