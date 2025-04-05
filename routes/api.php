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
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\AntrianPoliklinikController;

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
Route::get('/ranap/{bangsal}/obat', [App\Http\Controllers\Api\ResepRanapController::class, 'getObatRanap']);
// Tambahkan rute untuk obat ralan
Route::get('/ralan/{poli}/obat', [ResepController::class, 'getObatRalan']);
Route::get('/obat-luar', [ResepController::class, 'getObatLuar']);
Route::get('/obat/{kdObat}', [ObatController::class, 'getObat']);
Route::post('/cari-kode-obat', [ObatController::class, 'cariKodeObat']);

// Rute untuk resep
Route::post('/resep/{noRawat}', [ResepController::class, 'postResep']);
Route::post('/resep/racikan/{noRawat}', [ResepController::class, 'postResepRacikan']);
Route::delete('/resep/{noResep}/{kdObat}/{noRawat}', [ResepController::class, 'hapusObat']);
Route::post('/obat-batch', [ResepController::class, 'hapusObatBatch']);

// Rute untuk resep ranap
Route::post('/resep_ranap/{noRawat}', [ResepRanapController::class, 'postResepRanap']);
Route::post('/obat/{noResep}/{kdObat}', [ObatController::class, 'hapusObat']);
Route::post('/resep/hapus-racikan', [ResepRanapController::class, 'hapusRacikan']);
Route::post('/ranap/resep/racikan/{noRawat}', [ResepRanapController::class, 'postResepRacikanRanap']);
Route::get('/ranap/riwayat-peresepan/{noRawat}', [ResepRanapController::class, 'getRiwayatPeresepan']);

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
Route::get('/get-permintaan-lab/{noRawat}', [LabController::class, 'getPermintaanLabData']);
Route::get('/get-detail-pemeriksaan/{noOrder}', [LabController::class, 'getDetailPemeriksaan']);

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

// BPJS Routes
Route::prefix('bpjs')->group(function () {
    Route::get('peserta/{noKartu}', [App\Http\Controllers\API\BPJSController::class, 'getPeserta']);
});

// PCare Routes
Route::prefix('pcare')->group(function () {
    // Peserta - format endpoint sesuai dengan library awageeks/laravel-bpjs
    Route::get('peserta/noka/{noKartu}', [App\Http\Controllers\API\PcareController::class, 'getPeserta']);
    Route::get('peserta/nik/{nik}', [App\Http\Controllers\API\PcareController::class, 'getPesertaByNIK']);
    Route::get('peserta/{noKartu}', [App\Http\Controllers\API\PcareController::class, 'getPeserta']);
    
    // Provider dan Dokter
    Route::get('provider', [App\Http\Controllers\API\PcareController::class, 'getProvider']);
    Route::get('dokter', [App\Http\Controllers\API\PcareController::class, 'getDokter']);
    
    // Diagnosa, Tindakan, dan Obat
    Route::get('diagnosa/{keyword}', [App\Http\Controllers\API\PcareController::class, 'getDiagnosa']);
    Route::get('tindakan/{keyword}', [App\Http\Controllers\API\PcareController::class, 'getTindakan']);
    Route::get('obat/{keyword}', [App\Http\Controllers\API\PcareController::class, 'getObat']);
    
    // Kunjungan dan Status
    Route::get('kunjungan/{noKartu}', [App\Http\Controllers\API\PcareController::class, 'getKunjungan']);
    Route::get('statuspulang', [App\Http\Controllers\API\PcareController::class, 'getStatusPulang']);
    
    // Pendaftaran Kunjungan Sehat
    Route::post('pendaftaran', [App\Http\Controllers\API\PcareController::class, 'addPendaftaran']);
    
    // Poli dan Klub
    Route::get('poli', [App\Http\Controllers\API\PcareController::class, 'getPoli']);
    Route::get('kelompok', [App\Http\Controllers\API\PcareController::class, 'getKelompokSehat']);
    Route::get('klubprolanis', [App\Http\Controllers\API\PcareController::class, 'getKlubProlanis']);
});

// ICare Routes
Route::prefix('icare')->group(function () {
    // Peserta
    Route::get('peserta/{noKartu}', [App\Http\Controllers\API\IcareController::class, 'getPeserta']);
    Route::get('peserta/nik/{nik}', [App\Http\Controllers\API\IcareController::class, 'getPesertaByNIK']);
    Route::get('peserta/{noKartu}/riwayat', [App\Http\Controllers\API\IcareController::class, 'getRiwayatPeserta']);
    Route::post('validate', [App\Http\Controllers\API\IcareController::class, 'validateIcare']);
});

