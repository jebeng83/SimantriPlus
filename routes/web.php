<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPJSTestController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log as LogFacade;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\RegPeriksaController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\AntrianPoliklinikController;
use App\Http\Controllers\AntrianDisplayController;
use App\Http\Controllers\MobileJknController;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\DeployWebhookController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route untuk test BPJS
Route::get('/test-bpjs-connection', [BPJSTestController::class, 'testConnection'])->name('test.bpjs');

// Webhook deploy dari GitHub (auto deploy)
Route::post('/webhook-deploy', [DeployWebhookController::class, 'handle'])->name('deploy.webhook');

// Route yang tidak memerlukan autentikasi
Route::get('/', [App\Http\Controllers\SkriningController::class, 'index'])->name('skrining.minimal');
Route::get('/customlogin', function() {
    return redirect('/');
})->name('customlogin');
Route::post('/customlogin', function() {
    return redirect('/');
})->name('customlogin');
Route::get('/logout', function() {
    return redirect('/');
})->name('logout');

// Error page routes
Route::get('/error', function() {
    return redirect('/');
})->name('error.500');
Route::get('/not-found', function() {
    return redirect('/');
})->name('error.404');
Route::get('/forbidden', function() {
    return redirect('/');
})->name('error.403');

Route::get('/infokesehatan', function () {
    return redirect('/');
});

Route::get('/skriningbpjs', function () {
    return redirect('/');
});

// Route untuk form skrining minimal tanpa autentikasi
Route::get('/skrining', [App\Http\Controllers\SkriningController::class, 'index'])->name('skrining.minimal');

// Route untuk menyimpan data skrining tanpa autentikasi
Route::post('/skrining/store', function(Illuminate\Http\Request $request) {
    return Redirect::to('/')->with('success', 'Data skrining berhasil disimpan');
})->name('skrining.store');

