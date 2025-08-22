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
    Route::get('/pendaftaran-mobile-jkn', [App\Http\Controllers\MobileJknController::class, 'index'])->name('pendaftaran-mobile-jkn');
    Route::get('/referensi-poli-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiPoliHfis'])->name('referensi-poli-hfis');
    Route::get('/referensi-dokter-hfis', [App\Http\Controllers\MobileJknController::class, 'refrensiDokterHfis'])->name('referensi-dokter-hfis');
});

// Rute yang memerlukan autentikasi
Route::middleware(['web', 'loginauth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // PCare Routes
    Route::prefix('pcare')->group(function () {
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
        Route::get('/pendaftaran-ckg/detail', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'detail'])->name('ckg.detail');
        Route::get('/pendaftaran-ckg/detail-sekolah', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'detailSekolah'])->name('ckg.detail-sekolah');
        Route::post('/pendaftaran-ckg/update-status', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updateStatus'])->name('ckg.update-status');
Route::post('/pendaftaran-ckg/update-petugas-entry-sekolah', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'updatePetugasEntrySekolah'])->name('pendaftaran-ckg.update-petugas-entry-sekolah');
Route::get('/pendaftaran-ckg/check-processing-status', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'checkProcessingStatus'])->name('ckg.check-processing-status');
Route::post('/pendaftaran-ckg/set-processing', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'setProcessing'])->name('ckg.set-processing');
Route::post('/pendaftaran-ckg/release-processing', [App\Http\Controllers\ILP\PendaftaranCKGController::class, 'releaseProcessing'])->name('ckg.release-processing');
        
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

// Route untuk API skrining (tanpa autentikasi)
Route::post('/api/skrining/demografi', [App\Http\Controllers\SkriningController::class, 'simpanDemografi'])
    ->name('api.skrining.demografi')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
Route::post('/api/skrining/tekanan-darah', [App\Http\Controllers\SkriningController::class, 'simpanTekananDarah'])
    ->name('api.skrining.tekanan-darah')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
Route::post('/api/skrining/perilaku-merokok', [App\Http\Controllers\SkriningController::class, 'simpanPerilakuMerokok'])
    ->name('api.skrining.perilaku-merokok')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
Route::post('/api/skrining/kesehatan-jiwa', [App\Http\Controllers\SkriningController::class, 'simpanKesehatanJiwa'])
    ->name('api.skrining.kesehatan-jiwa')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);
    
Route::post('/api/skrining/simpan', [App\Http\Controllers\SkriningController::class, 'simpanSkrining'])
    ->name('api.skrining.simpan')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Route untuk debugging API
Route::any('/api/skrining/debug', [App\Http\Controllers\SkriningController::class, 'debug'])
    ->name('api.skrining.debug')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);

// Ruta para obtener detalles de pendaftaran PCare para asegurar que coincida con el formato utilizado en la vista.
Route::get('/api/pcare/pendaftaran/detail/{year}/{month}/{day}/{number}', [PcarePendaftaranController::class, 'getDetailByParts']);
Route::get('/api/pcare/pendaftaran/detail/{no_rawat}', [PcarePendaftaranController::class, 'getDetail']);
Route::delete('/api/pcare/pendaftaran/peserta/{noKartu}/tglDaftar/{tglDaftar}/noUrut/{noUrut}/kdPoli/{kdPoli}', [PcarePendaftaranController::class, 'deletePendaftaran']);
Route::get('/api/test', function() {
    return response()->json(['message' => 'API working', 'time' => now()]);
});

// Duplikasi route PCare dihapus - sudah ada di atas

// Include test routes untuk development
// Mobile JKN Routes (Public Access)
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

// if (app()->environment('local')) {
//     require __DIR__.'/test-auth-error.php';
// }
