<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ResepController;
use App\Http\Controllers\API\LabController;
use App\Http\Controllers\API\PemeriksaanController;
use App\Http\Controllers\API\RadiologiController;
use App\Http\Controllers\API\ResumePasienController;
use App\Http\Controllers\API\RiwayatController;

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

Route::get('/ranap/{bangsal}/obat', [ResepController::class, 'getObatRanap']);
Route::get('/ralan/{poli}/obat', [ResepController::class, 'getObatRalan']);
Route::post('/resep/{noRawat}', [ResepController::class, 'postResep']);
Route::post('/resep_ranap/{noRawat}', [ResepController::class, 'postResepRanap']);
Route::post('/obat/{noResep}/{kdObat}/{noRawat}', [ResepController::class, 'hapusObat']);

Route::get('/hasil/lab/{noRawat}', [LabController::class, 'getPemeriksaanLab']);
Route::post('/permintaanlab/{noRawat}', [LabController::class, 'postPermintaanLab']);
Route::post('/hapus/permintaanlab/{noOrder}', [LabController::class, 'hapusPermintaanLab']);

Route::get('/hasil/rad/{noRawat}', [RadiologiController::class, 'getPermintaanRadiologi']);
Route::post('/permintaanrad/{noRawat}', [RadiologiController::class, 'postPermintaanRadiologi']);
Route::post('/hapus/permintaanrad/{noOrder}', [RadiologiController::class, 'hapusPermintaanRadiologi']);
Route::get('/jns_perawatan_rad', [RadiologiController::class, 'getPerawatanRadiologi']);

Route::post('/resumemedis/{noRawat}', [ResumePasienController::class, 'postResume']);
Route::get('/hasil/kel/{noRawat}', [ResumePasienController::class, 'getKeluhanUtama']);

Route::get('/obat/{kdObat}', [ResepController::class, 'getDataObat']);
Route::get('/jns_perawatan_lab', [LabController::class, 'getPerawatanLab']);
Route::post('/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikan']);
Route::post('ranap/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikanRanap']);

Route::get('/riwayat_pemeriksaan', [RiwayatController::class, 'getRiwayatPemeriksaan']);
Route::get('/pemeriksaan', [RiwayatController::class, 'getPemeriksaan']);

Route::get('/pemeriksaan/{noRawat}', [PemeriksaanController::class, 'getPemeriksaan']);