// Route untuk mendapatkan data pasien berdasarkan NIK
Route::get('/pasien/get-by-nik', function(Illuminate\Http\Request $request) {
    $nik = $request->input('nik');
    $pasien = DB::table('pasien')
        ->leftJoin('kelurahan', 'pasien.kd_kel', '=', 'kelurahan.kd_kel')
        ->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.kode_posyandu')
        ->where('pasien.no_ktp', $nik)
        ->select(
            'pasien.*',
            DB::raw('kelurahan.nm_kel as nm_kel'),
            DB::raw('data_posyandu.nama_posyandu as nama_posyandu'),
            DB::raw('pasien.data_posyandu as kode_posyandu')
        )
        ->first();
    
    if ($pasien) {
        return response()->json([
            'status' => 'success',
            'data' => $pasien
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Pasien tidak ditemukan'
        ]);
    }
})->name('pasien.get-by-nik')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Route untuk mendapatkan data posyandu berdasarkan kode
Route::get('/posyandu/get-by-kode', function(Illuminate\Http\Request $request) {
    $kode = $request->input('kode_posyandu');
    $posyandu = DB::table('data_posyandu')
        ->where('kode_posyandu', $kode)
        ->select('kode_posyandu', 'nama_posyandu', 'desa', 'alamat')
        ->first();

    if ($posyandu) {
        return response()->json([
            'status' => 'success',
            'data' => $posyandu
        ]);
    } else {
        return response()->json([
            'status' => 'error',
            'message' => 'Posyandu tidak ditemukan'
        ], 404);
    }
})->name('posyandu.get-by-kode')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Route untuk mencari posyandu (searchable) tanpa autentikasi
Route::get('/posyandu/search', function(Illuminate\Http\Request $request) {
    $q = $request->input('q');
    $desa = $request->input('desa');
    if (!$q || strlen($q) < 2) {
        return response()->json([]);
    }
    $query = DB::table('data_posyandu')
        ->select('kode_posyandu', 'nama_posyandu', 'desa', 'alamat')
        ->where(function($w) use ($q) {
            $w->where('nama_posyandu', 'like', '%' . $q . '%')
              ->orWhere('kode_posyandu', 'like', '%' . $q . '%');
        });
    if ($desa) {
        $query->where('desa', $desa);
    }
    $list = $query->orderBy('nama_posyandu', 'asc')->limit(20)->get();
    return response()->json($list);
})->name('posyandu.search')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Search wilayah: propinsi/kabupaten/kecamatan/kelurahan
Route::prefix('wilayah/search')->group(function() {
    Route::get('/propinsi', function(Illuminate\Http\Request $request) {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) return response()->json([]);
        $list = DB::table('propinsi')
            ->select('kd_prop', 'nm_prop')
            ->where('nm_prop', 'like', '%' . $q . '%')
            ->orderBy('nm_prop', 'asc')
            ->limit(20)
            ->get();
        return response()->json($list);
    })->name('wilayah.search.propinsi')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/kabupaten', function(Illuminate\Http\Request $request) {
        try {
            $q = trim((string)$request->input('q', ''));
            $kdProp = $request->input('kd_prop');
            $query = DB::table('kabupaten')->select('kd_kab', 'nm_kab');
            if (!empty($kdProp) && Schema::hasColumn('kabupaten', 'kd_prop')) {
                if (ctype_digit((string)$kdProp)) {
                    $query->where('kd_prop', (int)$kdProp);
                } else {
                    $query->where('kd_prop', 'like', '%' . $kdProp . '%');
                }
            }
            if ($q !== '') {
                $query->where(function($w) use ($q) {
                    $w->where('nm_kab', 'like', '%' . $q . '%')
                      ->orWhere('kd_kab', 'like', '%' . $q . '%');
                });
            }
            $list = $query->orderBy('nm_kab', 'asc')->limit(20)->get();
            return response()->json($list);
        } catch (\Throwable $e) {
            LogFacade::error('Kabupaten search error', ['message' => $e->getMessage()]);
            return response()->json([]);
        }
    })->name('wilayah.search.kabupaten')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/kecamatan', function(Illuminate\Http\Request $request) {
        try {
            $q = trim((string)$request->input('q', ''));
            $kdKab = $request->input('kd_kab');
            $query = DB::table('kecamatan')->select('kd_kec', 'nm_kec');
            if (!empty($kdKab) && Schema::hasColumn('kecamatan', 'kd_kab')) {
                if (ctype_digit((string)$kdKab)) {
                    $query->where('kd_kab', (int)$kdKab);
                } else {
                    $query->where('kd_kab', 'like', '%' . $kdKab . '%');
                }
            }
            if ($q !== '') {
                $query->where(function($w) use ($q) {
                    $w->where('nm_kec', 'like', '%' . $q . '%')
                      ->orWhere('kd_kec', 'like', '%' . $q . '%');
                });
            }
            $list = $query->orderBy('nm_kec', 'asc')->limit(20)->get();
            return response()->json($list);
        } catch (\Throwable $e) {
            LogFacade::error('Kecamatan search error', ['message' => $e->getMessage()]);
            return response()->json([]);
        }
    })->name('wilayah.search.kecamatan')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/kelurahan', function(Illuminate\Http\Request $request) {
        try {
            $q = trim((string)$request->input('q', ''));
            $kdKec = $request->input('kd_kec');
            $query = DB::table('kelurahan')->select(['kd_kel', 'nm_kel']);
            if (!empty($kdKec) && Schema::hasColumn('kelurahan', 'kd_kec')) {
                if (ctype_digit((string)$kdKec)) {
                    $query->where('kd_kec', (int)$kdKec);
                } else {
                    $query->where('kd_kec', 'like', '%' . $kdKec . '%');
                }
            }
            if ($q !== '') {
                $query->where(function($w) use ($q) {
                    $w->where('nm_kel', 'like', '%' . $q . '%')
                      ->orWhere('kd_kel', 'like', '%' . $q . '%');
                });
            }
            $list = $query->orderBy('nm_kel', 'asc')->limit(20)->get();
            if ($list->isEmpty()) {
                $path = public_path('assets/kelurahan.iyem');
                if (file_exists($path)) {
                    $content = file_get_contents($path);
                    $json = json_decode($content, true);
                    if (isset($json['kelurahan']) && is_array($json['kelurahan'])) {
                        $items = [];
                        foreach ($json['kelurahan'] as $row) {
                            $rid = isset($row['id']) ? (string)$row['id'] : '';
                            $rnm = isset($row['nama']) ? (string)$row['nama'] : '';
                            $rkec = isset($row['id_kecamatan']) ? (string)$row['id_kecamatan'] : '';
                            if (!empty($kdKec)) {
                                if ((string)$rkec !== (string)$kdKec) continue;
                            }
                            if ($q !== '') {
                                if (stripos($rnm, $q) === false && stripos($rid, $q) === false) continue;
                            }
                            $items[] = [
                                'kd_kel' => $rid,
                                'nm_kel' => $rnm,
                                'kd_kec' => $rkec,
                            ];
                        }
                        usort($items, function($a, $b) { return strcmp((string)$a['nm_kel'], (string)$b['nm_kel']); });
                        $list = array_slice($items, 0, 20);
                    } else {
                        $list = [];
                    }
                }
            }
            return response()->json($list);
        } catch (\Throwable $e) {
            LogFacade::error('Kelurahan search error', ['message' => $e->getMessage()]);
            $q = isset($q) ? $q : trim((string)$request->input('q', ''));
            $kdKec = isset($kdKec) ? $kdKec : $request->input('kd_kec');
            $path = public_path('assets/kelurahan.iyem');
            $list = [];
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (isset($json['kelurahan']) && is_array($json['kelurahan'])) {
                    foreach ($json['kelurahan'] as $row) {
                        $rid = isset($row['id']) ? (string)$row['id'] : '';
                        $rnm = isset($row['nama']) ? (string)$row['nama'] : '';
                        $rkec = isset($row['id_kecamatan']) ? (string)$row['id_kecamatan'] : '';
                        if (!empty($kdKec)) {
                            if ((string)$rkec !== (string)$kdKec) continue;
                        }
                        if ($q !== '') {
                            if (stripos($rnm, $q) === false && stripos($rid, $q) === false) continue;
                        }
                        $list[] = [
                            'kd_kel' => $rid,
                            'nm_kel' => $rnm,
                            'kd_kec' => $rkec,
                        ];
                    }
                    usort($list, function($a, $b) { return strcmp((string)$a['nm_kel'], (string)$b['nm_kel']); });
                    $list = array_slice($list, 0, 20);
                }
            }
            return response()->json($list);
        }
    })->name('wilayah.search.kelurahan')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});

