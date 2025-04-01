<?php

use App\Http\Controllers\ANC\DataIbuHamilController;
use App\Http\Controllers\ANC\DataBalitaSakitController;
use App\Http\Controllers\ANC\DataRematriController;
use App\Http\Controllers\ANC\DataIbuNifasController;
use App\Http\Controllers\ANC\PartografController;

Route::prefix('anc')->name('anc.')->group(function () {
    // Data Ibu Hamil Routes
    Route::resource('data-ibu-hamil', DataIbuHamilController::class);
    Route::get('data-ibu-hamil/get-data-pasien/{nik}', [DataIbuHamilController::class, 'getDataPasien'])->name('data-ibu-hamil.get-data-pasien');
    Route::get('data-ibu-hamil/{id}/edit', [DataIbuHamilController::class, 'edit'])->name('data-ibu-hamil.edit');
    Route::get('data-ibu-hamil/{id}/detail', [DataIbuHamilController::class, 'detail'])->name('data-ibu-hamil.detail');
    
    // Partograf Routes
    Route::resource('partograf', PartografController::class);
    Route::get('partograf/pasien/{id_hamil}', [PartografController::class, 'showByIdHamil'])->name('partograf.by-id-hamil');
    Route::get('partograf/export/{id}', [PartografController::class, 'exportPdf'])->name('partograf.export');
    
    // Data Balita Sakit Routes
    Route::resource('data-balita-sakit', DataBalitaSakitController::class);
    Route::get('data-balita-sakit/get-data-pasien/{nik}', [DataBalitaSakitController::class, 'getDataPasien'])->name('data-balita-sakit.get-data-pasien');
    
    // Data Rematri Routes
    Route::resource('data-rematri', DataRematriController::class);
    Route::get('data-rematri/get-data-pasien/{nik}', [DataRematriController::class, 'getDataPasien'])->name('data-rematri.get-data-pasien');
    
    // Data Ibu Nifas Routes
    Route::resource('data-ibu-nifas', DataIbuNifasController::class);
    Route::get('data-ibu-nifas/get-data-pasien/{nik}', [DataIbuNifasController::class, 'getDataPasien'])->name('data-ibu-nifas.get-data-pasien');
});
