<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\ResepController;
use App\Http\Controllers\API\LabController;

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

Route::get('/obat/{kdObat}', [ResepController::class, 'getDataObat']);
Route::get('/jns_perawatan_lab', [LabController::class, 'getPerawatanLab']);
Route::post('/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikan']);
Route::post('ranap/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikanRanap']);