// Resolve hierarki wilayah dari kode yang diisi
Route::get('/wilayah/resolve', function(Illuminate\Http\Request $request) {
    $kdKel = $request->input('kd_kel');
    $kdKec = $request->input('kd_kec');
    $kdKab = $request->input('kd_kab');
    $kdProp = $request->input('kd_prop');
    $result = [];
    if ($kdKel) {
        $kel = DB::table('kelurahan')->where('kd_kel', $kdKel)->first();
        if ($kel) {
            $result['kd_kel'] = isset($kel->kd_kel) ? $kel->kd_kel : (isset($kel->id) ? (string)$kel->id : null);
            $result['nm_kel'] = isset($kel->nm_kel) ? $kel->nm_kel : (isset($kel->nama) ? $kel->nama : null);
            $tmp = isset($kel->kd_kec) ? $kel->kd_kec : (isset($kel->id_kecamatan) ? $kel->id_kecamatan : null);
            if (!empty($tmp)) $kdKec = $tmp;
        } else {
            $path = public_path('assets/kelurahan.iyem');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (isset($json['kelurahan']) && is_array($json['kelurahan'])) {
                    foreach ($json['kelurahan'] as $row) {
                        $rid = isset($row['id']) ? (string)$row['id'] : '';
                        if ($rid === (string)$kdKel) {
                            $result['kd_kel'] = $rid;
                            $result['nm_kel'] = isset($row['nama']) ? (string)$row['nama'] : null;
                            $rkec = isset($row['id_kecamatan']) ? (string)$row['id_kecamatan'] : '';
                            if ($rkec !== '') $kdKec = $rkec;
                            break;
                        }
                    }
                }
            }
        }
    }
    if ($kdKec) {
        $kec = DB::table('kecamatan')->where('kd_kec', $kdKec)->first();
        if ($kec) {
            $result['kd_kec'] = isset($kec->kd_kec) ? $kec->kd_kec : (isset($kec->id) ? (string)$kec->id : null);
            $result['nm_kec'] = isset($kec->nm_kec) ? $kec->nm_kec : (isset($kec->nama) ? $kec->nama : null);
            $tmp = isset($kec->kd_kab) ? $kec->kd_kab : (isset($kec->id_kabupaten) ? $kec->id_kabupaten : null);
            if (!empty($tmp)) $kdKab = $tmp;
        } else {
            $path = public_path('assets/kecamatan.iyem');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (isset($json['kecamatan']) && is_array($json['kecamatan'])) {
                    foreach ($json['kecamatan'] as $row) {
                        $rid = isset($row['id']) ? (string)$row['id'] : '';
                        if ($rid === (string)$kdKec) {
                            $result['kd_kec'] = $rid;
                            $result['nm_kec'] = isset($row['nama']) ? (string)$row['nama'] : null;
                            $rkab = isset($row['id_kabupaten']) ? (string)$row['id_kabupaten'] : '';
                            if ($rkab !== '') $kdKab = $rkab;
                            break;
                        }
                    }
                }
            }
        }
    }
    if ($kdKab) {
        $kab = DB::table('kabupaten')->where('kd_kab', $kdKab)->first();
        if ($kab) {
            $result['kd_kab'] = isset($kab->kd_kab) ? $kab->kd_kab : (isset($kab->id) ? (string)$kab->id : null);
            $result['nm_kab'] = isset($kab->nm_kab) ? $kab->nm_kab : (isset($kab->nama) ? $kab->nama : null);
            $tmp = isset($kab->kd_prop) ? $kab->kd_prop : (isset($kab->id_propinsi) ? $kab->id_propinsi : null);
            if (!empty($tmp)) $kdProp = $tmp;
        } else {
            $path = public_path('assets/kabupaten.iyem');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (isset($json['kabupaten']) && is_array($json['kabupaten'])) {
                    foreach ($json['kabupaten'] as $row) {
                        $rid = isset($row['id']) ? (string)$row['id'] : '';
                        if ($rid === (string)$kdKab) {
                            $result['kd_kab'] = $rid;
                            $result['nm_kab'] = isset($row['nama']) ? (string)$row['nama'] : null;
                            $rprop = isset($row['id_propinsi']) ? (string)$row['id_propinsi'] : '';
                            if ($rprop !== '') $kdProp = $rprop;
                            break;
                        }
                    }
                }
            }
        }
    }
    if ($kdProp) {
        $prop = DB::table('propinsi')->where('kd_prop', $kdProp)->first();
        if ($prop) {
            $result['kd_prop'] = isset($prop->kd_prop) ? $prop->kd_prop : (isset($prop->id) ? (string)$prop->id : null);
            $result['nm_prop'] = isset($prop->nm_prop) ? $prop->nm_prop : (isset($prop->nama) ? $prop->nama : null);
        } else {
            $path = public_path('assets/propinsi.iyem');
            if (file_exists($path)) {
                $content = file_get_contents($path);
                $json = json_decode($content, true);
                if (isset($json['propinsi']) && is_array($json['propinsi'])) {
                    foreach ($json['propinsi'] as $row) {
                        $rid = isset($row['id']) ? (string)$row['id'] : '';
                        if ($rid === (string)$kdProp) {
                            $result['kd_prop'] = $rid;
                            $result['nm_prop'] = isset($row['nama']) ? (string)$row['nama'] : null;
                            break;
                        }
                    }
                }
            }
        }
    }
    return response()->json($result);
})->name('wilayah.resolve')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Search penjawab (penjab) dan perusahaan_pasien
Route::get('/penjab/search', function(Illuminate\Http\Request $request) {
    $q = $request->input('q');
    if (!$q || strlen($q) < 2) return response()->json([]);
    $list = DB::table('penjab')
        ->select('kd_pj', 'png_jawab')
        ->where(function($w) use ($q) {
            $w->where('png_jawab', 'like', '%' . $q . '%')
              ->orWhere('kd_pj', 'like', '%' . $q . '%');
        })
        ->orderBy('png_jawab', 'asc')->limit(20)->get();
    return response()->json($list);
})->name('penjab.search')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

