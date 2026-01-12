<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CkgSekolahExport;
use App\Models\DataSekolah;
use App\Models\JenisSekolah;
use App\Models\DataKelas;

class DashboardCkgSekolahController extends Controller
{
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    public function index(Request $request)
    {
        View::share('title', 'Analisa CKG Sekolah');

        $sekolahId = $request->get('sekolah') ?: null;
        $jenisSekolahId = $request->get('jenis_sekolah') ?: null;
        $kelasId = $request->get('kelas') ?: null;
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        if (empty($tanggalAwal) || empty($tanggalAkhir)) {
            $tahun = now()->format('Y');
            $tanggalAwal = $tahun . '-01-01';
            $tanggalAkhir = $tahun . '-12-31';
        }

        $base = DB::table('skrining_siswa_sd');
        if (Schema::hasColumn('skrining_siswa_sd', 'no_rkm_medis')) {
            $base->leftJoin('data_siswa_sekolah', 'skrining_siswa_sd.no_rkm_medis', '=', 'data_siswa_sekolah.no_rkm_medis');
        } else {
            $base->leftJoin('data_siswa_sekolah', 'skrining_siswa_sd.siswa_id', '=', 'data_siswa_sekolah.id');
        }
        $base->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->leftJoin('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->leftJoin('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
            ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
            ->where(function($q) use ($tanggalAwal, $tanggalAkhir) {
                if (Schema::hasColumn('skrining_siswa_sd', 'tanggal_skrining')) {
                    $q->whereBetween('skrining_siswa_sd.tanggal_skrining', [$tanggalAwal, $tanggalAkhir]);
                } else {
                    $q->whereBetween('skrining_siswa_sd.created_at', [$tanggalAwal, $tanggalAkhir]);
                }
            });

        if (Schema::hasColumn('pasien', 'data_posyandu')) {
            $base->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.nama_posyandu');
        }

        if ($sekolahId) {
            $base->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        if ($jenisSekolahId) {
            $base->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
        }
        if ($kelasId) {
            $base->where('data_siswa_sekolah.id_kelas', $kelasId);
        }

        $idCol = Schema::hasColumn('skrining_siswa_sd', 'id_skrining_siswa_sd') ? 'skrining_siswa_sd.id_skrining_siswa_sd' : 'skrining_siswa_sd.id';

        $statusCol = Schema::hasColumn('skrining_siswa_sd', 'status_skrining')
            ? 'status_skrining'
            : (Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung') ? 'kebugaran_jantung' : null);
        if ($statusCol) {
            $totalNormal = (clone $base)
                ->where('skrining_siswa_sd.' . $statusCol, 'Normal')
                ->distinct($idCol)
                ->count($idCol);
            $totalPerlu = (clone $base)
                ->where(function($q) use ($statusCol) {
                    $q->where('skrining_siswa_sd.' . $statusCol, 'Perlu Perhatian')
                      ->orWhere('skrining_siswa_sd.' . $statusCol, 'Perlu');
                })
                ->distinct($idCol)
                ->count($idCol);
            $totalRujuk = (clone $base)
                ->where('skrining_siswa_sd.' . $statusCol, 'Rujuk')
                ->distinct($idCol)
                ->count($idCol);
        } else {
            $totalNormal = (clone $base)
                ->where('skrining_siswa_sd.kategori_status_gizi', 'Normal')
                ->distinct($idCol)
                ->count($idCol);
            $totalPerlu = 0;
            $totalRujuk = 0;
        }

        $antropometri = (clone $base)
            ->select(DB::raw('AVG(skrining_siswa_sd.berat_badan) as bb_avg'), DB::raw('AVG(skrining_siswa_sd.tinggi_badan) as tb_avg'), DB::raw('AVG(skrining_siswa_sd.imt) as imt_avg'))
            ->first();

        $statCol = $statusCol ?: 'kategori_status_gizi';
        $statistikSekolah = (clone $base)
            ->select(
                'data_sekolah.nama_sekolah',
                DB::raw("COUNT(DISTINCT $idCol) as total"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Normal', $idCol, NULL)) as normal"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Perlu Perhatian' OR skrining_siswa_sd.$statCol = 'Perlu', $idCol, NULL)) as perlu"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Rujuk', $idCol, NULL)) as rujuk")
            )
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $statistikKelas = (clone $base)
            ->select(
                'data_kelas.kelas',
                DB::raw("COUNT(DISTINCT $idCol) as total"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Normal', $idCol, NULL)) as normal"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Perlu Perhatian' OR skrining_siswa_sd.$statCol = 'Perlu', $idCol, NULL)) as perlu"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Rujuk', $idCol, NULL)) as rujuk")
            )
            ->groupBy('data_kelas.kelas')
            ->orderBy('data_kelas.kelas')
            ->get();

        $dateCol = Schema::hasColumn('skrining_siswa_sd', 'tanggal_skrining') ? 'skrining_siswa_sd.tanggal_skrining' : 'skrining_siswa_sd.created_at';
        $trenBulanan = (clone $base)
            ->select(DB::raw("DATE_FORMAT($dateCol, '%Y-%m') as bulan"), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $trenBulananPerSekolah = (clone $base)
            ->select(DB::raw("DATE_FORMAT($dateCol, '%Y-%m') as bulan"), 'data_sekolah.nama_sekolah', DB::raw('COUNT(*) as total'))
            ->groupBy('bulan', 'data_sekolah.nama_sekolah')
            ->orderBy('bulan')
            ->get();

        $totalRowsSkriningAll = DB::table('skrining_siswa_sd')->count();
        $filteredDistinctRows = (clone $base)->distinct($idCol)->count($idCol);
        $createdWindowRows = DB::table('skrining_siswa_sd')->whereBetween('created_at', [$tanggalAwal, $tanggalAkhir])->count();
        $tanggalWindowRows = Schema::hasColumn('skrining_siswa_sd', 'tanggal_skrining')
            ? DB::table('skrining_siswa_sd')->whereBetween('tanggal_skrining', [$tanggalAwal, $tanggalAkhir])->count()
            : null;

        $topSekolah = collect($statistikSekolah ?? [])
            ->sortByDesc(function($row){ return $row->total ?? 0; })
            ->take(6)
            ->pluck('nama_sekolah')
            ->values()
            ->all();

        // Kolom identitas siswa unik untuk konsistensi perhitungan
        $studentDistinctCol = Schema::hasColumn('data_siswa_sekolah', 'no_rkm_medis')
            ? 'data_siswa_sekolah.no_rkm_medis'
            : (Schema::hasColumn('skrining_siswa_sd', 'siswa_id') ? 'skrining_siswa_sd.siswa_id' : $idCol);

        $siswaBase = DB::table('data_siswa_sekolah')
            ->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah');
        if ($sekolahId) {
            $siswaBase->where('data_siswa_sekolah.id_sekolah', $sekolahId);
        }
        if ($jenisSekolahId) {
            $siswaBase->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId);
        }
        if ($kelasId) {
            $siswaBase->where('data_siswa_sekolah.id_kelas', $kelasId);
        }
        $totalSiswaAll = (clone $siswaBase)->count();

