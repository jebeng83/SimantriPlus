<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::post('/customlogin', [App\Http\Controllers\LoginController::class, 'customLogin'])->name('customlogin');
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/ralan/pasien', [App\Http\Controllers\Ralan\PasienRalanController::class, 'index'])->name('ralan.pasien');
Route::get('/ralan/pemeriksaan', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'index'])->name('ralan.pemeriksaan');
Route::get('/ralan/obat', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getObat'])->name('ralan.obat');
Route::post('/ralan/simpan/resep/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResep'])->name('ralan.simpan.resep');
Route::post('/ralan/simpan/racikan/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResepRacikan'])->name('ralan.simpan.racikan');
Route::post('/ralan/simpan/copyresep/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postCopyResep'])->name('ralan.simpan.copyresep');
Route::post('/ralan/simpan/resumemedis/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postResumMedis']);
Route::delete('/ralan/obat/{noResep}/{kdObat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'hapusObat']);
Route::delete('/ralan/racikan/{noResep}/{noRacik}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'hapusObatRacikan']);
Route::get('/ralan/copy/{noResep}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getCopyResep']);
Route::post('/ralan/pemeriksaan/submit', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postPemeriksaan'])->name('ralan.pemeriksaan.submit');


Route::get('/ranap/pasien', [App\Http\Controllers\Ranap\PasienRanapController::class, 'index'])->name('ranap.pasien');
Route::get('/ranap/pemeriksaan', [App\Http\Controllers\Ranap\PemeriksaanRanapController::class, 'index'])->name('ranap.pemeriksaan');
Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login');