// Endpoint untuk tes getObatRanap
Route::get('/test/obat-ranap/{bangsal}', [App\Http\Controllers\Api\ResepRanapController::class, 'getObatRanap']);

// Routes untuk data wilayah dari file iyem
Route::get('/propinsi', [WilayahController::class, 'getPropinsi']);
Route::get('/kabupaten', [WilayahController::class, 'getKabupaten']);
Route::get('/kecamatan', [WilayahController::class, 'getKecamatan']);
Route::get('/kelurahan', [WilayahController::class, 'getKelurahan']);

// Route untuk mendapatkan data posyandu berdasarkan desa/kelurahan
Route::middleware('web')->get('/data-posyandu', [\App\Http\Controllers\ILP\FaktorResikoController::class, 'getPosyandu']);

// Antrian Poliklinik API Routes
Route::get('/antrian-poliklinik', [AntrianPoliklinikController::class, 'getAntrianPoliklinik']);
Route::get('/antrian-display', [AntrianPoliklinikController::class, 'getAntrianDisplay']);
Route::get('/poliklinik', [AntrianPoliklinikController::class, 'getPoliklinik']);
Route::get('/pasien/detail/{noRawat}', [AntrianPoliklinikController::class, 'getDetailPasien']);
Route::post('/antrian/panggil', [AntrianPoliklinikController::class, 'panggilPasien']);
Route::get('/media-files', [AntrianPoliklinikController::class, 'getMediaFiles'])->withoutMiddleware(['auth:sanctum']);

// MobileJKN BPJS Routes
Route::prefix('wsbpjs')->group(function () {
    Route::get('referensi/poli/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiPoli']);
    Route::get('referensi/dokter/kodepoli/{kodePoli}/tanggal/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiDokter']);
    Route::post('antrean/add', [App\Http\Controllers\API\WsBPJSController::class, 'tambahAntrean']);
    Route::post('antrean/create', [App\Http\Controllers\API\WsBPJSController::class, 'buatAntreanDariDB']);
    Route::get('antrean/status/kodepoli/{kodePoli}/tanggalperiksa/{tanggalPeriksa}', [App\Http\Controllers\API\WsBPJSController::class, 'cekStatusAntrean']);
    Route::post('antrean/panggil', [App\Http\Controllers\API\WsBPJSController::class, 'updateStatusAntrean']);
    Route::post('antrean/update-status', [App\Http\Controllers\API\WsBPJSController::class, 'updateStatusAntreanDariDB']);
    Route::post('antrean/batal', [App\Http\Controllers\API\WsBPJSController::class, 'batalAntrean']);
    Route::post('antrean/batal-dari-db', [App\Http\Controllers\API\WsBPJSController::class, 'batalAntreanDariDB']);
    Route::get('timestamp', [App\Http\Controllers\API\WsBPJSController::class, 'getTimestamp']);
});

// BPJS FKTP Routes
Route::prefix('fktp')->group(function () {
    Route::get('auth', [App\Http\Controllers\API\WsFKTPController::class, 'getToken']);
    Route::get('antrean/sisapeserta/{nomorKartu}/{kodePoli}/{tanggalPeriksa}', [App\Http\Controllers\API\WsFKTPController::class, 'getSisaAntrean']);
    Route::post('peserta', [App\Http\Controllers\API\WsFKTPController::class, 'registrasiPasienBaru']);
    Route::post('antrean', [App\Http\Controllers\API\WsFKTPController::class, 'ambilAntrean']);
    Route::get('antrean/status/{kodePoli}/{tanggalPeriksa}', [App\Http\Controllers\API\WsFKTPController::class, 'getStatusAntrean']);
    Route::put('antrean/batal', [App\Http\Controllers\API\WsFKTPController::class, 'batalAntrean']);
});

// Route API untuk Mobile JKN yang dapat diakses tanpa autentikasi
Route::prefix('mobile-jkn')->group(function () {
    Route::get('/referensi-poli/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiPoli']);
    Route::get('/referensi-dokter/kodepoli/{kodePoli}/tanggal/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiDokter']);
});