        $siswaPerSekolah = (clone $siswaBase)
            ->select('data_sekolah.nama_sekolah', DB::raw('COUNT(*) as total_siswa'))
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $skriningPerSekolah = (clone $base)
            ->select('data_sekolah.nama_sekolah', DB::raw("COUNT(DISTINCT $studentDistinctCol) as siswa_terskrining"))
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $cakupanPerSekolah = collect($siswaPerSekolah)
            ->mapWithKeys(function(object $row){
                return [
                    $row->nama_sekolah => [
                        'nama_sekolah' => $row->nama_sekolah,
                        'total_siswa' => (int)($row->total_siswa ?? 0),
                        'terskrining' => 0,
                        'persen' => 0,
                    ]
                ];
            });
        foreach ($skriningPerSekolah as $row) {
            $nama = $row->nama_sekolah ?? 'Tanpa Sekolah';
            $existing = $cakupanPerSekolah->get($nama, [
                'nama_sekolah' => $nama,
                'total_siswa' => 0,
                'terskrining' => 0,
                'persen' => 0,
            ]);
            $existing['terskrining'] = (int)($row->siswa_terskrining ?? 0);
            $cakupanPerSekolah->put($nama, $existing);
        }
        $cakupanPerSekolah = collect($cakupanPerSekolah)
            ->values()
            ->map(function($it){
                $t = (int)($it['total_siswa'] ?? 0);
                $s = (int)($it['terskrining'] ?? 0);
                $it['persen'] = $t > 0 ? round(($s / $t) * 100, 2) : 0;
                return $it;
            })
            ->values();

        $columnsByCategory = [
            'gula_darah' => ['sering_bangun_sd','sering_haus_sekolah','sering_lapar','berat_turun_sekolah','sering_ngompol_sekolah','riwayat_dm_sd'],
            'gejala_cemas' => ['gejala_cemas_khawatir','gejala_cemas_berfikir_lebih','gejala_cemas_sulit_konsentrasi'],
            'gejala_depresi' => ['depresi_anak_sedih','depresi_anak_tidaksuka','depresi_anak_capek'],
            'malaria' => ['malaria_gejala','malaria_sakit','malaria_tempat'],
            'tropis_terabaikan' => ['tropis_bercak','tropis_koreng'],
            'riwayat_imunisasi' => ['imunisasi_hepatitis','imunisasi_bcg','imunisasi_opv1','imunisasi_dpt1','imunisasi_opv2','imunisasi_dpt2','imunisasi_opv3','imunisasi_dpt3','imunisasi_opv4','imunisasi_ipv','imunisasi_campak1','imunisasi_dpt4','imunisasi_campak2'],
            'resiko_hepatitis' => ['tes_hepatitis_sekolah','keluarga_hepatitis_sekolah','tranfusi_darah_sekolah','cucidarah_sekolah'],
            'resiko_tbc' => ['tbc_batuk_lama','tbc_bb_turun','tbc_demam','tbc_lesu','tbc_kontak'],
            'antropometri_fields' => ['berat_badan','tinggi_badan','nilai_imt','z_score','kategori_status_gizi','imt'],
            'tekanan_darah' => ['sistole','diastole'],
            'mata_telinga' => ['gangguan_telingga_kanan','gangguan_telingga_kiri','serumen_kanan','serumen_kiri','infeksi_telingga_kanan','infeksi_telingga_kiri','selaput_mata_kanan','selaput_mata_kiri','visus_mata_kanan','visus_mata_kiri','kacamata'],
            'merokok' => ['merokok_aktif_sd','jenis_rokok_sd','jumlah_rokok_sd','lama_rokok_sd','terpapar_rokok_sd'],
            'reproduksi_putri' => ['menstruasi','haid_pertama','keputihan','gatal_kemaluan_puteri'],
            'reproduksi_putra' => ['gatal_kemaluan_putra','nyeri_bak_bab','luka_penis_dubur'],
            'kelayakan_kebugaran' => ['kebugaran_tulang','kebugaran_jantung','kebugaran_asma','kebugaran_pingsan','kebugaran_jasmani'],
            'aktivitas_fisik' => ['aktivitas_fisik_jumlah','aktifitas_fisik_waktu'],
            'resiko_hepa_smp_sma' => ['resiko_hepa_smp_sma_1','resiko_hepa_smp_sma_2','resiko_hepa_smp_sma_3','resiko_hepa_smp_sma_4','resiko_hepa_smp_sma_5','resiko_hepa_smp_sma_6','resiko_hepa_smp_sma_7','resiko_hepa_smp_sma_8'],
            'resiko_talasemia' => ['talasemia_1','talasemia_2'],
            'pemeriksaan_lab' => ['hasil_gds','pemeriksaan_hb','hasil_hb'],
            'riwayat_hpv' => ['riwayat_hpv_9'],
            'resiko_mental_health' => ['khawatir','berpikir_berlebihan','sulit_tidur','sedih','tidak_tertarik','capok']
        ];

        // Penentu kolom distinct siswa untuk semua agregasi agar konsisten di live (sudah dideklarasikan di atas)

        // Bangun agregasi berbasis COUNT DISTINCT per siswa untuk menghindari duplikasi dari join
        $aggSelects = [];
        foreach ($columnsByCategory as $category => $cols) {
            foreach ($cols as $col) {
                if (Schema::hasColumn('skrining_siswa_sd', $col)) {
                    $aggSelects[] = "COUNT(DISTINCT IF(skrining_siswa_sd.$col = 'Ya', $studentDistinctCol, NULL)) as `{$col}_yes_distinct`";
                    $aggSelects[] = "COUNT(DISTINCT IF(skrining_siswa_sd.$col = 'Tidak', $studentDistinctCol, NULL)) as `{$col}_no_distinct`";
                    $aggSelects[] = "COUNT(DISTINCT IF(skrining_siswa_sd.$col IS NOT NULL AND skrining_siswa_sd.$col <> '', $studentDistinctCol, NULL)) as `{$col}_filled_distinct`";
                }
            }
        }

        if (Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung')) {
            $aggSelects[] = "SUM(CASE WHEN skrining_siswa_sd.kebugaran_jantung = 'Normal' THEN 1 ELSE 0 END) as kebugaran_jantung_normal";
            $aggSelects[] = "SUM(CASE WHEN skrining_siswa_sd.kebugaran_jantung = 'Perlu Perhatian' THEN 1 ELSE 0 END) as kebugaran_jantung_perlu";
            $aggSelects[] = "SUM(CASE WHEN skrining_siswa_sd.kebugaran_jantung = 'Rujuk' THEN 1 ELSE 0 END) as kebugaran_jantung_rujuk";
        }

        $aggRow = (clone $base)->select(DB::raw(implode(', ', $aggSelects)))->first();