Route::get('/perusahaan/search', function(Illuminate\Http\Request $request) {
    $q = $request->input('q');
    if (!$q || strlen($q) < 2) return response()->json([]);
    $list = DB::table('perusahaan_pasien')
        ->select('kode_perusahaan', 'nama_perusahaan', 'kota')
        ->where(function($w) use ($q) {
            $w->where('nama_perusahaan', 'like', '%' . $q . '%')
              ->orWhere('kode_perusahaan', 'like', '%' . $q . '%');
        })
        ->orderBy('nama_perusahaan', 'asc')->limit(20)->get();
    return response()->json($list);
})->name('perusahaan.search')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Search referensi: suku_bangsa, bahasa_pasien, cacat_fisik
Route::prefix('ref/search')->group(function() {
    Route::get('/suku-bangsa', function(Illuminate\Http\Request $request) {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) return response()->json([]);
        $list = DB::table('suku_bangsa')->select('id', 'nama_suku_bangsa')
            ->where('nama_suku_bangsa', 'like', '%' . $q . '%')
            ->orderBy('nama_suku_bangsa', 'asc')->limit(20)->get();
        return response()->json($list);
    })->name('ref.search.suku-bangsa')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/bahasa-pasien', function(Illuminate\Http\Request $request) {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) return response()->json([]);
        $list = DB::table('bahasa_pasien')->select('id', 'nama_bahasa')
            ->where('nama_bahasa', 'like', '%' . $q . '%')
            ->orderBy('nama_bahasa', 'asc')->limit(20)->get();
        return response()->json($list);
    })->name('ref.search.bahasa-pasien')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::get('/cacat-fisik', function(Illuminate\Http\Request $request) {
        $q = $request->input('q');
        if (!$q || strlen($q) < 2) return response()->json([]);
        $list = DB::table('cacat_fisik')->select('id', 'nama_cacat')
            ->where('nama_cacat', 'like', '%' . $q . '%')
            ->orderBy('nama_cacat', 'asc')->limit(20)->get();
        return response()->json($list);
    })->name('ref.search.cacat-fisik')->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});

