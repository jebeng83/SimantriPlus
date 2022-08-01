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
Route::post('/ralan/simpan/resep', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getObat'])->name('ralan.simpan.resep');
Route::post('/ralan/pemeriksaan/submit', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'postPemeriksaan'])->name('ralan.pemeriksaan.submit');
Route::get('/login', [App\Http\Controllers\LoginController::class, 'index'])->name('login');