        $agregasi = [];
        foreach ($columnsByCategory as $category => $cols) {
            $items = [];
            foreach ($cols as $col) {
                if (Schema::hasColumn('skrining_siswa_sd', $col)) {
                    $yesD = (int)($aggRow->{$col . '_yes_distinct'} ?? 0);
                    $noD  = (int)($aggRow->{$col . '_no_distinct'} ?? 0);
                    $fillD = (int)($aggRow->{$col . '_filled_distinct'} ?? 0);
                    $items[] = [
                        'name' => $col,
                        'yes' => $yesD,
                        'no' => $noD,
                        'count' => ($yesD + $noD) > 0 ? ($yesD + $noD) : $fillD,
                    ];
                }
            }
            if (!empty($items)) {
                $agregasi[$category] = $items;
            }
        }

        $distinctSiswa = (clone $base)
            ->distinct($studentDistinctCol)
            ->count($studentDistinctCol);

        $gulaColsExisting = array_values(array_filter($columnsByCategory['gula_darah'], function($c){
            return Schema::hasColumn('skrining_siswa_sd', $c);
        }));

        $riskStudentsCount = 0;
        if (!empty($gulaColsExisting)) {
            $riskStudentsCount = (clone $base)
                ->where(function($q) use ($gulaColsExisting) {
                    foreach ($gulaColsExisting as $c) {
                        $q->orWhere("skrining_siswa_sd.$c", '=', 'Ya');
                    }
                })
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
        }

        $persenResikoGulaDarah = $distinctSiswa > 0 ? round(($riskStudentsCount / $distinctSiswa) * 100, 2) : 0;

        $persenResikoKategori = [];
        foreach ($columnsByCategory as $category => $cols) {
            $existing = array_values(array_filter($cols, function($c){
                return Schema::hasColumn('skrining_siswa_sd', $c);
            }));
            if (!empty($existing)) {
                $riskCount = (clone $base)
                    ->where(function($q) use ($existing) {
                        foreach ($existing as $c) {
                            $q->orWhere("skrining_siswa_sd.$c", '=', 'Ya');
                        }
                    })
                    ->distinct($studentDistinctCol)
                    ->count($studentDistinctCol);
                $persenResikoKategori[$category] = [
                    'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
                    'riskCount' => $riskCount,
                    'totalDistinct' => $distinctSiswa
                ];
            }
        }