// Semua rute API untuk skrining
Route::prefix('api')->group(function() {
    Route::post('/skrining/cek-nik', [App\Http\Controllers\SkriningController::class, 'cekNikSkrining'])
        ->name('api.skrining.cek-nik')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/demografi', [App\Http\Controllers\SkriningController::class, 'simpanDemografi'])
        ->name('api.skrining.demografi')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/tekanan-darah', [App\Http\Controllers\SkriningController::class, 'simpanTekananDarah'])
        ->name('api.skrining.tekanan-darah')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/perilaku-merokok', [App\Http\Controllers\SkriningController::class, 'simpanPerilakuMerokok'])
        ->name('api.skrining.perilaku-merokok')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/kesehatan-jiwa', [App\Http\Controllers\SkriningController::class, 'simpanKesehatanJiwa'])
        ->name('api.skrining.kesehatan-jiwa')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/hati', [App\Http\Controllers\SkriningController::class, 'simpanHati'])
        ->name('api.skrining.hati')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/kanker-leher-rahim', [App\Http\Controllers\SkriningController::class, 'simpanKankerLeherRahim'])
        ->name('api.skrining.kanker-leher-rahim')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/aktivitas-fisik', [App\Http\Controllers\SkriningController::class, 'simpanAktivitasFisik'])
        ->name('api.skrining.aktivitas-fisik')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/tuberkulosis', [App\Http\Controllers\SkriningController::class, 'simpanTuberkulosis'])
        ->name('api.skrining.tuberkulosis')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/antropometri-lab', [App\Http\Controllers\SkriningController::class, 'simpanAntropometriLab'])
        ->name('api.skrining.antropometri-lab')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/skrining-indra', [App\Http\Controllers\SkriningController::class, 'simpanSkriningIndra'])
        ->name('api.skrining.skrining-indra')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/skrining-gigi', [App\Http\Controllers\SkriningController::class, 'simpanSkriningGigi'])
        ->name('api.skrining.skrining-gigi')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/gangguan-fungsional', [App\Http\Controllers\SkriningController::class, 'simpanGangguanFungsional'])
        ->name('api.skrining.gangguan-fungsional')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    Route::post('/skrining/skrining-puma', [App\Http\Controllers\SkriningController::class, 'simpanSkriningPuma'])
        ->name('api.skrining.skrining-puma')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

    Route::post('/skrining/simpan', [App\Http\Controllers\SkriningController::class, 'simpanSkrining'])
        ->name('api.skrining.simpan')
        ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
});

// Route untuk menolak semua rute lainnya
Route::get('/offline', function () {
    return redirect('/');
});

Route::get('/kerjo-award', function () {
    return redirect('/');
});

Route::get('/clear-cache', function() {
    return redirect('/');
});

// Rute API yang tidak memerlukan autentikasi (letakkan sebelum catch-all)
Route::get('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'getDiagnosa'])->name('diagnosa');
Route::post('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'simpanDiagnosa'])->name('diagnosa.simpan');
Route::get('/icd9', [App\Http\Controllers\API\ResumePasienController::class, 'getICD9'])->name('icd9');
Route::get('/pegawai', [App\Http\Controllers\API\PemeriksaanController::class, 'getPegawai'])->name('pegawai');
Route::get('/pegawai/nik', [App\Http\Controllers\API\PemeriksaanController::class, 'getPegawaiNik'])->name('pegawai.nik');
Route::get('/kader', [App\Http\Controllers\API\PemeriksaanController::class, 'getKader'])->name('kader');
Route::get('/kader/list', [App\Http\Controllers\API\PemeriksaanController::class, 'listKader'])->name('kader.list');
Route::post('/kader', [App\Http\Controllers\API\PemeriksaanController::class, 'storeKader'])->name('kader.store');
Route::put('/kader/{id}', [App\Http\Controllers\API\PemeriksaanController::class, 'updateKader'])->name('kader.update');
Route::delete('/kader/{id}', [App\Http\Controllers\API\PemeriksaanController::class, 'deleteKader'])->name('kader.destroy');
Route::get('/api/pasien', [App\Http\Controllers\RegisterController::class, 'getPasien'])->name('get.pasien');
Route::get('/pasien/search', [App\Http\Controllers\PasienController::class, 'searchPasien'])->name('pasien.search');
Route::post('/pasien/store-skrining', [App\Http\Controllers\PasienController::class, 'storeFromSkrining'])->name('pasien.store-skrining');
Route::post('/pasien/update-skrining/{no_rkm_medis}', [App\Http\Controllers\PasienController::class, 'updateFromSkrining'])->name('pasien.update-skrining');
Route::get('/api/dokter', [App\Http\Controllers\RegisterController::class, 'getDokter'])->name('dokter');
Route::get('/propinsi', [WilayahController::class, 'getPropinsi'])->name('propinsi');
Route::get('/kabupaten', [WilayahController::class, 'getKabupaten'])->name('kabupaten');
Route::get('/kecamatan', [WilayahController::class, 'getKecamatan'])->name('kecamatan');
Route::get('/kelurahan', [WilayahController::class, 'getKelurahan'])->name('kelurahan');

// Tangani semua rute lain
Route::any('{any}', function() {
    return redirect('/');
})->where('any', '.*');

// Rute untuk berkas
Route::get('/berkas/{noRawat}/{noRM}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRM'])->where('noRawat', '.*');
Route::get('/berkas-retensi/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRetensi']);

