<?php

namespace App\Http\Controllers\Ranap;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class GrafikAnalisaController extends Controller
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
     * Show the Grafik Analisa dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('ranap.laporan.grafik');
    }

    /**
     * Show the Demographic Analysis page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showDemografi()
    {
        return view('ranap.laporan.demografi-pasien');
    }

    /**
     * Show the Top 10 Diseases Analysis page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function showTopPenyakit()
    {
        return view('ranap.laporan.top-penyakit');
    }

    /**
     * Get analytics data for charts
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAnalyticsData(Request $request)
    {
        $category = $request->get('category', 'all');
        
        // Sample data - replace with actual database queries
        $data = [
            'demographic' => [
                'labels' => ['Laki-laki', 'Perempuan'],
                'data' => [1456, 1391],
                'colors' => ['#1890ff', '#eb2f96']
            ],
            'trends' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'data' => [120, 145, 132, 167, 189, 201],
                'trend' => 'increasing'
            ],
            'disease' => [
                'labels' => ['Hipertensi', 'Diabetes', 'Influenza', 'Demam', 'Batuk'],
                'data' => [456, 342, 289, 234, 198],
                'colors' => ['#f5222d', '#fa8c16', '#faad14', '#52c41a', '#1890ff']
            ],
            'performance' => [
                'recovery_rate' => 95.2,
                'satisfaction' => 97.8,
                'efficiency' => 89.5,
                'quality_score' => 94.1
            ]
        ];

        if ($category !== 'all') {
            return response()->json($data[$category] ?? []);
        }

        return response()->json($data);
    }

    /**
     * Export analytics data
     *
     * @return \Illuminate\Http\Response
     */
    public function exportData(Request $request)
    {
        $format = $request->get('format', 'excel');
        $category = $request->get('category', 'all');
        
        // Implement export logic here
        // This is a placeholder response
        
        return response()->json([
            'message' => 'Export initiated',
            'format' => $format,
            'category' => $category,
            'download_url' => '/downloads/analytics_' . date('Y-m-d') . '.' . $format
        ]);
    }

    /**
     * Get real-time statistics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRealtimeStats()
    {
        // Sample real-time data - replace with actual queries
        $stats = [
            'total_graphs' => 24,
            'active_analysis' => 8,
            'completed_reports' => 156,
            'data_accuracy' => 97.8,
            'last_update' => now()->diffForHumans(),
            'system_status' => 'healthy'
        ];

        return response()->json($stats);
    }

    /**
     * Get demographic data for charts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDemograficData(Request $request)
    {
        try {
            $query = \DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel');

            // Apply filters
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('reg_periksa.tgl_registrasi', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->has('kd_kab') && $request->kd_kab) {
                $query->where('pasien.kd_kab', $request->kd_kab);
            }

            if ($request->has('kd_kec') && $request->kd_kec) {
                $query->where('pasien.kd_kec', $request->kd_kec);
            }

            if ($request->has('kd_kel') && $request->kd_kel) {
                $query->where('pasien.kd_kel', $request->kd_kel);
            }

            // Get summary data
            $summary = [
                'totalPatients' => (clone $query)->count(),
                'uniquePatients' => (clone $query)->distinct('reg_periksa.no_rkm_medis')->count('reg_periksa.no_rkm_medis'),
                'kabupatenCount' => (clone $query)->distinct('pasien.kd_kab')->whereNotNull('pasien.kd_kab')->count('pasien.kd_kab'),
                'kecamatanCount' => (clone $query)->distinct('pasien.kd_kec')->whereNotNull('pasien.kd_kec')->count('pasien.kd_kec'),
                'kelurahanCount' => (clone $query)->distinct('pasien.kd_kel')->whereNotNull('pasien.kd_kel')->count('pasien.kd_kel')
            ];

            // Get kabupaten distribution
            $kabupatenData = (clone $query)
                ->select('kabupaten.nm_kab', \DB::raw('COUNT(*) as total'))
                ->whereNotNull('kabupaten.nm_kab')
                ->groupBy('kabupaten.nm_kab', 'kabupaten.kd_kab')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            // Get kecamatan distribution
            $kecamatanData = (clone $query)
                ->select('kecamatan.nm_kec', \DB::raw('COUNT(*) as total'))
                ->whereNotNull('kecamatan.nm_kec')
                ->groupBy('kecamatan.nm_kec', 'kecamatan.kd_kec')
                ->orderBy('total', 'desc')
                ->limit(15)
                ->get();

            // Get kelurahan distribution
            $kelurahanData = (clone $query)
                ->select('kelurahan.nm_kel', \DB::raw('COUNT(*) as total'))
                ->whereNotNull('kelurahan.nm_kel')
                ->groupBy('kelurahan.nm_kel', 'kelurahan.kd_kel')
                ->orderBy('total', 'desc')
                ->limit(20)
                ->get();

            // Get gender distribution
            $genderData = (clone $query)
                ->select('pasien.jk', \DB::raw('COUNT(*) as total'))
                ->whereNotNull('pasien.jk')
                ->groupBy('pasien.jk')
                ->get();

            // Format chart data
            $charts = [
                'kabupaten' => [
                    'labels' => $kabupatenData->pluck('nm_kab')->toArray(),
                    'data' => $kabupatenData->pluck('total')->map(fn($val) => (int)$val)->toArray()
                ],
                'kecamatan' => [
                    'labels' => $kecamatanData->pluck('nm_kec')->toArray(),
                    'data' => $kecamatanData->pluck('total')->map(fn($val) => (int)$val)->toArray()
                ],
                'kelurahan' => [
                    'labels' => $kelurahanData->pluck('nm_kel')->toArray(),
                    'data' => $kelurahanData->pluck('total')->map(fn($val) => (int)$val)->toArray()
                ],
                'gender' => [
                    'labels' => $genderData->pluck('jk')->map(function($jk) {
                        return $jk === 'L' ? 'Laki-laki' : ($jk === 'P' ? 'Perempuan' : 'Tidak Diketahui');
                    })->toArray(),
                    'data' => $genderData->pluck('total')->map(fn($val) => (int)$val)->toArray()
                ]
            ];

            // Provide sample data if no real data exists
            if (empty($charts['kabupaten']['data']) && empty($charts['kecamatan']['data']) && 
                empty($charts['kelurahan']['data']) && empty($charts['gender']['data'])) {
                $charts = [
                    'kabupaten' => [
                        'labels' => ['Belum ada data'],
                        'data' => [1]
                    ],
                    'kecamatan' => [
                        'labels' => ['Belum ada data'],
                        'data' => [1]
                    ],
                    'kelurahan' => [
                        'labels' => ['Belum ada data'],
                        'data' => [1]
                    ],
                    'gender' => [
                        'labels' => ['Belum ada data'],
                        'data' => [1]
                    ]
                ];
            }

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'charts' => $charts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data demografis: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export demographic data to Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportDemograficData(Request $request)
    {
        try {
            $query = \DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'kabupaten.nm_kab',
                    'kecamatan.nm_kec',
                    'kelurahan.nm_kel',
                    'reg_periksa.tgl_registrasi'
                );

            // Apply same filters as chart data
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('reg_periksa.tgl_registrasi', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->has('kd_kab') && $request->kd_kab) {
                $query->where('pasien.kd_kab', $request->kd_kab);
            }

            if ($request->has('kd_kec') && $request->kd_kec) {
                $query->where('pasien.kd_kec', $request->kd_kec);
            }

            if ($request->has('kd_kel') && $request->kd_kel) {
                $query->where('pasien.kd_kel', $request->kd_kel);
            }

            $data = $query->orderBy('reg_periksa.tgl_registrasi', 'desc')->get();

            // For now, return JSON. In production, implement Excel export using Laravel Excel package
            return response()->json([
                'success' => true,
                'message' => 'Data siap untuk diekspor',
                'total_records' => $data->count(),
                'download_url' => '/temp/demografi-export-' . date('Y-m-d') . '.xlsx'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get kabupaten data from database for demographics
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKabupatenFromDb()
    {
        try {
            $kabupaten = \DB::table('kabupaten')
                ->select('kd_kab', 'nm_kab')
                ->whereExists(function($query) {
                    $query->select(\DB::raw(1))
                          ->from('pasien')
                          ->whereColumn('pasien.kd_kab', 'kabupaten.kd_kab');
                })
                ->orderBy('nm_kab')
                ->get();

            return response()->json($kabupaten);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kabupaten: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all kecamatan data from database for independent filtering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllKecamatanFromDb()
    {
        try {
            $kecamatan = \DB::table('kecamatan')
                ->select('kd_kec', 'nm_kec')
                ->whereExists(function($query) {
                    $query->select(\DB::raw(1))
                          ->from('pasien')
                          ->whereColumn('pasien.kd_kec', 'kecamatan.kd_kec');
                })
                ->orderBy('nm_kec')
                ->get();

            return response()->json($kecamatan);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kecamatan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all kelurahan data from database for independent filtering
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllKelurahanFromDb()
    {
        try {
            $kelurahan = \DB::table('kelurahan')
                ->select('kd_kel', 'nm_kel')
                ->whereExists(function($query) {
                    $query->select(\DB::raw(1))
                          ->from('pasien')
                          ->whereColumn('pasien.kd_kel', 'kelurahan.kd_kel');
                })
                ->orderBy('nm_kel')
                ->get();

            return response()->json($kelurahan);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data kelurahan: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get top 10 diseases data for charts
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTopPenyakitData(Request $request)
    {
        try {
            $query = \DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel');

            // Apply filters
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('reg_periksa.tgl_registrasi', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->has('kd_kab') && $request->kd_kab) {
                $query->where('pasien.kd_kab', $request->kd_kab);
            }

            if ($request->has('kd_kec') && $request->kd_kec) {
                $query->where('pasien.kd_kec', $request->kd_kec);
            }

            if ($request->has('kd_kel') && $request->kd_kel) {
                $query->where('pasien.kd_kel', $request->kd_kel);
            }

            if ($request->has('prioritas') && $request->prioritas) {
                $query->where('diagnosa_pasien.prioritas', $request->prioritas);
            }

            // Get top 10 diseases
            $topDiseases = (clone $query)
                ->select(
                    'penyakit.kd_penyakit',
                    'penyakit.nm_penyakit',
                    \DB::raw('COUNT(*) as total_kasus'),
                    \DB::raw('COUNT(DISTINCT reg_periksa.no_rkm_medis) as pasien_unik')
                )
                ->groupBy('penyakit.kd_penyakit', 'penyakit.nm_penyakit')
                ->orderBy('total_kasus', 'desc')
                ->limit(10)
                ->get();

            // Get gender distribution for top diseases
            $genderDistribution = (clone $query)
                ->whereIn('penyakit.kd_penyakit', $topDiseases->pluck('kd_penyakit'))
                ->select(
                    'penyakit.nm_penyakit',
                    'pasien.jk',
                    \DB::raw('COUNT(*) as total')
                )
                ->groupBy('penyakit.nm_penyakit', 'pasien.jk')
                ->get()
                ->groupBy('nm_penyakit');

            // Get age group distribution for top diseases
            $ageDistribution = (clone $query)
                ->whereIn('penyakit.kd_penyakit', $topDiseases->pluck('kd_penyakit'))
                ->select(
                    'penyakit.nm_penyakit',
                    \DB::raw('CASE 
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) < 18 THEN "0-17"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) BETWEEN 18 AND 30 THEN "18-30"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) BETWEEN 31 AND 50 THEN "31-50"
                        WHEN TIMESTAMPDIFF(YEAR, pasien.tgl_lahir, reg_periksa.tgl_registrasi) BETWEEN 51 AND 65 THEN "51-65"
                        ELSE "65+"
                    END as age_group'),
                    \DB::raw('COUNT(*) as total')
                )
                ->groupBy('penyakit.nm_penyakit', 'age_group')
                ->get()
                ->groupBy('nm_penyakit');

            // Get monthly trend for top 5 diseases
            $monthlyTrend = (clone $query)
                ->whereIn('penyakit.kd_penyakit', $topDiseases->take(5)->pluck('kd_penyakit'))
                ->select(
                    'penyakit.nm_penyakit',
                    \DB::raw('DATE_FORMAT(reg_periksa.tgl_registrasi, "%Y-%m") as bulan'),
                    \DB::raw('COUNT(*) as total')
                )
                ->where('reg_periksa.tgl_registrasi', '>=', \DB::raw('DATE_SUB(CURDATE(), INTERVAL 12 MONTH)'))
                ->groupBy('penyakit.nm_penyakit', 'bulan')
                ->orderBy('bulan')
                ->get()
                ->groupBy('nm_penyakit');

            // Get summary statistics
            $summary = [
                'totalDiagnoses' => (clone $query)->count(),
                'uniqueDiseases' => (clone $query)->distinct('penyakit.kd_penyakit')->count('penyakit.kd_penyakit'),
                'totalPatients' => (clone $query)->distinct('reg_periksa.no_rkm_medis')->count('reg_periksa.no_rkm_medis'),
                'avgCasesPerDisease' => $topDiseases->avg('total_kasus'),
                'topDiseasePercentage' => $topDiseases->isNotEmpty() ? 
                    round(($topDiseases->first()->total_kasus / (clone $query)->count()) * 100, 2) : 0
            ];

            // Format chart data
            $charts = [
                'topDiseases' => [
                    'labels' => $topDiseases->pluck('nm_penyakit')->toArray(),
                    'data' => $topDiseases->pluck('total_kasus')->map(fn($val) => (int)$val)->toArray(),
                    'patients' => $topDiseases->pluck('pasien_unik')->map(fn($val) => (int)$val)->toArray()
                ],
                'genderDistribution' => $genderDistribution->map(function ($diseases) {
                    $result = ['L' => 0, 'P' => 0];
                    foreach ($diseases as $disease) {
                        $result[$disease->jk] = (int)$disease->total;
                    }
                    return [
                        'labels' => ['Laki-laki', 'Perempuan'],
                        'data' => [$result['L'], $result['P']]
                    ];
                })->toArray(),
                'ageDistribution' => $ageDistribution->map(function ($ages) {
                    $ageGroups = ['0-17' => 0, '18-30' => 0, '31-50' => 0, '51-65' => 0, '65+' => 0];
                    foreach ($ages as $age) {
                        $ageGroups[$age->age_group] = (int)$age->total;
                    }
                    return [
                        'labels' => array_keys($ageGroups),
                        'data' => array_values($ageGroups)
                    ];
                })->toArray(),
                'monthlyTrend' => $monthlyTrend->map(function ($months) {
                    $monthData = [];
                    $dataValues = [];
                    foreach ($months as $month) {
                        $monthData[] = $month->bulan;
                        $dataValues[] = (int)$month->total;
                    }
                    return [
                        'labels' => $monthData,
                        'data' => $dataValues
                    ];
                })->toArray()
            ];

            // Provide sample data if no real data exists
            if (empty($charts['topDiseases']['data'])) {
                $charts = [
                    'topDiseases' => [
                        'labels' => ['Belum ada data'],
                        'data' => [1],
                        'patients' => [1]
                    ],
                    'genderDistribution' => [],
                    'ageDistribution' => [],
                    'monthlyTrend' => []
                ];
                $summary['totalDiagnoses'] = 0;
                $summary['uniqueDiseases'] = 0;
                $summary['totalPatients'] = 0;
                $summary['avgCasesPerDisease'] = 0;
                $summary['topDiseasePercentage'] = 0;
            }

            return response()->json([
                'success' => true,
                'summary' => $summary,
                'charts' => $charts
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memuat data penyakit: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export top diseases data to Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportTopPenyakitData(Request $request)
    {
        try {
            $query = \DB::table('diagnosa_pasien')
                ->join('penyakit', 'diagnosa_pasien.kd_penyakit', '=', 'penyakit.kd_penyakit')
                ->join('reg_periksa', 'diagnosa_pasien.no_rawat', '=', 'reg_periksa.no_rawat')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->leftJoin('kabupaten', 'pasien.kd_kab', '=', 'kabupaten.kd_kab')
                ->leftJoin('kecamatan', 'pasien.kd_kec', '=', 'kecamatan.kd_kec')
                ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
                ->select(
                    'penyakit.kd_penyakit',
                    'penyakit.nm_penyakit',
                    'diagnosa_pasien.prioritas',
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.jk',
                    'pasien.tgl_lahir',
                    'kabupaten.nm_kab',
                    'kecamatan.nm_kec',
                    'kelurahan.nm_kel',
                    'reg_periksa.tgl_registrasi'
                );

            // Apply same filters as chart data
            if ($request->has('start_date') && $request->has('end_date')) {
                $query->whereBetween('reg_periksa.tgl_registrasi', [
                    $request->start_date,
                    $request->end_date
                ]);
            }

            if ($request->has('kd_kab') && $request->kd_kab) {
                $query->where('pasien.kd_kab', $request->kd_kab);
            }

            if ($request->has('kd_kec') && $request->kd_kec) {
                $query->where('pasien.kd_kec', $request->kd_kec);
            }

            if ($request->has('kd_kel') && $request->kd_kel) {
                $query->where('pasien.kd_kel', $request->kd_kel);
            }

            if ($request->has('prioritas') && $request->prioritas) {
                $query->where('diagnosa_pasien.prioritas', $request->prioritas);
            }

            $data = $query->orderBy('reg_periksa.tgl_registrasi', 'desc')->get();

            // For now, return JSON. In production, implement Excel export using Laravel Excel package
            return response()->json([
                'success' => true,
                'message' => 'Data siap untuk diekspor',
                'total_records' => $data->count(),
                'download_url' => '/temp/top-penyakit-export-' . date('Y-m-d') . '.xlsx'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengekspor data: ' . $e->getMessage()
            ], 500);
        }
    }
}