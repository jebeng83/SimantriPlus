<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BPJSTestController;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RegPeriksaController;
use App\Http\Controllers\WilayahController;

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
    return redirect()->away('https://ayosehat.kemkes.go.id/promosi-kesehatan');
});

Route::get('/skriningbpjs', function () {
    return redirect()->away('https://webskrining.bpjs-kesehatan.go.id/skrining');
});

Route::get('/offline', function () {
    return view('modules/laravelpwa/offline');
});

Route::get('/kerjo-award', function () {
    return view('kerjo_award');
});

Route::get('/clear-cache', function() {
    $exitCode = Artisan::call('optimize:clear');
    // return what you want
});

// Rute API yang tidak memerlukan autentikasi
Route::get('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'getDiagnosa'])->name('diagnosa');
Route::post('/diagnosa', [App\Http\Controllers\API\ResumePasienController::class, 'simpanDiagnosa'])->name('diagnosa.simpan');
Route::get('/icd9', [App\Http\Controllers\API\ResumePasienController::class, 'getICD9'])->name('icd9');
Route::get('/pegawai', [App\Http\Controllers\API\PemeriksaanController::class, 'getPegawai'])->name('pegawai');
Route::get('/api/pasien', [App\Http\Controllers\RegisterController::class, 'getPasien'])->name('get.pasien');
Route::get('/pasien/search', [App\Http\Controllers\PasienController::class, 'searchPasien'])->name('pasien.search');
Route::get('/api/dokter', [App\Http\Controllers\RegisterController::class, 'getDokter'])->name('dokter');
Route::get('/propinsi', [WilayahController::class, 'getPropinsi'])->name('propinsi');
Route::get('/kabupaten', [WilayahController::class, 'getKabupaten'])->name('kabupaten');
Route::get('/kecamatan', [WilayahController::class, 'getKecamatan'])->name('kecamatan');
Route::get('/kelurahan', [WilayahController::class, 'getKelurahan'])->name('kelurahan');

// Rute untuk berkas
Route::get('/berkas/{noRawat}/{noRM}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRM'])->where('noRawat', '.*');
Route::get('/berkas-retensi/{noRawat}', [App\Http\Controllers\Ralan\PemeriksaanRalanController::class, 'getBerkasRetensi']);

// Rute yang memerlukan autentikasi
Route::middleware(['loginauth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
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
        // Regenerate session ID dan CSRF token
        Session::regenerate(true);
        return csrf_token();
    })->name('refresh-csrf');
    
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
            \Log::error("Error generateNoReg via Livewire: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    })->name('livewire.generate-noreg');
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

// Testing Routes
Route::get('/test/data-ibu-hamil', [\App\Http\Controllers\API\TestController::class, 'testDataIbuHamil']);
