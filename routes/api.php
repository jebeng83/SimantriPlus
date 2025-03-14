<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ResepController;
use App\Http\Controllers\API\LabController;
use App\Http\Controllers\API\PemeriksaanController;
use App\Http\Controllers\API\RadiologiController;
use App\Http\Controllers\API\ResumePasienController;
use App\Http\Controllers\API\RiwayatController;
use App\Http\Controllers\API\ObatController;
use App\Http\Controllers\API\ResepRanapController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rute untuk obat ranap
Route::get('/ranap/{bangsal}/obat', [ResepController::class, 'getObatRanap']);
Route::get('/obat/{kdObat}', [ObatController::class, 'getObat']);

// Rute untuk resep ranap
Route::post('/resep_ranap/{noRawat}', [ResepRanapController::class, 'postResepRanap']);
Route::post('/obat/{noResep}/{kdObat}', [ObatController::class, 'hapusObat']);
Route::post('/resep/hapus-racikan', [ResepRanapController::class, 'hapusRacikan']);
Route::post('/ranap/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikanRanap']);

// Rute untuk copy resep
Route::get('/ranap/resep/copy/{noResep}', [ResepController::class, 'getDetailResep']);
Route::get('/ranap/resep-copy/{noResep}', [ResepRanapController::class, 'getCopyResep']);

// Lab
Route::get('/hasil/lab/{noRawat}', [LabController::class, 'getPemeriksaanLab']);
Route::post('/permintaan-lab/{noRawat}', [LabController::class, 'postPermintaanLab']);
Route::post('/hapus/permintaan-lab/{noOrder}', [LabController::class, 'hapusPermintaanLab']);
Route::get('/template-lab/{kd_jenis_prw}', [LabController::class, 'getTemplateByJenisPemeriksaan']);
Route::post('/template-lab', [LabController::class, 'getTemplateByMultipleJenisPemeriksaan']);
Route::get('/template-lab', [LabController::class, 'getTemplateByMultipleJenisPemeriksaan']);
Route::get('/template-lab/check', [LabController::class, 'checkTemplateExistence']);
Route::post('/template-lab/create-dummy', [LabController::class, 'createDummyTemplates']);
Route::get('/jns_perawatan_lab', [LabController::class, 'getPerawatanLab']);

// Radiologi
Route::get('/hasil/rad/{noRawat}', [RadiologiController::class, 'getPermintaanRadiologi']);
Route::post('/permintaanrad/{noRawat}', [RadiologiController::class, 'postPermintaanRadiologi']);
Route::post('/hapus/permintaanrad/{noOrder}', [RadiologiController::class, 'hapusPermintaanRadiologi']);
Route::get('/jns_perawatan_rad', [RadiologiController::class, 'getPerawatanRadiologi']);

// Resume Pasien
Route::post('/resumemedis/{noRawat}', [ResumePasienController::class, 'postResume']);
Route::get('/hasil/kel/{noRawat}', [ResumePasienController::class, 'getKeluhanUtama']);

// Riwayat
Route::get('/riwayat_pemeriksaan', [RiwayatController::class, 'getRiwayatPemeriksaan']);
Route::get('/pemeriksaan', [RiwayatController::class, 'getPemeriksaan']);
Route::get('/pemeriksaan/{noRawat}', [PemeriksaanController::class, 'getPemeriksaan']);
Route::get('/riwayat-pemeriksaan/{noRawat}', [PemeriksaanController::class, 'getRiwayatPemeriksaan']);

// BPJS
Route::post('/icare', [App\Http\Controllers\API\BPJSController::class, 'icare']);
