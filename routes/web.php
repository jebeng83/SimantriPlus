<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\BPJSTestController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RegPeriksaController;
use App\Http\Controllers\WilayahController;
use App\Http\Controllers\AntrianPoliklinikController;
use App\Http\Controllers\AntrianDisplayController;
use App\Http\Controllers\MobileJknController;
use App\Http\Controllers\SkriningController;
use App\Http\Controllers\PcareKunjunganController;
use App\Http\Controllers\API\PcarePendaftaranController;

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

// Rute yang tidak memerlukan autentikasi
Route::get('/', [App\Http\Controllers\LoginController::class, 'index'])->name('login');
Route::post('/customlogin', [App\Http\Controllers\LoginController::class, 'customLogin'])->name('customlogin');
Route::get('/logout', [App\Http\Controllers\HomeController::class, 'logout'])->name('logout');

// Error page routes
Route::get('/error', [App\Http\Controllers\ErrorController::class, 'index'])->name('error.500');
Route::get('/not-found', [App\Http\Controllers\ErrorController::class, 'notFound'])->name('error.404');
Route::get('/forbidden', [App\Http\Controllers\ErrorController::class, 'forbidden'])->name('error.403');

Route::get('/infokesehatan', function () {
    return redirect('https://ayosehat.kemkes.go.id/promosi-kesehatan');
});

Route::get('/skriningbpjs', function () {
    return redirect('https://webskrining.bpjs-kesehatan.go.id/skrining');
});

// Route untuk form skrining minimal tanpa autentikasi
Route::get('/skrining', [App\Http\Controllers\SkriningController::class, 'index'])->name('skrining.minimal');

// Route untuk menyimpan data skrining tanpa autentikasi
Route::post('/skrining/store', [\App\Http\Controllers\SkriningController::class, 'store'])->name('skrining.store');

// Route untuk mendapatkan data pasien berdasarkan NIK
Route::get('/pasien/get-by-nik', function(\Illuminate\Http\Request $request) {
    $nik = $request->input('nik');
    
    if (empty($nik)) {
        return response()->json([
            'status' => 'error',
            'message' => 'NIK tidak boleh kosong'
        ]);
    }
    
    $pasien = DB::table('pasien')->where('no_ktp', $nik)->first();
    
    if ($pasien !== null) {
        return response()->json([
            'status' => 'success',
            'data' => $pasien
        ]);
    }
    
    return response()->json([
        'status' => 'error',
        'message' => 'Pasien tidak ditemukan'
    ]);
})->name('pasien.get-by-nik');

Route::get('/offline', function () {
    return view('modules/laravelpwa/offline');
});

Route::get('/kerjo-award', function () {
    return view('kerjo_award');
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('optimize:clear');
    return $exitCode;
});

// Route untuk refresh CSRF token (Heartbeat) agar session tidak expire
Route::get('/refresh-csrf', function() {
    return csrf_token();
});

// Rute API yang tidak memerlukan autentikasi
Route::get('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'getDiagnosa'])->name('diagnosa');
Route::post('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'simpanDiagnosa'])->name('diagnosa.simpan');
Route::get('/icd9', [App\Http\Controllers\API\ResumePasienController::class, 'getICD9'])->name('icd9');
Route::get('/pegawai', [App\Http\Controllers\API\PemeriksaanController::class, 'getPegawai'])->name('pegawai');
Route::get('/api/pasien', [App\Http\Controllers\RegisterController::class, 'getPasien'])->name('get.pasien');
Route::get('/pasien/search', [App\Http\Controllers\PasienController::class, 'searchPasien'])->name('pasien.search');
Route::get('/api/dokter', [App\Http\Controllers\RegisterController::class, 'getDokter'])->name('dokter');
Route::get('/propinsi', [\App\Http\Controllers\WilayahController::class, 'getPropinsi'])->name('propinsi');
Route::get('/kabupaten', [\App\Http\Controllers\WilayahController::class, 'getKabupaten'])->name('kabupaten');
Route::get('/kecamatan', [\App\Http\Controllers\WilayahController::class, 'getKecamatan'])->name('kecamatan');
Route::get('/kelurahan', [\App\Http\Controllers\WilayahController::class, 'getKelurahan'])->name('kelurahan');

// Rute untuk berkas
Route::get('/berkas/{noRawat}/{noRM}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRM'])->where('noRawat', '.*');
Route::get('/berkas-retensi/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRetensi']);

// Mobile JKN Reference Routes (tanpa autentikasi)
Route::prefix('mobile-jkn')->name('mobile-jkn.ref.')->group(function () {
    Route::get('/refrensi-poli-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiPoliHfis'])->name('refrensi-poli-hfis');
    Route::get('/refrensi-dokter-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiDokterHfis'])->name('refrensi-dokter-hfis');
});