        $gdsNormal = 0; $gdsAbnormal = 0; $hbNormal = 0; $hbAbnormal = 0; $imtNormal = 0; $imtAbnormal = 0; $sgNormal = 0; $sgAbnormal = 0; $bpNormal = 0; $bpAbnormal = 0;
        $imtAdultUnder = 0; $imtAdultNormal = 0; $imtAdultOver = 0; $imtAdultObese = 0;
        if (Schema::hasColumn('skrining_siswa_sd', 'hasil_gds')) {
            $gdsNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.hasil_gds')
                ->whereRaw('CAST(skrining_siswa_sd.hasil_gds AS DECIMAL(10,2)) <= 150')
                ->distinct($idCol)
                ->count($idCol);
            $gdsAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.hasil_gds')
                ->whereRaw('CAST(skrining_siswa_sd.hasil_gds AS DECIMAL(10,2)) > 150')
                ->distinct($idCol)
                ->count($idCol);
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'hasil_hb')) {
            $hbNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.hasil_hb')
                ->whereBetween(DB::raw('CAST(skrining_siswa_sd.hasil_hb AS DECIMAL(10,2))'), [12, 16])
                ->distinct($idCol)
                ->count($idCol);
            $hbAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.hasil_hb')
                ->where(function($q){
                    $q->whereRaw('CAST(skrining_siswa_sd.hasil_hb AS DECIMAL(10,2)) < 12')
                      ->orWhereRaw('CAST(skrining_siswa_sd.hasil_hb AS DECIMAL(10,2)) > 16');
                })
                ->distinct($idCol)
                ->count($idCol);
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'imt')) {
            $imtNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereBetween(DB::raw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2))'), [18.5, 24.9])
                ->distinct($idCol)
                ->count($idCol);
            $imtAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->where(function($q){
                    $q->whereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) < 18.5')
                      ->orWhereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) > 24.9');
                })
                ->distinct($idCol)
                ->count($idCol);
        }
        // Build distribusi enum untuk Mata & Telinga
        $mtEnumMap = [
            'gangguan_telingga_kanan' => ['Normal','Ada indikasi gangguan pendengaran'],
            'gangguan_telingga_kiri' => ['Normal','Ada indikasi gangguan pendengaran'],
            'serumen_kanan' => ['Tidak ada serumen impaksi','Ada serumen impaksi'],
            'serumen_kiri' => ['Tidak ada serumen impaksi','Ada serumen impaksi'],
            'infeksi_telingga_kanan' => ['Tidak ada infeksi telinga','Ada infeksi telinga'],
            'infeksi_telingga_kiri' => ['Tidak ada infeksi telinga','Ada infeksi telinga'],
            'selaput_mata_kanan' => ['Tidak','Ya'],
            'selaput_mata_kiri' => ['Tidak','Ya'],
            'visus_mata_kanan' => ['Visus 6/6 - 6/9','Visus <6/9'],
            'visus_mata_kiri' => ['Visus 6/6 - 6/9','Visus <6/9'],
            'kacamata' => ['Tidak','Ya'],
        ];
        $itemsMt = [];
        foreach ($mtEnumMap as $field => $vals) {
            if (!Schema::hasColumn('skrining_siswa_sd', $field)) { continue; }
            $dist = [];
            foreach ($vals as $val) {
                $cnt = (clone $base)
                    ->where('skrining_siswa_sd.' . $field, '=', $val)
                    ->distinct($studentDistinctCol)
                    ->count($studentDistinctCol);
                $dist[$val] = $cnt;
            }
            $itemsMt[] = [
                'name' => $field,
                'dist' => $dist,
                'filled' => array_sum($dist),
            ];
        }
        if (!empty($itemsMt)) {
            $kategoriAnalisa['mata_telinga'] = [
                'items' => $itemsMt,
                'summary' => (function() use ($base, $studentDistinctCol, $distinctSiswa) {
                    $riskCount = (clone $base)
                        ->where(function($q){
                            if (Schema::hasColumn('skrining_siswa_sd','gangguan_telingga_kanan')) $q->orWhere('skrining_siswa_sd.gangguan_telingga_kanan', '=', 'Ada indikasi gangguan pendengaran');
                            if (Schema::hasColumn('skrining_siswa_sd','gangguan_telingga_kiri')) $q->orWhere('skrining_siswa_sd.gangguan_telingga_kiri', '=', 'Ada indikasi gangguan pendengaran');
                            if (Schema::hasColumn('skrining_siswa_sd','serumen_kanan')) $q->orWhere('skrining_siswa_sd.serumen_kanan', '=', 'Ada serumen impaksi');
                            if (Schema::hasColumn('skrining_siswa_sd','serumen_kiri')) $q->orWhere('skrining_siswa_sd.serumen_kiri', '=', 'Ada serumen impaksi');
                            if (Schema::hasColumn('skrining_siswa_sd','infeksi_telingga_kanan')) $q->orWhere('skrining_siswa_sd.infeksi_telingga_kanan', '=', 'Ada infeksi telinga');
                            if (Schema::hasColumn('skrining_siswa_sd','infeksi_telingga_kiri')) $q->orWhere('skrining_siswa_sd.infeksi_telingga_kiri', '=', 'Ada infeksi telinga');
                            if (Schema::hasColumn('skrining_siswa_sd','selaput_mata_kanan')) $q->orWhere('skrining_siswa_sd.selaput_mata_kanan', '=', 'Ya');
                            if (Schema::hasColumn('skrining_siswa_sd','selaput_mata_kiri')) $q->orWhere('skrining_siswa_sd.selaput_mata_kiri', '=', 'Ya');
                            if (Schema::hasColumn('skrining_siswa_sd','visus_mata_kanan')) $q->orWhere('skrining_siswa_sd.visus_mata_kanan', '=', 'Visus <6/9');
                            if (Schema::hasColumn('skrining_siswa_sd','visus_mata_kiri')) $q->orWhere('skrining_siswa_sd.visus_mata_kiri', '=', 'Visus <6/9');
                            if (Schema::hasColumn('skrining_siswa_sd','kacamata')) $q->orWhere('skrining_siswa_sd.kacamata', '=', 'Ya');
                        })
                        ->distinct($studentDistinctCol)
                        ->count($studentDistinctCol);
                    return [
                        'totalDistinct' => $distinctSiswa,
                        'riskCount' => $riskCount,
                        'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
                    ];
                })(),
            ];
        }

        if (Schema::hasColumn('skrining_siswa_sd', 'nilai_imt')) {
            $imtAdultUnder = (clone $base)
                ->whereNotNull('skrining_siswa_sd.nilai_imt')
                ->whereRaw('CAST(skrining_siswa_sd.nilai_imt AS DECIMAL(10,2)) <= 18.49')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.nilai_imt')
                ->whereBetween(DB::raw('CAST(skrining_siswa_sd.nilai_imt AS DECIMAL(10,2))'), [18.5, 24.9])
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultOver = (clone $base)
                ->whereNotNull('skrining_siswa_sd.nilai_imt')
                ->whereRaw('CAST(skrining_siswa_sd.nilai_imt AS DECIMAL(10,2)) > 25 AND CAST(skrining_siswa_sd.nilai_imt AS DECIMAL(10,2)) <= 27')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultObese = (clone $base)
                ->whereNotNull('skrining_siswa_sd.nilai_imt')
                ->whereRaw('CAST(skrining_siswa_sd.nilai_imt AS DECIMAL(10,2)) > 27')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
        } else if (Schema::hasColumn('skrining_siswa_sd', 'imt')) {
            $imtAdultUnder = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) <= 18.49')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereBetween(DB::raw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2))'), [18.5, 24.9])
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultOver = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) > 25 AND CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) <= 27')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $imtAdultObese = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) > 27')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
        }
        $sgCol = null;
        if (Schema::hasColumn('skrining_siswa_sd', 'kategori_status_gizi')) { $sgCol = 'kategori_status_gizi'; }
        else if (Schema::hasColumn('skrining_siswa_sd', 'status_gizi')) { $sgCol = 'status_gizi'; }
        if (!empty($sgCol)) {
            $sgNormal = (clone $base)
                ->where('skrining_siswa_sd.' . $sgCol, 'Normal')
                ->distinct($idCol)
                ->count($idCol);
            $sgAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.' . $sgCol)
                ->where('skrining_siswa_sd.' . $sgCol, '<>', 'Normal')
                ->distinct($idCol)
                ->count($idCol);
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'sistole') && Schema::hasColumn('skrining_siswa_sd', 'diastole')) {
            $bpNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.sistole')
                ->whereNotNull('skrining_siswa_sd.diastole')
                ->whereRaw('CAST(skrining_siswa_sd.sistole AS UNSIGNED) < 120 AND CAST(skrining_siswa_sd.diastole AS UNSIGNED) < 80')
                ->distinct($idCol)
                ->count($idCol);
            $bpAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.sistole')
                ->whereNotNull('skrining_siswa_sd.diastole')
                ->where(function($q){
                    $q->whereRaw('CAST(skrining_siswa_sd.sistole AS UNSIGNED) >= 120')
                      ->orWhereRaw('CAST(skrining_siswa_sd.diastole AS UNSIGNED) >= 80');
                })
                ->distinct($idCol)
                ->count($idCol);
        }
        $karies1 = 0; $karies2 = 0; $karies3 = 0; $kariesGt3 = 0;
        if (Schema::hasColumn('skrining_siswa_sd', 'gigi_karies')) {
            $karies1 = (clone $base)
                ->where('skrining_siswa_sd.gigi_karies', '=', '1')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $karies2 = (clone $base)
                ->where('skrining_siswa_sd.gigi_karies', '=', '2')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $karies3 = (clone $base)
                ->where('skrining_siswa_sd.gigi_karies', '=', '3')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
            $kariesGt3 = (clone $base)
                ->where('skrining_siswa_sd.gigi_karies', '=', '>3')
                ->distinct($studentDistinctCol)
                ->count($studentDistinctCol);
        }
        $mataDistribusi = [];
        $enumMap = [
            'gangguan_telingga_kanan' => ['Normal','Ada indikasi gangguan pendengaran'],
            'gangguan_telingga_kiri' => ['Normal','Ada indikasi gangguan pendengaran'],
            'serumen_kanan' => ['Tidak ada serumen impaksi','Ada serumen impaksi'],
            'serumen_kiri' => ['Tidak ada serumen impaksi','Ada serumen impaksi'],
            'infeksi_telingga_kanan' => ['Tidak ada infeksi telinga','Ada infeksi telinga'],
            'infeksi_telingga_kiri' => ['Tidak ada infeksi telinga','Ada infeksi telinga'],
            'selaput_mata_kanan' => ['Tidak','Ya'],
            'selaput_mata_kiri' => ['Tidak','Ya'],
            'visus_mata_kanan' => ['Visus 6/6 - 6/9','Visus <6/9'],
            'visus_mata_kiri' => ['Visus 6/6 - 6/9','Visus <6/9'],
            'kacamata' => ['Tidak','Ya'],
        ];
        foreach ($enumMap as $col => $values) {
            if (Schema::hasColumn('skrining_siswa_sd', $col)) {
                $data = [];
                foreach ($values as $val) {
                    $cnt = (clone $base)
                        ->where('skrining_siswa_sd.' . $col, '=', $val)
                        ->distinct($studentDistinctCol)
                        ->count($studentDistinctCol);
                    $data[$val] = $cnt;
                }
                $mataDistribusi[$col] = $data;
            }
        }
        $enumDistribusi = [];
        try {
            $fullCols = DB::select('SHOW FULL COLUMNS FROM skrining_siswa_sd');
            foreach ($fullCols as $fc) {
                $field = $fc->Field ?? null;
                $type  = $fc->Type ?? '';
                if (!$field || !is_string($type)) { continue; }
                if (str_starts_with($type, 'enum(')) {
                    $inside = trim($type);
                    $inside = preg_replace('/^enum\(/i', '', $inside);
                    $inside = preg_replace('/\)$/', '', $inside);
                    $vals = [];
                    foreach (preg_split('/,(?=(?:[^\']*\'[^\']*\')*[^\']*$)/', $inside) as $v) {
                        $v = trim($v);
                        if (strlen($v) >= 2 && $v[0] === "'" && substr($v, -1) === "'") {
                            $v = substr($v, 1, -1);
                        }
                        if ($v !== '') { $vals[] = $v; }
                    }
                    $dist = [];
                    foreach ($vals as $val) {
                        $cnt = (clone $base)
                            ->where('skrining_siswa_sd.' . $field, '=', $val)
                            ->distinct($studentDistinctCol)
                            ->count($studentDistinctCol);
                        $dist[$val] = $cnt;
                    }
                    $enumDistribusi[$field] = $dist;
                }
            }
        } catch (\Throwable $e) {
            // ignore
        }
        $ringkasanPemeriksaan = [
            'lab' => [
                'hasil_gds' => ['normal' => $gdsNormal, 'tidak' => $gdsAbnormal],
                'hasil_hb' => ['normal' => $hbNormal, 'tidak' => $hbAbnormal],
            ],
            'antropometri' => [
                'imt' => ['normal' => $imtNormal, 'tidak' => $imtAbnormal],
                'status_gizi' => ['normal' => $sgNormal, 'tidak' => $sgAbnormal],
                'kategori_imt_dewasa' => [
                    'underweight' => $imtAdultUnder,
                    'normal' => $imtAdultNormal,
                    'overweight' => $imtAdultOver,
                    'obesitas' => $imtAdultObese,
                ],
            ],
            'mata_telinga' => [
                'gigi_karies_kategori' => [
                    '1' => $karies1,
                    '2' => $karies2,
                    '3' => $karies3,
                    'gt3' => $kariesGt3,
                ],
                'distribusi' => $mataDistribusi,
            ],
            'enum_distribusi' => $enumDistribusi,
            'tekanan_darah' => [
                'bp' => ['normal' => $bpNormal, 'tidak' => $bpAbnormal],
            ],
        ];

        $daftarSekolah = DataSekolah::select('id_sekolah', 'nama_sekolah')->orderBy('nama_sekolah')->get();
        $daftarJenisSekolah = JenisSekolah::select('id', 'nama')->orderBy('nama')->get();
        $daftarKelas = DataKelas::select('id_kelas', 'kelas')->orderBy('kelas')->get();
        

        return view('ilp.data-siswa-sekolah.analisa-ckg-sekolah', compact(
            'totalNormal',
            'totalPerlu',
            'totalRujuk',
            'antropometri',
            'statistikSekolah',
            'statistikKelas',
            'trenBulanan',
            'trenBulananPerSekolah',
            'topSekolah',
            'cakupanPerSekolah',
            'agregasi',
            'distinctSiswa',
            'totalRowsSkriningAll',
            'filteredDistinctRows',
            'createdWindowRows',
            'tanggalWindowRows',
            'riskStudentsCount',
            'persenResikoGulaDarah',
            'persenResikoKategori',
            'ringkasanPemeriksaan',
            'daftarSekolah',
            'daftarJenisSekolah',
            'daftarKelas',
            'sekolahId',
            'jenisSekolahId',
            'kelasId',
            'tanggalAwal',
            'tanggalAkhir'
        ));
    }

    public function exportExcel(Request $request)
    {
        $filters = [
            'sekolah' => $request->get('sekolah'),
            'jenis_sekolah' => $request->get('jenis_sekolah'),
            'kelas' => $request->get('kelas'),
            'tanggal_awal' => $request->get('tanggal_awal'),
            'tanggal_akhir' => $request->get('tanggal_akhir')
        ];
        $export = new CkgSekolahExport($filters);
        $filename = 'hasil_ckg_sekolah_' . date('YmdHis') . '.xlsx';
        return Excel::download($export, $filename);
    }

    public function presentasi(Request $request)
    {
        View::share('title', 'Presentasi Analisa CKG Sekolah');

        $sekolahId = $request->get('sekolah') ?: null;
        $jenisSekolahId = $request->get('jenis_sekolah') ?: null;
        $kelasId = $request->get('kelas') ?: null;
        $tanggalAwal = $request->get('tanggal_awal');
        $tanggalAkhir = $request->get('tanggal_akhir');

        if (empty($tanggalAwal) || empty($tanggalAkhir)) {
            $tahun = now()->format('Y');
            $tanggalAwal = $tahun . '-01-01';
            $tanggalAkhir = $tahun . '-12-31';
        }

        $base = DB::table('skrining_siswa_sd');
        if (Schema::hasColumn('skrining_siswa_sd', 'no_rkm_medis')) {
            $base->leftJoin('data_siswa_sekolah', 'skrining_siswa_sd.no_rkm_medis', '=', 'data_siswa_sekolah.no_rkm_medis');
        } else {
            $base->leftJoin('data_siswa_sekolah', 'skrining_siswa_sd.siswa_id', '=', 'data_siswa_sekolah.id');
        }
        $base->leftJoin('pasien', 'data_siswa_sekolah.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah')
            ->leftJoin('data_kelas', 'data_siswa_sekolah.id_kelas', '=', 'data_kelas.id_kelas')
            ->leftJoin('jenis_sekolah', 'data_sekolah.id_jenis_sekolah', '=', 'jenis_sekolah.id')
            ->where(function($q) use ($tanggalAwal, $tanggalAkhir) {
                if (Schema::hasColumn('skrining_siswa_sd', 'tanggal_skrining')) {
                    $q->whereBetween('skrining_siswa_sd.tanggal_skrining', [$tanggalAwal, $tanggalAkhir]);
                } else {
                    $q->whereBetween('skrining_siswa_sd.created_at', [$tanggalAwal, $tanggalAkhir]);
                }
            });

        if ($sekolahId) { $base->where('data_siswa_sekolah.id_sekolah', $sekolahId); }
        if ($jenisSekolahId) { $base->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId); }
        if ($kelasId) { $base->where('data_siswa_sekolah.id_kelas', $kelasId); }

        $idCol = Schema::hasColumn('skrining_siswa_sd', 'id_skrining_siswa_sd') ? 'skrining_siswa_sd.id_skrining_siswa_sd' : 'skrining_siswa_sd.id';
        $statusCol = Schema::hasColumn('skrining_siswa_sd', 'status_skrining')
            ? 'status_skrining'
            : (Schema::hasColumn('skrining_siswa_sd', 'kebugaran_jantung') ? 'kebugaran_jantung' : null);

        if ($statusCol) {
            $totalNormal = (clone $base)->where('skrining_siswa_sd.' . $statusCol, 'Normal')->distinct($idCol)->count($idCol);
            $totalPerlu = (clone $base)
                ->where(function($q) use ($statusCol) {
                    $q->where('skrining_siswa_sd.' . $statusCol, 'Perlu Perhatian')
                      ->orWhere('skrining_siswa_sd.' . $statusCol, 'Perlu');
                })
                ->distinct($idCol)
                ->count($idCol);
            $totalRujuk = (clone $base)->where('skrining_siswa_sd.' . $statusCol, 'Rujuk')->distinct($idCol)->count($idCol);
        } else {
            $totalNormal = (clone $base)->where('skrining_siswa_sd.kategori_status_gizi', 'Normal')->distinct($idCol)->count($idCol);
            $totalPerlu = 0;
            $totalRujuk = 0;
        }

        $antropometri = (clone $base)
            ->select(DB::raw('AVG(skrining_siswa_sd.berat_badan) as bb_avg'), DB::raw('AVG(skrining_siswa_sd.tinggi_badan) as tb_avg'), DB::raw('AVG(skrining_siswa_sd.imt) as imt_avg'))
            ->first();

        $statCol = $statusCol ?: 'kategori_status_gizi';
        $statistikSekolah = (clone $base)
            ->select(
                'data_sekolah.nama_sekolah',
                DB::raw("COUNT(DISTINCT $idCol) as total"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Normal', $idCol, NULL)) as normal"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Perlu Perhatian' OR skrining_siswa_sd.$statCol = 'Perlu', $idCol, NULL)) as perlu"),
                DB::raw("COUNT(DISTINCT IF(skrining_siswa_sd.$statCol = 'Rujuk', $idCol, NULL)) as rujuk")
            )
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $dateCol = Schema::hasColumn('skrining_siswa_sd', 'tanggal_skrining') ? 'skrining_siswa_sd.tanggal_skrining' : 'skrining_siswa_sd.created_at';
        $trenBulanan = (clone $base)
            ->select(DB::raw("DATE_FORMAT($dateCol, '%Y-%m') as bulan"), DB::raw('COUNT(*) as total'))
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $studentDistinctCol = Schema::hasColumn('data_siswa_sekolah', 'no_rkm_medis')
            ? 'data_siswa_sekolah.no_rkm_medis'
            : (Schema::hasColumn('skrining_siswa_sd', 'siswa_id') ? 'skrining_siswa_sd.siswa_id' : $idCol);

        $siswaBase = DB::table('data_siswa_sekolah')->leftJoin('data_sekolah', 'data_siswa_sekolah.id_sekolah', '=', 'data_sekolah.id_sekolah');
        if ($sekolahId) { $siswaBase->where('data_siswa_sekolah.id_sekolah', $sekolahId); }
        if ($jenisSekolahId) { $siswaBase->where('data_sekolah.id_jenis_sekolah', $jenisSekolahId); }
        if ($kelasId) { $siswaBase->where('data_siswa_sekolah.id_kelas', $kelasId); }

        $totalSiswaAll = (clone $siswaBase)->count();

        $siswaPerSekolah = (clone $siswaBase)
            ->select('data_sekolah.nama_sekolah', DB::raw('COUNT(*) as total_siswa'))
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $skriningPerSekolah = (clone $base)
            ->select('data_sekolah.nama_sekolah', DB::raw("COUNT(DISTINCT $studentDistinctCol) as siswa_terskrining"))
            ->groupBy('data_sekolah.nama_sekolah')
            ->orderBy('data_sekolah.nama_sekolah')
            ->get();

        $cakupanPerSekolah = collect($siswaPerSekolah)
            ->mapWithKeys(function(object $row){
                return [
                    $row->nama_sekolah => [
                        'nama_sekolah' => $row->nama_sekolah,
                        'total_siswa' => (int)($row->total_siswa ?? 0),
                        'terskrining' => 0,
                        'persen' => 0,
                    ]
                ];
            });
        foreach ($skriningPerSekolah as $row) {
            $nama = $row->nama_sekolah ?? 'Tanpa Sekolah';
            $existing = $cakupanPerSekolah->get($nama, [
                'nama_sekolah' => $nama,
                'total_siswa' => 0,
                'terskrining' => 0,
                'persen' => 0,
            ]);
            $existing['terskrining'] = (int)($row->siswa_terskrining ?? 0);
            $cakupanPerSekolah->put($nama, $existing);
        }
        $cakupanPerSekolah = collect($cakupanPerSekolah)->values()->map(function($it){
            $t = (int)($it['total_siswa'] ?? 0);
            $s = (int)($it['terskrining'] ?? 0);
            $it['persen'] = $t > 0 ? round(($s / $t) * 100, 2) : 0;
            return $it;
        })->values();

        $daftarSekolah = DataSekolah::select('id_sekolah', 'nama_sekolah')->orderBy('nama_sekolah')->get();
        $daftarJenisSekolah = JenisSekolah::select('id', 'nama')->orderBy('nama')->get();
        $daftarKelas = DataKelas::select('id_kelas', 'kelas')->orderBy('kelas')->get();

        $distinctSiswa = (clone $base)->distinct($studentDistinctCol)->count($studentDistinctCol);

        $columnsByCategory = [
            'gula_darah' => ['sering_bangun_sd','sering_haus_sekolah','sering_lapar','berat_turun_sekolah','sering_ngompol_sekolah','riwayat_dm_sd'],
            'gejala_cemas' => ['gejala_cemas_khawatir','gejala_cemas_berfikir_lebih','gejala_cemas_sulit_konsentrasi'],
            'gejala_depresi' => ['depresi_anak_sedih','depresi_anak_tidaksuka','depresi_anak_capek'],
            'malaria' => ['malaria_gejala','malaria_sakit','malaria_tempat'],
            'tropis_terabaikan' => ['tropis_bercak','tropis_koreng'],
            'riwayat_imunisasi' => ['imunisasi_hepatitis','imunisasi_bcg','imunisasi_opv1','imunisasi_dpt1','imunisasi_opv2','imunisasi_dpt2','imunisasi_opv3','imunisasi_dpt3','imunisasi_opv4','imunisasi_ipv','imunisasi_campak1','imunisasi_dpt4','imunisasi_campak2'],
            'resiko_hepatitis' => ['tes_hepatitis_sekolah','keluarga_hepatitis_sekolah','tranfusi_darah_sekolah','cucidarah_sekolah'],
            'resiko_tbc' => ['tbc_batuk_lama','tbc_bb_turun','tbc_demam','tbc_lesu','tbc_kontak'],
            'antropometri_fields' => ['berat_badan','tinggi_badan','nilai_imt','z_score','kategori_status_gizi','imt'],
            'tekanan_darah' => ['sistole','diastole'],
            'mata_telinga' => ['gangguan_telingga_kanan','gangguan_telingga_kiri','serumen_kanan','serumen_kiri','infeksi_telingga_kanan','infeksi_telingga_kiri','selaput_mata_kanan','selaput_mata_kiri','visus_mata_kanan','visus_mata_kiri','kacamata'],
            'merokok' => ['merokok_aktif_sd','jenis_rokok_sd','jumlah_rokok_sd','lama_rokok_sd','terpapar_rokok_sd'],
            'reproduksi_putri' => ['menstruasi','haid_pertama','keputihan','gatal_kemaluan_puteri'],
            'reproduksi_putra' => ['gatal_kemaluan_putra','nyeri_bak_bab','luka_penis_dubur'],
            'kelayakan_kebugaran' => ['kebugaran_tulang','kebugaran_jantung','kebugaran_asma','kebugaran_pingsan','kebugaran_jasmani'],
            'aktivitas_fisik' => ['aktivitas_fisik_jumlah','aktifitas_fisik_waktu'],
            'resiko_hepa_smp_sma' => ['resiko_hepa_smp_sma_1','resiko_hepa_smp_sma_2','resiko_hepa_smp_sma_3','resiko_hepa_smp_sma_4','resiko_hepa_smp_sma_5','resiko_hepa_smp_sma_6','resiko_hepa_smp_sma_7','resiko_hepa_smp_sma_8'],
            'resiko_talasemia' => ['talasemia_1','talasemia_2'],
            'pemeriksaan_lab' => ['hasil_gds','pemeriksaan_hb','hasil_hb'],
            'riwayat_hpv' => ['riwayat_hpv_9'],
            'resiko_mental_health' => ['khawatir','berpikir_berlebihan','sulit_tidur','sedih','tidak_tertarik','capok']
        ];

        $gulaColsExisting = array_values(array_filter($columnsByCategory['gula_darah'], function($c){
            return Schema::hasColumn('skrining_siswa_sd', $c);
        }));
        $buildItems = function(array $cols) use ($base, $studentDistinctCol) {
            $res = [];
            foreach ($cols as $c) {
                $yesD = (clone $base)->where("skrining_siswa_sd.$c", '=', 'Ya')->distinct($studentDistinctCol)->count($studentDistinctCol);
                $noD  = (clone $base)->where("skrining_siswa_sd.$c", '=', 'Tidak')->distinct($studentDistinctCol)->count($studentDistinctCol);
                $fillD = (clone $base)
                    ->whereNotNull("skrining_siswa_sd.$c")
                    ->where("skrining_siswa_sd.$c", '<>', '')
                    ->distinct($studentDistinctCol)
                    ->count($studentDistinctCol);
                $res[] = ['name' => $c, 'yes' => $yesD, 'no' => $noD, 'filled' => $fillD];
            }
            return $res;
        };
        $buildSummary = function(array $cols) use ($base, $studentDistinctCol, $distinctSiswa) {
            $riskCount = 0;
            if (!empty($cols)) {
                $riskCount = (clone $base)
                    ->where(function($q) use ($cols) { foreach ($cols as $c) { $q->orWhere("skrining_siswa_sd.$c", '=', 'Ya'); } })
                    ->distinct($studentDistinctCol)
                    ->count($studentDistinctCol);
            }
            return [
                'totalDistinct' => $distinctSiswa,
                'riskCount' => $riskCount,
                'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
            ];
        };

        $kategoriAnalisa = [];
        foreach ($columnsByCategory as $cat => $cols) {
            $existing = array_values(array_filter($cols, function($c){ return Schema::hasColumn('skrining_siswa_sd', $c); }));
            if (!empty($existing)) {
                $kategoriAnalisa[$cat] = [
                    'items' => $buildItems($existing),
                    'summary' => $buildSummary($existing),
                ];
            }
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'imt')) {
            $countEnum = function(string $label) {
                return DB::table('skrining_siswa_sd')
                    ->whereRaw('LOWER(TRIM(skrining_siswa_sd.imt)) = ?', [strtolower(trim($label))])
                    ->count();
            };
            $buruk  = $countEnum('Gizi Buruk');
            $kurang = $countEnum('Gizi Kurang');
            $baik   = $countEnum('Gizi Baik');
            $risiko = $countEnum('Berisiko gizi lebih');
            $lebih  = $countEnum('Gizi Lebih');
            $obes   = $countEnum('Obesitas');
            $itemsImt = [
                ['name' => 'gizi_buruk', 'count' => $buruk, 'filled' => $buruk],
                ['name' => 'gizi_kurang', 'count' => $kurang, 'filled' => $kurang],
                ['name' => 'gizi_baik', 'count' => $baik, 'filled' => $baik],
                ['name' => 'berisiko_gizi_lebih', 'count' => $risiko, 'filled' => $risiko],
                ['name' => 'gizi_lebih', 'count' => $lebih, 'filled' => $lebih],
                ['name' => 'obesitas', 'count' => $obes, 'filled' => $obes],
            ];
            $riskCountImt = $buruk + $kurang + $risiko + $lebih + $obes;
            $kategoriAnalisa['antropometri_fields'] = [
                'items' => $itemsImt,
                'summary' => [
                    'totalDistinct' => $distinctSiswa,
                    'riskCount' => $riskCountImt,
                    'percent' => $distinctSiswa > 0 ? round(($riskCountImt / $distinctSiswa) * 100, 2) : 0,
                ],
            ];
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'gigi_karies')) {
            $k1 = (clone $base)->where('skrining_siswa_sd.gigi_karies', '=', '1')->distinct($studentDistinctCol)->count($studentDistinctCol);
            $k2 = (clone $base)->where('skrining_siswa_sd.gigi_karies', '=', '2')->distinct($studentDistinctCol)->count($studentDistinctCol);
            $k3 = (clone $base)->where('skrining_siswa_sd.gigi_karies', '=', '3')->distinct($studentDistinctCol)->count($studentDistinctCol);
            $kgt3 = (clone $base)->where('skrining_siswa_sd.gigi_karies', '=', '>3')->distinct($studentDistinctCol)->count($studentDistinctCol);
            $kAny = (clone $base)->whereIn('skrining_siswa_sd.gigi_karies', ['1','2','3','>3'])->distinct($studentDistinctCol)->count($studentDistinctCol);
            $kNone = max(0, $distinctSiswa - $kAny);
            $kategoriAnalisa['kesehatan_gigi'] = [
                'items' => [
                    ['name' => 'tidak_ada', 'count' => $kNone, 'filled' => $kNone],
                    ['name' => '1', 'count' => $k1, 'filled' => $k1],
                    ['name' => '2', 'count' => $k2, 'filled' => $k2],
                    ['name' => '3', 'count' => $k3, 'filled' => $k3],
                    ['name' => 'gt3', 'count' => $kgt3, 'filled' => $kgt3],
                ],
                'summary' => [
                    'totalDistinct' => $distinctSiswa,
                    'riskCount' => $kAny,
                    'percent' => $distinctSiswa > 0 ? round(($kAny / $distinctSiswa) * 100, 2) : 0,
                ],
            ];
        }
        $gulaPerPertanyaan = $kategoriAnalisa['gula_darah']['items'] ?? [];
        $gulaSummary = $kategoriAnalisa['gula_darah']['summary'] ?? ['totalDistinct' => $distinctSiswa, 'riskCount' => 0, 'percent' => 0];
        $persenResikoKategori = [];
        foreach ($columnsByCategory as $category => $cols) {
            $existing = array_values(array_filter($cols, function($c){ return Schema::hasColumn('skrining_siswa_sd', $c); }));
            if (!empty($existing)) {
                if ($category === 'antropometri_fields' && Schema::hasColumn('skrining_siswa_sd', 'imt')) {
                    $countEnum = function(string $label) {
                        return DB::table('skrining_siswa_sd')
                            ->whereRaw('LOWER(TRIM(skrining_siswa_sd.imt)) = ?', [strtolower(trim($label))])
                            ->count();
                    };
                    $buruk  = $countEnum('Gizi Buruk');
                    $kurang = $countEnum('Gizi Kurang');
                    $risiko = $countEnum('Berisiko gizi lebih');
                    $lebih  = $countEnum('Gizi Lebih');
                    $obes   = $countEnum('Obesitas');
                    $riskCount = $buruk + $kurang + $risiko + $lebih + $obes;
                    $persenResikoKategori[$category] = [
                        'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
                        'riskCount' => $riskCount,
                        'totalDistinct' => $distinctSiswa
                    ];
                } else if ($category === 'mata_telinga') {
                    $riskCount = (clone $base)
                        ->where(function($q){
                            if (Schema::hasColumn('skrining_siswa_sd','gangguan_telingga_kanan')) $q->orWhere('skrining_siswa_sd.gangguan_telingga_kanan', '=', 'Ada indikasi gangguan pendengaran');
                            if (Schema::hasColumn('skrining_siswa_sd','gangguan_telingga_kiri')) $q->orWhere('skrining_siswa_sd.gangguan_telingga_kiri', '=', 'Ada indikasi gangguan pendengaran');
                            if (Schema::hasColumn('skrining_siswa_sd','serumen_kanan')) $q->orWhere('skrining_siswa_sd.serumen_kanan', '=', 'Ada serumen impaksi');
                            if (Schema::hasColumn('skrining_siswa_sd','serumen_kiri')) $q->orWhere('skrining_siswa_sd.serumen_kiri', '=', 'Ada serumen impaksi');
                            if (Schema::hasColumn('skrining_siswa_sd','infeksi_telingga_kanan')) $q->orWhere('skrining_siswa_sd.infeksi_telingga_kanan', '=', 'Ada infeksi telinga');
                            if (Schema::hasColumn('skrining_siswa_sd','infeksi_telingga_kiri')) $q->orWhere('skrining_siswa_sd.infeksi_telingga_kiri', '=', 'Ada infeksi telinga');
                            if (Schema::hasColumn('skrining_siswa_sd','selaput_mata_kanan')) $q->orWhere('skrining_siswa_sd.selaput_mata_kanan', '=', 'Ya');
                            if (Schema::hasColumn('skrining_siswa_sd','selaput_mata_kiri')) $q->orWhere('skrining_siswa_sd.selaput_mata_kiri', '=', 'Ya');
                            if (Schema::hasColumn('skrining_siswa_sd','visus_mata_kanan')) $q->orWhere('skrining_siswa_sd.visus_mata_kanan', '=', 'Visus <6/9');
                            if (Schema::hasColumn('skrining_siswa_sd','visus_mata_kiri')) $q->orWhere('skrining_siswa_sd.visus_mata_kiri', '=', 'Visus <6/9');
                            if (Schema::hasColumn('skrining_siswa_sd','kacamata')) $q->orWhere('skrining_siswa_sd.kacamata', '=', 'Ya');
                        })
                        ->distinct($studentDistinctCol)
                        ->count($studentDistinctCol);
                    $persenResikoKategori[$category] = [
                        'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
                        'riskCount' => $riskCount,
                        'totalDistinct' => $distinctSiswa
                    ];
                } else {
                    $riskCount = (clone $base)
                        ->where(function($q) use ($existing) {
                            foreach ($existing as $c) { $q->orWhere("skrining_siswa_sd.$c", '=', 'Ya'); }
                        })
                        ->distinct($studentDistinctCol)
                        ->count($studentDistinctCol);
                    $persenResikoKategori[$category] = [
                        'percent' => $distinctSiswa > 0 ? round(($riskCount / $distinctSiswa) * 100, 2) : 0,
                        'riskCount' => $riskCount,
                        'totalDistinct' => $distinctSiswa
                    ];
                }
            }
        }
        if (Schema::hasColumn('skrining_siswa_sd', 'gigi_karies')) {
            $kAny = (clone $base)->whereIn('skrining_siswa_sd.gigi_karies', ['1','2','3','>3'])->distinct($studentDistinctCol)->count($studentDistinctCol);
            $persenResikoKategori['kesehatan_gigi'] = [
                'percent' => $distinctSiswa > 0 ? round(($kAny / $distinctSiswa) * 100, 2) : 0,
                'riskCount' => $kAny,
                'totalDistinct' => $distinctSiswa
            ];
        }

        $ringkasanPemeriksaan = [
            'antropometri' => [
                'imt' => ['normal' => 0, 'tidak' => 0],
                'status_gizi' => ['normal' => 0, 'tidak' => 0],
            ],
        ];
        if (Schema::hasColumn('skrining_siswa_sd', 'imt')) {
            $imtNormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->whereBetween(DB::raw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2))'), [18.5, 24.9])
                ->distinct($idCol)
                ->count($idCol);
            $imtAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.imt')
                ->where(function($q){
                    $q->whereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) < 18.5')
                      ->orWhereRaw('CAST(skrining_siswa_sd.imt AS DECIMAL(10,2)) > 24.9');
                })
                ->distinct($idCol)
                ->count($idCol);
            $ringkasanPemeriksaan['antropometri']['imt'] = ['normal' => $imtNormal, 'tidak' => $imtAbnormal];
        }
        $sgCol = null;
        if (Schema::hasColumn('skrining_siswa_sd', 'kategori_status_gizi')) { $sgCol = 'kategori_status_gizi'; }
        else if (Schema::hasColumn('skrining_siswa_sd', 'status_gizi')) { $sgCol = 'status_gizi'; }
        if (!empty($sgCol)) {
            $sgNormal = (clone $base)->where('skrining_siswa_sd.' . $sgCol, 'Normal')->distinct($idCol)->count($idCol);
            $sgAbnormal = (clone $base)
                ->whereNotNull('skrining_siswa_sd.' . $sgCol)
                ->where('skrining_siswa_sd.' . $sgCol, '<>', 'Normal')
                ->distinct($idCol)
                ->count($idCol);
            $ringkasanPemeriksaan['antropometri']['status_gizi'] = ['normal' => $sgNormal, 'tidak' => $sgAbnormal];
        }

        return view('ilp.data-siswa-sekolah.PresentasiCkgSekolah', compact(
            'totalNormal',
            'totalPerlu',
            'totalRujuk',
            'antropometri',
            'statistikSekolah',
            'trenBulanan',
            'cakupanPerSekolah',
            'persenResikoKategori',
            'ringkasanPemeriksaan',
            'daftarSekolah',
            'daftarJenisSekolah',
            'daftarKelas',
            'sekolahId',
            'jenisSekolahId',
            'kelasId',
            'tanggalAwal',
            'tanggalAkhir',
            'distinctSiswa',
            'totalSiswaAll',
            'gulaPerPertanyaan',
            'gulaSummary',
            'kategoriAnalisa'
        ));
    }
}