// Rute yang memerlukan autentikasi
Route::middleware(['web', 'loginauth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Route untuk skrining CKG
    Route::get('/skrining-ckg', function() {
        return view('skrining-ckg');
    })->name('skrining.ckg');
    
    // Route untuk form skrining sederhana
    Route::get('/skrining-sederhana', function() {
        return view('form-skrining-sederhana');
    })->name('skrining.sederhana');
    
    // Route untuk data pasien
    Route::prefix('data-pasien')->group(function () {
        Route::get('/', [App\Http\Controllers\PasienController::class, 'index'])->name('pasien.index');
        Route::get('/create', [App\Http\Controllers\PasienController::class, 'create'])->name('pasien.create');
        Route::post('/simpan', [App\Http\Controllers\PasienController::class, 'simpan'])->name('pasien.simpan');
        Route::get('/{no_rkm_medis}/edit', [App\Http\Controllers\PasienController::class, 'edit'])->name('pasien.edit');
        Route::put('/{no_rkm_medis}', [App\Http\Controllers\PasienController::class, 'update'])->name('pasien.update');
        Route::get('/export', [App\Http\Controllers\PasienController::class, 'export'])->name('pasien.export');
        Route::get('/cetak', [App\Http\Controllers\PasienController::class, 'cetak'])->name('pasien.cetak');
    });
    
    // Route untuk detail pasien (diluar prefix data-pasien agar tidak bentrok)
    Route::get('/pasien/{no_rkm_medis}', [App\Http\Controllers\PasienController::class, 'show'])->name('pasien.show');
    
    // Route untuk register
    Route::get('/register', [App\Http\Controllers\RegisterController::class, 'index'])->name('register');
    
    // Route untuk regperiksa
    Route::prefix('regperiksa')->group(function () {
        Route::get('/create/{no_rkm_medis}', [App\Http\Controllers\RegPeriksaController::class, 'create'])->name('regperiksa.create');
        Route::post('/store', [App\Http\Controllers\RegPeriksaController::class, 'store'])->name('regperiksa.store');
        Route::get('/generate-noreg/{kd_dokter}/{tgl_registrasi}', [App\Http\Controllers\RegPeriksaController::class, 'generateNoReg'])->name('regperiksa.generate-noreg');
    });
    
    // Route untuk diagnostik
    Route::get('/diagnostic', [App\Http\Controllers\DiagnosticController::class, 'index'])->name('diagnostic');
    
    // Route untuk master obat
    Route::get('/master_obat', [App\Http\Controllers\MasterObat::class, 'index'])->name('master_obat');
    
    // Route menu booking
    Route::get('/booking', [App\Http\Controllers\BookingController::class, 'index'])->name('booking');
    
    // KYC Routes
    Route::prefix('kyc')->group(function () {
        Route::get('/', [App\Http\Controllers\KYCController::class, 'index'])->name('kyc.index');
        Route::post('/process', [App\Http\Controllers\KYCController::class, 'processVerification'])->name('kyc.process');
        Route::get('/status', [App\Http\Controllers\KYCController::class, 'status'])->name('kyc.status');
        Route::get('/config', [App\Http\Controllers\KYCController::class, 'config'])->name('kyc.config');
        Route::get('/test-token', [App\Http\Controllers\KYCController::class, 'testToken'])->name('kyc.new.test-token');
        Route::get('/search-patient', [App\Http\Controllers\KYCController::class, 'searchPatient'])->name('kyc.search-patient');
    });
    
    // Mobile JKN Routes
    Route::prefix('pendaftaran-mobile-jkn')->name('mobile-jkn.')->group(function () {
        Route::get('/', [App\Http\Controllers\MobileJknController::class, 'index'])->name('index');
        Route::get('/get-peserta', [App\Http\Controllers\MobileJknController::class, 'getPeserta'])->name('get-peserta');
        Route::get('/get-poli', [App\Http\Controllers\MobileJknController::class, 'getPoli'])->name('get-poli');
        Route::get('/get-dokter', [App\Http\Controllers\MobileJknController::class, 'getDokter'])->name('get-dokter');
        Route::get('/get-sisa-antrean', [App\Http\Controllers\MobileJknController::class, 'getSisaAntrean'])->name('get-sisa-antrean');
        Route::get('/status-antrean', [App\Http\Controllers\MobileJknController::class, 'statusAntrean'])->name('get-status-antrean');
        Route::post('/daftar-antrean', [App\Http\Controllers\MobileJknController::class, 'daftarAntrean'])->name('daftar-antrean');
        Route::post('/batal-antrean', [App\Http\Controllers\MobileJknController::class, 'batalAntrean'])->name('batal-antrean');
    });
    
    // Route Menu Ralan
    Route::prefix('ralan')->group(function () {
        Route::get('/pasien', [App\Http\Controllers\Ralan\PasienRalanController::class, 'index'])->name('ralan.pasien');
        Route::get('/refresh-data', [App\Http\Controllers\Ralan\PasienRalanController::class, 'getDataForRefresh'])->name('ralan.refresh-data');
        Route::get('/listen-new-patients', [App\Http\Controllers\Ralan\PasienRalanController::class, 'listenForNewPatients'])->name('ralan.listen-new-patients');
        Route::get('/pemeriksaan', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'index'])->name('ralan.pemeriksaan');
        Route::get('/rujuk-internal', [App\Http\Controllers\Ralan\RujukInternalPasien::class, 'index'])->name('ralan.rujuk-internal');
        Route::get('/obat', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getObat'])->name('ralan.obat');
        Route::post('/simpan/resep/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResep'])->name('ralan.simpan.resep');
        Route::post('/simpan/racikan/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResepRacikan'])->name('ralan.simpan.racikan');
        Route::post('/simpan/copyresep/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postCopyResep'])->name('ralan.simpan.copyresep');
        Route::post('/simpan/resumemedis/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResumMedis']);
        Route::delete('/obat/{noResep}/{kdObat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'hapusObat']);
        Route::delete('/racikan/{noResep}/{noRacik}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'hapusObatRacikan']);
        Route::get('/copy/{noResep}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getCopyResep']);
        Route::post('/pemeriksaan/submit', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postPemeriksaan'])->name('ralan.pemeriksaan.submit');
        Route::post('/catatan/submit', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postCatatan'])->name('ralan.catatan.submit');
        Route::get('/poli', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getPoli']);
        Route::get('/dokter/{kdPoli}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getDokter']);
        Route::post('/rujuk-internal/submit', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postRujukan']);
        Route::delete('/rujuk-internal/delete/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'deleteRujukan']);
        Route::put('/rujuk-internal/update/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'updateRujukanInternal'])->name('ralan.rujuk-internal.update');
        Route::post('/panggil-pasien', [App\Http\Controllers\Ralan\PasienRalanController::class, 'panggilPasien'])->name('ralan.panggil-pasien');
    });
    
    // Route Menu Ranap
    Route::prefix('ranap')->group(function () {
        Route::get('/pasien', [App\Http\Controllers\Ranap\PasienRanapController::class, 'index'])->name('ranap.pasien');
        Route::get('/pemeriksaan', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'index'])->name('ranap.pemeriksaan');
        Route::post('/pemeriksaan/submit', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'postPemeriksaan'])->name('ranap.pemeriksaan.submit');
        Route::get('/copy/{noResep}', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'getCopyResep']);
        Route::get('/pemeriksaan/{noRawat}/{tgl}/{jam}', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'getPemeriksaan']);
        Route::post('/pemeriksaan/edit/{noRawat}/{tgl}/{jam}', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'editPemeriksaan']);
        Route::get('/obat', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getObat'])->name('ranap.obat');
        Route::post('/simpan/resep/{noRawat}', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'postResep'])->name('ranap.simpan.resep');
        Route::delete('/obat/{noResep}/{kdObat}', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'hapusObat']);
    });
    
    // Route untuk Partograf
    Route::get('/partograf-klasik/{id_hamil}', [App\Http\Controllers\PartografController::class, 'showKlasik'])->name('partograf.klasik');
    
    // Route menu ILP
    Route::prefix('ilp')->name('ilp.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ILP\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [App\Http\Controllers\ILP\DashboardController::class, 'index'])->name('dashboard.data');
        Route::get('/pendaftaran', [App\Http\Controllers\ILP\PendaftaranController::class, 'index'])->name('pendaftaran');
        Route::get('/pelayanan', [App\Http\Controllers\ILP\PelayananController::class, 'index'])->name('pelayanan');
        Route::put('/update/{id}', [App\Http\Controllers\ILP\PelayananController::class, 'update'])->name('update');
        Route::get('/cetak/{id}', [App\Http\Controllers\ILP\PelayananController::class, 'cetakPdf'])->name('cetak');
        Route::post('/get-summary', [App\Http\Controllers\IlpController::class, 'getSummary'])->name('get-summary');
        Route::post('/send-pdf', [App\Http\Controllers\IlpController::class, 'sendPdf'])->name('send-pdf');
        Route::post('/send-whatsapp', [App\Http\Controllers\IlpController::class, 'sendWhatsApp'])->name('send-whatsapp');
        Route::get('/faktor-resiko', [App\Http\Controllers\ILP\FaktorResikoController::class, 'index'])->name('faktor-resiko');
        Route::get('/get-posyandu', [App\Http\Controllers\ILP\FaktorResikoController::class, 'getPosyandu'])->name('get-posyandu');
        
        // Route untuk Sasaran CKG
        Route::get('/sasaran-ckg', [App\Http\Controllers\ILP\SasaranCKGController::class, 'index'])->name('sasaran-ckg');
        Route::get('/sasaran-ckg/detail/{noRekamMedis}', [App\Http\Controllers\ILP\SasaranCKGController::class, 'detail'])->name('sasaran-ckg.detail');
        Route::get('/sasaran-ckg/kirim-wa/{noRekamMedis}', [App\Http\Controllers\ILP\SasaranCKGController::class, 'kirimWA'])->name('sasaran-ckg.kirim-wa');
        
        // Route untuk ILP Dewasa - dengan penanganan URL yang di-encode
        Route::get('/dewasa/{noRawat}', [App\Http\Controllers\ILP\IlpDewasaController::class, 'index'])
            ->name('dewasa.form')
            ->where('noRawat', '.*');
        
        Route::post('/dewasa', [App\Http\Controllers\ILP\IlpDewasaController::class, 'store'])->name('dewasa.store');
        Route::delete('/dewasa/{noRawat}', [App\Http\Controllers\ILP\IlpDewasaController::class, 'destroy'])
            ->name('dewasa.destroy')
            ->where('noRawat', '.*');
    });

    // Route untuk refresh CSRF token
    Route::get('/refresh-csrf', function() {
        return csrf_token();
    });
    
    // Route untuk Livewire generateNoReg
    Route::post('/livewire/generate-noreg', function(Illuminate\Http\Request $request) {
        $formPendaftaran = new App\Http\Livewire\Registrasi\FormPendaftaran();
        $formPendaftaran->dokter = $request->input('dokter');
        $formPendaftaran->kd_poli = $request->input('kd_poli');
        $formPendaftaran->tgl_registrasi = $request->input('tgl_registrasi');
        
        try {
            $no_reg = $formPendaftaran->generateNoReg();
            return response()->json([
                'success' => true,
                'no_reg' => $no_reg,
                'message' => 'Nomor registrasi berhasil dibuat'
            ]);
        } catch (\Exception $e) {
            LogFacade::error("Error generateNoReg via Livewire: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    })->name('livewire.generate-noreg');

    // Route untuk PCare BPJS
    Route::prefix('pcare')->group(function () {
        Route::get('/form-pendaftaran', function (Illuminate\Http\Request $request) {
            $no_rkm_medis = $request->input('no_rkm_medis');
            return view('Pcare.form-pendaftaran', compact('no_rkm_medis'));
        })->name('pcare.form-pendaftaran');
        
        Route::get('/pendaftaran', function () {
            return Redirect::to('/')->with('info', 'Halaman Pendaftaran PCare sedang dalam pengembangan');
        })->name('pcare.pendaftaran');
        
        Route::get('/data-pendaftaran', function () {
            return view('Pcare.data-pendaftaran-pcare');
        })->name('pcare.data-pendaftaran');
    });

    // Antrian Poliklinik Routes
    Route::get('/antrian-poliklinik', [App\Http\Controllers\AntrianPoliklinikController::class, 'index'])
        ->name('antrian-poliklinik.index');
    Route::get('/antrian-display', [App\Http\Controllers\AntrianDisplayController::class, 'display'])
        ->name('antrian.display');
    Route::get('/antrian/display/data', [App\Http\Controllers\AntrianDisplayController::class, 'getDataDisplay'])->name('antrian.display.data');
    Route::get('/laporan/antrian-poliklinik', [App\Http\Controllers\AntrianPoliklinikController::class, 'cetakLaporan'])
        ->name('antrian-poliklinik.cetak');
    Route::get('/laporan/antrian-poliklinik/export', [App\Http\Controllers\AntrianPoliklinikController::class, 'exportExcel'])
        ->name('antrian-poliklinik.export');

    Route::get('/get-videos', [App\Http\Controllers\VideoController::class, 'getVideos']);
});

// Temporary route for debugging
Route::get('/debug/permintaan-lab', function() {
    $data = DB::table('permintaan_lab')
            ->where('noorder', 'PL202503180001')
            ->orWhere('no_rawat', '2025/03/18/000001')
            ->get();
    
    $pemeriksaan = DB::table('permintaan_pemeriksaan_lab AS ppl')
            ->join('jns_perawatan_lab AS jpl', 'ppl.kd_jenis_prw', '=', 'jpl.kd_jenis_prw')
            ->where('ppl.noorder', 'PL202503180001')
            ->select('ppl.kd_jenis_prw', 'jpl.nm_perawatan')
            ->get();
            
    return [
        'permintaan_lab' => $data,
        'detail_pemeriksaan' => $pemeriksaan
    ];
});

// Rute pengujian untuk memeriksa nomor registrasi
Route::get('/test-noreg', [App\Http\Controllers\RegPeriksaController::class, 'testNoReg']);

// Rute pengujian tanpa autentikasi
Route::get('/test-noreg-public', [App\Http\Controllers\RegPeriksaController::class, 'testNoRegPublic'])->withoutMiddleware(['loginauth']);

// Rute pengujian dokter spesifik
Route::get('/test-dokter-noreg-public/{kd_dokter?}', [App\Http\Controllers\RegPeriksaController::class, 'testDokterNoRegPublic'])->withoutMiddleware(['loginauth']);

// Route untuk debugging API
Route::any('/api/skrining/debug', [App\Http\Controllers\SkriningController::class, 'debug'])
    ->name('api.skrining.debug')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