// Antrol BPJS Routes (tanpa autentikasi)
Route::prefix('antrol-bpjs')->name('antrol-bpjs.')->group(function () {
    // Antrol BPJS Home (cards menu)
    Route::get('/', function () { return view('mobile-jkn.home'); })->name('index');
    
    // Sub-feature pages
    Route::get('/pendaftaran-mobile-jkn', [App\Http\Controllers\MobileJknController::class, 'index'])->name('pendaftaran-mobile-jkn');
    Route::get('/referensi-poli-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiPoliHfis'])->name('referensi-poli-hfis');
    Route::get('/referensi-dokter-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiDokterHfis'])->name('referensi-dokter-hfis');
});

// Rute yang memerlukan autentikasi
Route::middleware(['web', 'loginauth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // PCare Routes
    Route::prefix('pcare')->group(function () {
        // Index PCare
        Route::get('/', function() { return view('Pcare.index'); })->name('pcare.index');
        // Referensi Dokter
        Route::get('/ref/dokter', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'index'])->name('pcare.ref.dokter');
        Route::get('/api/ref/dokter/tanggal/{tanggal}', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter'])->name('pcare.api.ref.dokter');
        Route::get('/api/ref/dokter/export/excel', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'exportExcel'])->name('pcare.api.ref.dokter.export.excel');
        Route::get('/api/ref/dokter/export/pdf', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'exportPdf'])->name('pcare.api.ref.dokter.export.pdf');
        
        // Referensi Poli
        Route::get('/ref/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'index'])->name('pcare.ref.poli');
        Route::get('/api/ref/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoli'])->name('pcare.api.ref.poli');
        Route::get('/api/ref/poli/export/excel', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'exportExcel'])->name('pcare.api.ref.poli.export.excel');
        Route::get('/api/ref/poli/export/pdf', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'exportPdf'])->name('pcare.api.ref.poli.export.pdf');
    });
    
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
        // Tambahkan route show agar GET /data-pasien/{no_rkm_medis} tidak 405
        Route::get('/{no_rkm_medis}', [App\Http\Controllers\PasienController::class, 'show'])
            ->where('no_rkm_medis', '[0-9\\.]+')
            ->name('pasien.show.inprefix');
    });
    
    // Route untuk detail pasien (diluar prefix data-pasien agar tidak bentrok)
    Route::get('/pasien/{no_rkm_medis}', [App\Http\Controllers\PasienController::class, 'show'])->name('pasien.show');
    
    // Route untuk register
    Route::get('/register', [App\Http\Controllers\RegisterController::class, 'index'])->name('register');
    Route::get('/register/stats', [App\Http\Controllers\RegisterController::class, 'getStats'])->name('register.stats');
    Route::get('/api/poliklinik', [App\Http\Controllers\RegisterController::class, 'getPoliklinik'])->name('get.poliklinik');
    
    // Route untuk regperiksa
    Route::prefix('regperiksa')->group(function () {
        Route::get('/create/{no_rkm_medis}', [App\Http\Controllers\RegPeriksaController::class, 'create'])->name('regperiksa.create');
        Route::post('/store', [App\Http\Controllers\RegPeriksaController::class, 'store'])->name('regperiksa.store');
        Route::get('/generate-noreg/{kd_dokter}/{tgl_registrasi}', [App\Http\Controllers\RegPeriksaController::class, 'generateNoReg'])->name('regperiksa.generate-noreg');
        // Endpoint generate norawat untuk kebutuhan UI registrasi
Route::get('/generate-norawat/{tgl_registrasi}', [App\Http\Controllers\RegPeriksaController::class, 'generateNoRawatApi'])->name('regperiksa.generate-norawat');
        Route::post('/delete', [App\Http\Controllers\RegPeriksaController::class, 'delete'])->name('regperiksa.delete');
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
     
// Route Menu Ralan
     
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
    
    // Route Menu Laporan
    Route::get('/laporan', function () {
        return view('laporan.index');
    })->name('laporan.index');
    
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
        
        // Route Laporan
        Route::prefix('laporan')->group(function () {
            Route::get('/program', [App\Http\Controllers\Ranap\LaporanController::class, 'laporanProgram'])->name('ranap.laporan.program');
            Route::get('/grafik', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'index'])->name('ranap.laporan.grafik');
            
            // Standalone Demographic Analysis Page
            Route::get('/demografi-pasien', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'showDemografi'])->name('ranap.laporan.demografi-pasien');
            
            // Top 10 Diseases Analysis Page
            Route::get('/top-penyakit', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'showTopPenyakit'])->name('ranap.laporan.top-penyakit');
            
            // API Routes for Grafik Analisa
            Route::get('/grafik/data', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getAnalyticsData'])->name('ranap.laporan.grafik.data');
            Route::get('/grafik/export', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'exportData'])->name('ranap.laporan.grafik.export');
            Route::get('/grafik/stats', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getRealtimeStats'])->name('ranap.laporan.grafik.stats');
            
            // Demographic Analysis Routes
            Route::get('/grafik/demografi', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getDemograficData'])->name('ranap.laporan.grafik.demografi');
            Route::get('/grafik/demografi/export', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'exportDemograficData'])->name('ranap.laporan.grafik.demografi.export');
            Route::get('/grafik/kabupaten-db', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getKabupatenFromDb'])->name('ranap.laporan.grafik.kabupaten-db');
            Route::get('/grafik/kecamatan-all', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getAllKecamatanFromDb'])->name('ranap.laporan.grafik.kecamatan-all');
            Route::get('/grafik/kelurahan-all', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getAllKelurahanFromDb'])->name('ranap.laporan.grafik.kelurahan-all');
            
            // Top Diseases Analysis Routes
            Route::get('/grafik/top-penyakit', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'getTopPenyakitData'])->name('ranap.laporan.grafik.top-penyakit');
            Route::get('/grafik/top-penyakit/export', [App\Http\Controllers\Ranap\GrafikAnalisaController::class, 'exportTopPenyakitData'])->name('ranap.laporan.grafik.top-penyakit.export');
        });
    });
    
    // Route untuk Partograf
    Route::get('/partograf-klasik/{id_hamil}', [App\Http\Controllers\PartografController::class, 'showKlasik'])->name('partograf.klasik');
    
    // Route menu ePPBGM
    Route::get('/eppbgm', function () { return view('eppbgm.index'); })->name('eppbgm.index');
    
    // Route menu ILP
    Route::get('/ilp', function () { return view('ilp.index'); })->name('ilp.index');
    Route::prefix('ilp')->name('ilp.')->group(function () {
        Route::get('/dashboard', [App\Http\Controllers\ILP\DashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/data', [App\Http\Controllers\ILP\DashboardController::class, 'index'])->name('dashboard.data');
        Route::get('/dashboard-pws', [App\Http\Controllers\ILP\DashboardController::class, 'dashboardPws'])->name('dashboard.pws');
        Route::get('/dashboard-pws/analisis', [App\Http\Controllers\ILP\DashboardController::class, 'getAnalisisPkgAjax'])->name('dashboard.pws.analisis');
        Route::get('/dashboard-ckg', [App\Http\Controllers\ILP\DashboardCKGController::class, 'index'])->name('dashboard-ckg');
        Route::get('/pendaftaran', [App\Http\Controllers\ILP\PendaftaranController::class, 'index'])->name('pendaftaran');
        Route::get('/pelayanan', [App\Http\Controllers\ILP\PelayananController::class, 'index'])->name('pelayanan');
        Route::put('/update/{id}', [App\Http\Controllers\ILP\PelayananController::class, 'update'])->name('update');
        Route::get('/cetak/{id}', [App\Http\Controllers\ILP\PelayananController::class, 'cetakPdf'])->name('cetak');
        Route::post('/get-summary', [App\Http\Controllers\IlpController::class, 'getSummary'])->name('get-summary');
        Route::post('/send-pdf', [App\Http\Controllers\IlpController::class, 'sendPdf'])->name('send-pdf');
        Route::post('/send-whatsapp', [App\Http\Controllers\IlpController::class, 'sendWhatsApp'])->name('send-whatsapp');
        
        // WhatsApp Gateway Routes
        Route::prefix('whatsapp')->name('whatsapp.')->group(function () {
            Route::post('/send', [App\Http\Controllers\WhatsAppController::class, 'sendMessage'])->name('send');
            Route::get('/session/status', [App\Http\Controllers\WhatsAppController::class, 'getSessionStatus'])->name('session.status');
            Route::get('/session/qr', [App\Http\Controllers\WhatsAppController::class, 'getQRCode'])->name('session.qr');
            Route::post('/session/create', [App\Http\Controllers\WhatsAppController::class, 'createSession'])->name('session.create');
            Route::delete('/session/delete', [App\Http\Controllers\WhatsAppController::class, 'deleteSession'])->name('session.delete');
            Route::post('/webhook', [App\Http\Controllers\WhatsAppController::class, 'webhook'])->name('webhook');
            Route::get('/dashboard', function() {
                return view('whatsapp.dashboard');
            })->name('dashboard');
            Route::get('/queue-dashboard', function() {
                return view('whatsapp.queue-dashboard');
            })->name('queue.dashboard');
            
            // Queue Management Routes
            Route::post('/queue/process', [App\Http\Controllers\WhatsAppController::class, 'processQueue'])->name('queue.process');
            Route::get('/queue/stats', [App\Http\Controllers\WhatsAppController::class, 'getQueueStats'])->name('queue.stats');
            Route::get('/queue/list', [App\Http\Controllers\WhatsAppController::class, 'getQueueList'])->name('queue.list');
            Route::delete('/queue/{id}', [App\Http\Controllers\WhatsAppController::class, 'deleteFromQueue'])->name('queue.delete');
            Route::post('/queue/{id}/retry', [App\Http\Controllers\WhatsAppController::class, 'retryMessage'])->name('queue.retry');
            
            // Node.js WhatsApp Gateway Routes
            Route::prefix('node')->name('node.')->group(function () {
                Route::get('/dashboard', [App\Http\Controllers\WhatsAppNodeController::class, 'dashboard'])->name('dashboard');
                Route::post('/qr-code', [App\Http\Controllers\WhatsAppNodeController::class, 'getQrCode'])->name('qr');
                Route::get('/status', [App\Http\Controllers\WhatsAppNodeController::class, 'getServerStatus'])->name('status');
                Route::post('/send-message', [App\Http\Controllers\WhatsAppNodeController::class, 'sendMessage'])->name('send-message');
                Route::post('/send-file', [App\Http\Controllers\WhatsAppNodeController::class, 'sendFile'])->name('send-file');
                Route::post('/process-queue', [App\Http\Controllers\WhatsAppNodeController::class, 'processQueueViaNode'])->name('process-queue');
                Route::post('/start', [\App\Http\Controllers\WhatsAppNodeController::class, 'startServer'])->name('start');
                Route::post('/stop', [App\Http\Controllers\WhatsAppNodeController::class, 'stopServer'])->name('stop');
                Route::post('/clear-cache', [App\Http\Controllers\WhatsAppNodeController::class, 'clearCache'])->name('clear-cache');
                Route::post('/quick-start', [App\Http\Controllers\WhatsAppNodeController::class, 'quickStartServer'])->name('quick-start');
                Route::get('/logs', [App\Http\Controllers\WhatsAppNodeController::class, 'getLogs'])->name('logs');
                Route::post('/execute-command', [App\Http\Controllers\WhatsAppNodeController::class, 'executeCommand'])->name('execute-command');
                Route::get('/realtime-output', [App\Http\Controllers\WhatsAppNodeController::class, 'getRealtimeOutput'])->name('realtime-output');
            });
        });
        Route::get('/faktor-resiko', [App\Http\Controllers\ILP\FaktorResikoController::class, 'index'])->name('faktor-resiko');
        Route::get('/get-posyandu', [App\Http\Controllers\ILP\FaktorResikoController::class, 'getPosyandu'])->name('get-posyandu');
        
        // Route untuk Sasaran CKG
        Route::get('/sasaran-ckg', [App\Http\Controllers\ILP\SasaranCKGController::class, 'index'])->name('sasaran-ckg');
        Route::get('/sasaran-ckg/detail/{noRekamMedis}', [App\Http\Controllers\ILP\SasaranCKGController::class, 'detail'])->name('sasaran-ckg.detail');
        Route::get('/sasaran-ckg/kirim-wa/{noRekamMedis}', [App\Http\Controllers\ILP\SasaranCKGController::class, 'kirimWA'])->name('sasaran-ckg.kirim-wa');
        
        // Route untuk Pendaftaran CKG
        Route::get('/pendaftaran-ckg', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'index'])->name('pendaftaran-ckg');
        // Allow DataTables to fetch data in local dev without auth to prevent HTML redirects breaking JSON parsing
        Route::get('/pendaftaran-ckg/data', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'data'])
            ->name('pendaftaran-ckg.data')
            ->withoutMiddleware(app()->environment('local') ? ['loginauth'] : []);
        Route::get('/pendaftaran-ckg/export/excel', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'exportExcel'])->name('pendaftaran-ckg.export.excel');
        Route::get('/pendaftaran-ckg/detail', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'detail'])->name('ckg.detail');
        Route::get('/pendaftaran-ckg/detail-sekolah', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'detailSekolah'])->name('ckg.detail-sekolah');
        Route::post('/pendaftaran-ckg/update-status', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updateStatus'])->name('ckg.update-status');
Route::post('/pendaftaran-ckg/update-petugas-entry-sekolah', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updatePetugasEntrySekolah'])->name('pendaftaran-ckg.update-petugas-entry-sekolah');
Route::get('/pendaftaran-ckg/check-processing-status', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'checkProcessingStatus'])->name('ckg.check-processing-status');
Route::post('/pendaftaran-ckg/set-processing', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'setProcessing'])->name('ckg.set-processing');
Route::post('/pendaftaran-ckg/release-processing', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'releaseProcessing'])->name('ckg.release-processing');
        Route::post('/pendaftaran-ckg/update-kd-kel', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updateKdKel'])->name('ckg.update-kd-kel');
        Route::post('/pendaftaran-ckg/update-kode-posyandu', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updateKodePosyandu'])->name('ckg.update-kode-posyandu');
        
        // Route untuk ILP Dewasa - dengan penanganan URL yang di-encode
        Route::get('/dewasa/{noRawat}', [App\Http\Controllers\ILP\IlpDewasaController::class, 'index'])
            ->name('dewasa.form')
            ->where('noRawat', '.*');
        
        Route::post('/dewasa', [App\Http\Controllers\ILP\IlpDewasaController::class, 'store'])->name('dewasa.store');
        Route::delete('/dewasa/{noRawat}', [App\Http\Controllers\ILP\IlpDewasaController::class, 'destroy'])
            ->name('dewasa.destroy')
            ->where('noRawat', '.*');
        
        // Route untuk Data Siswa Sekolah
        Route::resource('data-siswa-sekolah', App\Http\Controllers\ILP\DataSiswaSekolahController::class);
        Route::get('/get-kelas-by-sekolah', [App\Http\Controllers\ILP\DataSiswaSekolahController::class, 'getKelasBySekolah'])->name('get-kelas-by-sekolah');
        Route::get('/data-siswa-sekolah/export/excel', [App\Http\Controllers\ILP\DataSiswaSekolahController::class, 'exportExcel'])->name('data-siswa-sekolah.export.excel');
        Route::get('/data-siswa-sekolah/export/pdf', [App\Http\Controllers\ILP\DataSiswaSekolahController::class, 'exportPdf'])->name('data-siswa-sekolah.export.pdf');
        
        // Route untuk Dashboard Sekolah
        Route::get('/dashboard-sekolah', [App\Http\Controllers\ILP\DashboardSekolahController::class, 'index'])->name('dashboard-sekolah');
        Route::get('/dashboard-sekolah/export/excel', [App\Http\Controllers\ILP\DashboardSekolahController::class, 'exportExcel'])->name('dashboard-sekolah.export.excel');
        Route::get('/dashboard-sekolah/export/pdf', [App\Http\Controllers\ILP\DashboardSekolahController::class, 'exportPdf'])->name('dashboard-sekolah.export.pdf');
        Route::get('/analisa-ckg-sekolah', [App\Http\Controllers\ILP\DashboardCkgSekolahController::class, 'index'])->name('analisa-ckg-sekolah');
        Route::get('/analisa-ckg-sekolah/export/excel', [App\Http\Controllers\ILP\DashboardCkgSekolahController::class, 'exportExcel'])->name('analisa-ckg-sekolah.export.excel');
        Route::get('/presentasi-ckg-sekolah', [App\Http\Controllers\ILP\DashboardCkgSekolahController::class, 'presentasi'])->name('presentasi-ckg-sekolah');
    });


    // Halaman React Registrasi Pasien
    Route::get('/reg-periksa', function () {
        return view('reg_periksa.index');
    })->name('regperiksa.index');

    // API: List Penjab (Cara Bayar)
    Route::get('/api/penjab', function() {
        $rows = DB::table('penjab')
            ->select(DB::raw('kd_pj as id'), DB::raw('png_jawab as text'))
            ->orderBy('png_jawab')
            ->get();
        return response()->json($rows);
    })->name('api.penjab');

    // API: Data Registrasi Hari Ini
    Route::get('/api/regperiksa/today', function(Illuminate\Http\Request $request) {
        $date = $request->query('date', date('Y-m-d'));
        $kdPoli = $request->query('kd_poli');

        $query = DB::table('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->join('poliklinik', 'reg_periksa.kd_poli', '=', 'poliklinik.kd_poli')
            ->join('dokter', 'reg_periksa.kd_dokter', '=', 'dokter.kd_dokter')
            ->join('penjab', 'reg_periksa.kd_pj', '=', 'penjab.kd_pj')
            ->select(
                'reg_periksa.no_reg',
                'reg_periksa.no_rawat',
                'pasien.no_rkm_medis',
                'pasien.nm_pasien',
                'poliklinik.nm_poli',
                'poliklinik.kd_poli',
                'dokter.nm_dokter',
                'penjab.png_jawab',
                'reg_periksa.kd_pj',
                'reg_periksa.jam_reg',
                'reg_periksa.stts'
            )
            ->where('reg_periksa.tgl_registrasi', '=', $date)
            ->orderBy('reg_periksa.no_reg', 'desc')
            ->orderBy('reg_periksa.jam_reg', 'desc');

        if (!empty($kdPoli)) {
            $query->where('reg_periksa.kd_poli', '=', $kdPoli);
        }

        $rows = $query->get();

        // Hitung jumlah pasien yang sudah CKG berdasarkan no_rkm_medis yang muncul di tabel skrining_pkg
        $nrms = $rows->pluck('no_rkm_medis')->filter()->unique()->values();
        $ckgCount = 0;
        if ($nrms->count() > 0) {
            $ckgCount = DB::table('skrining_pkg')
                ->whereIn('no_rkm_medis', $nrms)
                ->distinct()
                ->count('no_rkm_medis');
        }

        return response()->json(['data' => $rows, 'ckg_count' => $ckgCount]);
    })->name('api.regperiksa.today');
    
    // API: Setting Hospital Info untuk header label
    Route::get('/api/setting/hospital-info', function() {
        try {
            $info = App\Models\Setting::getHospitalInfo();
            return response()->json($info ?? []);
        } catch (\Throwable $e) {
            Log::error('API setting hospital-info error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengambil setting rumah sakit'], 500);
        }
    })->name('api.setting.hospital-info');
    
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
            Log::error("Error generateNoReg via Livewire: " . $e->getMessage());
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
        
        Route::get('/data-pendaftaran', function () {
            return view('Pcare.data-pendaftaran-pcare');
        })->name('pcare.data-pendaftaran');
        
        // Halaman status pendaftaran PCare (membandingkan total registrasi vs sukses terkirim)
        Route::get('/status-pendaftaran', function () {
            return view('Pcare.status-pendaftaran-pcare');
        })->name('pcare.status-pendaftaran');
        
        Route::get('/data-peserta-by-nik', function () {
            return view('Pcare.data-peserta-by-nik');
        })->name('pcare.data-peserta-by-nik');

        Route::get('/data-kunjungan', [App\Http\Controllers\PcareKunjunganController::class, 'index'])->name('pcare.data-kunjungan');
        Route::get('/kunjungan/{noRawat}', [App\Http\Controllers\PcareKunjunganController::class, 'show'])->name('pcare.kunjungan.show');
        Route::post('/kunjungan/kirim-ulang/{noRawat}', [App\Http\Controllers\PcareKunjunganController::class, 'kirimUlang'])->name('pcare.kunjungan.kirim-ulang');
        Route::post('/kunjungan/kirim-ulang-batch', [App\Http\Controllers\PcareKunjunganController::class, 'kirimUlangBatch'])->name('pcare.kunjungan.kirim-ulang-batch');
        Route::post('/api/pcare/pendaftaran/jadikan-kunjungan', [PcarePendaftaranController::class, 'jadikanKunjungan']);

        // Route untuk Referensi Dokter PCare
        Route::get('/ref/dokter', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'index'])->name('pcare.ref.dokter');
        Route::get('/ref/dokter/get', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter'])->name('pcare.ref.dokter.get');
        
        // Route untuk Referensi Poli PCare
        Route::get('/ref/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'index'])->name('pcare.ref.poli');
        Route::get('/api/ref/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoli'])->name('pcare.api.ref.poli');
        Route::get('/api/ref/poli/export/excel', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'exportExcel'])->name('pcare.api.ref.poli.export.excel');
        Route::get('/api/ref/poli/export/pdf', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'exportPdf'])->name('pcare.api.ref.poli.export.pdf');
        
        // Route untuk menu referensi (sesuai dengan menu yang ditambahkan)
        Route::get('/referensi/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'index'])->name('pcare.referensi.poli');
        Route::get('/referensi/dokter', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'index'])->name('pcare.referensi.dokter');
        Route::get('/api/ref/dokter', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter'])->name('pcare.ref.dokter.api');
    });

    // Route testing untuk poli FKTP tanpa middleware
    Route::get('/test-poli-fktp/{start}/{limit}', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoliFktp'])
        ->withoutMiddleware(['loginauth'])
        ->name('test.poli.fktp');
        
    // Route test untuk endpoint dokter sesuai katalog BPJS
    Route::get('/test-dokter-fktp/{start}/{limit}', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokterPaginated'])
        ->withoutMiddleware(['loginauth'])
        ->name('test.dokter.fktp');
        
    // Route test sederhana untuk debug
    Route::get('/test-simple', function () {
    header('Content-Type: application/json');
    echo '{"message":"Test simple works","timestamp":"' . date('Y-m-d H:i:s') . '"}';
    exit;
})->name('test.simple');
    
    // Route test controller sederhana
    Route::get('/test-controller', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'testMethod'])
        ->withoutMiddleware(['loginauth'])
        ->name('test.controller');
    
    Route::get('/test-ref-poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'index'])
        ->withoutMiddleware(['loginauth'])
        ->name('test.ref.poli');
        
    Route::get('/test-api-ref-poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoli'])
        ->withoutMiddleware(['loginauth'])
        ->name('test.api.ref.poli');

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
Route::get('/antri-poli', [App\Http\Controllers\AntriPoliController::class, 'index'])->name('antri-poli.index');
Route::get('/api/antri-poli/display', [App\Http\Controllers\AntriPoliController::class, 'getDisplayData'])->name('api.antri-poli.display');

    
Route::get('/farmasi/permintaan-medis', function () {
    return view('farmasi.permintaan-medis');
});



// Farmasi module routes
Route::prefix('farmasi')->name('farmasi.')->group(function () {
    // Index (cards menu)
    Route::get('/', function () {
        return view('farmasi.index');
    })->name('index');

    // Master Data
    Route::get('/industri-farmasi', function () { return view('farmasi.industri-farmasi'); });
    Route::get('/data-suplier', function () { return view('farmasi.data-suplier'); })->name('datasuplier.index');
    Route::get('/satuan-barang', function () { return view('farmasi.satuan-barang'); })->name('satuan-barang.index');
    // Added JSON and CRUD endpoints for Satuan Barang
    Route::get('/satuan-barang/json', [App\Http\Controllers\Farmasi\SatuanBarangController::class, 'indexJson'])->name('satuan-barang.json');
    Route::post('/satuan-barang', [App\Http\Controllers\Farmasi\SatuanBarangController::class, 'store'])->name('satuan-barang.store');
    Route::put('/satuan-barang/{kode_sat}', [App\Http\Controllers\Farmasi\SatuanBarangController::class, 'update'])->name('satuan-barang.update');
    Route::delete('/satuan-barang/{kode_sat}', [App\Http\Controllers\Farmasi\SatuanBarangController::class, 'destroy'])->name('satuan-barang.destroy');
    Route::get('/metode-racik', function () { return view('farmasi.metode-racik'); });
    Route::get('/konversi-satuan', function () { return view('farmasi.konversi-satuan'); });
    Route::get('/jenis-obat', function () { return view('farmasi.jenis-obat'); });
    Route::get('/kategori-obat', function () { return view('farmasi.kategori-obat'); })->name('kategori-obat.index');
    Route::get('/golongan-obat', function () { return view('farmasi.golongan-obat'); });
    // JSON and CRUD endpoints for Golongan Obat (React)
    Route::get('/golongan-obat/json', [App\Http\Controllers\Farmasi\GolonganBarangController::class, 'indexJson'])->name('golongan-obat.json');
    Route::post('/golongan-obat', [App\Http\Controllers\Farmasi\GolonganBarangController::class, 'store'])->name('golongan-obat.store');
    Route::put('/golongan-obat/{kode}', [App\Http\Controllers\Farmasi\GolonganBarangController::class, 'update'])->name('golongan-obat.update');
    Route::delete('/golongan-obat/{kode}', [App\Http\Controllers\Farmasi\GolonganBarangController::class, 'destroy'])->name('golongan-obat.destroy');
    Route::get('/set-harga-obat', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'index'])->name('set-harga-obat');
    Route::get('/set-harga-obat/json', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'getPercentageData'])->name('set-harga-obat.json');
    Route::get('/set-penjualan-umum', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'showPenjualanUmum']);
    Route::get('/set-penjualan/{kdjns}', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'showPenjualanPerJenis']);
    Route::get('/set-penjualan-barang/{kode_brng}', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'showPenjualanPerBarang']);
// JSON list endpoints for React summary tables
Route::get('/set-penjualan/json', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'listPenjualanPerJenis']);
Route::get('/set-penjualan-barang/json', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'listPenjualanPerBarang']);
Route::post('/set-penjualan-barang', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'storePenjualanPerBarang'])->name('set-penjualan-barang.store');
Route::delete('/set-penjualan-barang/{kode_brng}', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'destroyPenjualanPerBarang'])->name('set-penjualan-barang.destroy');
    
    // Data Obat (gunakan controller untuk dukungan Inertia + JSON)
    Route::get('/data-obat', [App\Http\Controllers\Farmasi\DataBarangController::class, 'index'])->name('data-obat');
    Route::post('/data-obat', [App\Http\Controllers\Farmasi\DataBarangController::class, 'store'])->name('data-obat.store');
    Route::put('/data-obat/{kode_brng}', [App\Http\Controllers\Farmasi\DataBarangController::class, 'update'])->name('data-obat.update');
    Route::delete('/data-obat/{kode_brng}', [App\Http\Controllers\Farmasi\DataBarangController::class, 'destroy'])->name('data-obat.destroy');
    Route::put('/data-obat/update-harga-semua', [App\Http\Controllers\Farmasi\DataBarangController::class, 'updateHargaSemua'])->name('data-obat.update-harga-semua');
    Route::get('/data-obat/dropdowns', [App\Http\Controllers\Farmasi\DataBarangController::class, 'dropdowns'])->name('data-obat.dropdowns');
    Route::get('/data-obat/next-code', [App\Http\Controllers\Farmasi\DataBarangController::class, 'nextCode'])->name('data-obat.next-code');

    // Transaksi
    Route::get('/stok-opname', function () { return view('farmasi.stok-opname'); })->name('stok-opname');
    Route::get('/pembelian-obat', function () { return view('farmasi.pembelian-obat'); })->name('pembelian-obat');
    Route::get('/penjualan-obat', function () { return view('farmasi.penjualan-obat'); })->name('penjualan-obat');
    Route::get('/resep-obat', function () { return view('farmasi.resep-obat'); })->name('resep-obat');
    Route::get('/riwayat-transaksi-gudang', function () { return view('farmasi.riwayat-transaksi-gudang'); })->name('riwayat-transaksi-gudang');

    // Laporan
    Route::get('/dashboard', function () { return view('farmasi.dashboard'); })->name('dashboard');

    // Tambahan opsional
    Route::get('/stok-obat', function () { return view('farmasi.stok-obat'); })->name('stok-obat');
    Route::get('/data-opname', function () { return view('farmasi.data-opname'); })->name('data-opname');
});

Route::get('/get-videos', [App\Http\Controllers\VideoController::class, 'getVideos']);
Route::get('/display/videos/{filename}', [App\Http\Controllers\VideoController::class, 'stream'])->where('filename', '.*');
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

// Route untuk API skrining (tanpa autentikasi)

// POST/DELETE endpoints (unprefixed names expected by frontend)
Route::post('/farmasi/set-harga-obat', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'update'])->name('set-harga-obat.update');
Route::post('/farmasi/set-penjualan-umum', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'updatePenjualanUmum'])->name('set-penjualan-umum.update');
Route::post('/farmasi/set-penjualan', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'storePenjualanPerJenis'])->name('set-penjualan.store');
Route::delete('/farmasi/set-penjualan/{kdjns}', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'destroyPenjualanPerJenis'])->name('set-penjualan.destroy');


// CRUD Jadwal UKM (React + API)
Route::middleware(['web', 'loginauth'])->group(function () {
    // Halaman React untuk Jadwal UKM
    Route::get('/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'page'])->name('jadwal-ukm.page');
    Route::get('/display-kegiatan-ukm', function () { return view('react.display-kegiatan-ukm'); })->name('display-kegiatan-ukm.page');
    // Alias route agar konsisten dengan nama kartu 'Display Jadwal UKM'
    Route::get('/display-jadwal-ukm', function () { return view('react.display-kegiatan-ukm'); })->name('display-jadwal-ukm.page');

    // API Endpoints
    Route::get('/api/jadwal-ukm/meta', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'meta'])->name('jadwal-ukm.meta');
    Route::get('/api/jadwal-ukm/describe', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'describe'])->name('jadwal-ukm.describe');
    Route::get('/api/jadwal-ukm/monthly', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'monthly'])->name('jadwal-ukm.monthly');
    Route::get('/api/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'data'])->name('jadwal-ukm.data');
    Route::post('/api/jadwal-ukm', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'store'])->name('jadwal-ukm.store');
    Route::put('/api/jadwal-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'update'])->name('jadwal-ukm.update');
    Route::delete('/api/jadwal-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\JadwalUkmController::class, 'destroy'])->name('jadwal-ukm.destroy');
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

// Route untuk API skrining (tanpa autentikasi)

// POST/DELETE endpoints (unprefixed names expected by frontend)
Route::post('/farmasi/set-harga-obat', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'update'])->name('set-harga-obat.update');
Route::post('/farmasi/set-penjualan-umum', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'updatePenjualanUmum'])->name('set-penjualan-umum.update');
Route::post('/farmasi/set-penjualan', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'storePenjualanPerJenis'])->name('set-penjualan.store');
Route::delete('/farmasi/set-penjualan/{kdjns}', [App\Http\Controllers\Farmasi\SetHargaObatController::class, 'destroyPenjualanPerJenis'])->name('set-penjualan.destroy');


// CRUD Kegiatan UKM (React + API)
Route::middleware(['web', 'loginauth'])->group(function () {
    // Halaman Menu UKM (React)
    Route::get('/matrik-kegiatan-ukm', function () {
        return view('react.matrik-kegiatan-ukm');
    })->name('ukm.menu');

    // Halaman React untuk Kegiatan UKM
    Route::get('/kegiatan-ukm', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'page'])->name('kegiatan-ukm.page');

    // API Endpoints
    Route::get('/api/kegiatan-ukm/meta', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'meta'])
        ->name('kegiatan-ukm.meta')
        ->withoutMiddleware(['loginauth']);
    Route::get('/api/kegiatan-ukm', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'data'])
        ->name('kegiatan-ukm.data')
        ->withoutMiddleware(['loginauth']);
    Route::get('/api/kegiatan-ukm/next-code', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'nextCode'])
        ->name('kegiatan-ukm.next-code')
        ->withoutMiddleware(['loginauth']);
    Route::post('/api/kegiatan-ukm', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'store'])
        ->name('kegiatan-ukm.store')
        ->withoutMiddleware(['loginauth', \App\Http\Middleware\VerifyCsrfToken::class]);
    Route::put('/api/kegiatan-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'update'])
        ->name('kegiatan-ukm.update')
        ->withoutMiddleware(['loginauth', \App\Http\Middleware\VerifyCsrfToken::class]);
    Route::delete('/api/kegiatan-ukm/{id}', [App\Http\Controllers\MatrikKegiatanUkm\KegiatanUkmController::class, 'destroy'])
        ->name('kegiatan-ukm.destroy')
        ->withoutMiddleware(['loginauth', \App\Http\Middleware\VerifyCsrfToken::class]);
});
