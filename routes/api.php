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
use App\Http\Antrol\AddAntreanController;
use App\Http\Antrol\PanggilAntreanController;
use App\Http\Controllers\PasienController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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

// Test route untuk debugging
Route::get('/test-api', function () {
    return response()->json([
        'message' => 'API test works',
        'timestamp' => date('Y-m-d H:i:s')
    ]);
});

// Rute untuk obat ranap
Route::get('/ranap/{bangsal}/obat', [App\Http\Controllers\API\ResepRanapController::class, 'getObatRanap']);
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
    
    // Tambahkan route untuk mapping poli
    Route::get('mapping-poli/{kd_poli_rs?}', [App\Http\Controllers\API\PcareController::class, 'getMappingPoli']);

    // API untuk data pendaftaran PCare
    Route::get('/pendaftaran/data', [App\Http\Controllers\API\PcarePendaftaranController::class, 'getData']);
    Route::get('/pendaftaran/detail/{no_rawat}', [App\Http\Controllers\API\PcarePendaftaranController::class, 'getDetail']);
    Route::get('/pendaftaran/export/excel', [App\Http\Controllers\API\PcarePendaftaranController::class, 'exportExcel']);
    Route::get('/pendaftaran/export/pdf', [App\Http\Controllers\API\PcarePendaftaranController::class, 'exportPdf']);
    // Status pendaftaran PCare dibandingkan dengan data registrasi (reg_periksa)
    Route::get('/pendaftaran/status', [App\Http\Controllers\API\PcarePendaftaranController::class, 'getStatusRegistrations']);
    
    // API untuk referensi dengan pagination sesuai katalog BPJS
    Route::get('poli/fktp/{start}/{limit}', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoliFktp']);
    Route::get('dokter/{start}/{limit}', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokterPaginated']);
    
    // Test connection endpoint untuk debugging
    Route::get('test-connection', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'testConnection']);
    
    // API untuk referensi poli dan dokter
    Route::get('ref/poli', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoli']);
    Route::get('ref/poli/tanggal/{tanggal}', [App\Http\Controllers\PCare\ReferensiPoliController::class, 'getPoli']);
    Route::get('ref/dokter', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter']);
    Route::get('ref/dokter/tanggal/{tanggal}', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter']);
    Route::get('ref/dokter/kodepoli/{kodepoli}/tanggal/{tanggal}', [App\Http\Controllers\PCare\ReferensiDokterController::class, 'getDokter']);
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

// Route for PCare pasien detail
Route::get('/pasien/detail/{no_rkm_medis}', [PasienController::class, 'getDetailByRekamMedis']);

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
});

// WhatsApp Gateway API Routes
Route::prefix('whatsapp')->group(function () {
    Route::post('/send', [App\Http\Controllers\WhatsAppController::class, 'sendMessage']);
    Route::get('/session/status', [App\Http\Controllers\WhatsAppController::class, 'getSessionStatus']);
    Route::get('/session/qr', [App\Http\Controllers\WhatsAppController::class, 'getQRCode']);
    Route::post('/session/create', [App\Http\Controllers\WhatsAppController::class, 'createSession']);
    Route::delete('/session/delete', [App\Http\Controllers\WhatsAppController::class, 'deleteSession']);
    Route::post('/webhook', [App\Http\Controllers\WhatsAppController::class, 'webhook'])->withoutMiddleware(['auth:sanctum']);
});

// WhatsApp Gateway Public Webhook (tanpa autentikasi)
Route::post('/whatsapp/webhook/public', [App\Http\Controllers\WhatsAppController::class, 'webhook'])
    ->withoutMiddleware(['auth:sanctum']);

// Route API untuk Mobile JKN yang dapat diakses tanpa autentikasi
Route::prefix('mobile-jkn')->group(function () {
    Route::get('/referensi-poli/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiPoli']);
    Route::get('/referensi-dokter/kodepoli/{kodePoli}/tanggal/{tanggal}', [App\Http\Controllers\API\WsBPJSController::class, 'getReferensiDokter']);
});

// Route untuk Antrian BPJS
Route::prefix('antrean')->group(function () {
    Route::post('/add', [AddAntreanController::class, 'add']);
    Route::post('/panggil', [PanggilAntreanController::class, 'panggil']);
});

// Route untuk mendapatkan no_rawat yang valid berdasarkan no_rkm_medis
Route::post('/get-valid-no-rawat', function (Request $request) {
    try {
        $no_rkm_medis = $request->input('no_rkm_medis');
        
        if (empty($no_rkm_medis)) {
            return response()->json([
                'success' => false,
                'message' => 'No. Rekam Medis diperlukan',
                'no_rawat' => null
            ]);
        }
        
        // Cari no_rawat terbaru untuk pasien berdasarkan no_rkm_medis
        $rawatTerbaru = DB::table('reg_periksa')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->orderBy('tgl_registrasi', 'desc')
            ->orderBy('jam_reg', 'desc')
            ->first();
            
        if ($rawatTerbaru) {
            return response()->json([
                'success' => true,
                'message' => 'No. Rawat valid ditemukan',
                'no_rawat' => $rawatTerbaru->no_rawat
            ]);
        }
        
        // Jika tidak ditemukan no_rawat untuk pasien ini
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada no_rawat terdaftar untuk pasien ini',
            'no_rawat' => null
        ]);
    } catch (\Exception $e) {
        Log::error('Error saat mendapatkan no_rawat valid', [
            'error' => $e->getMessage(),
            'no_rkm_medis' => $no_rkm_medis ?? 'not provided'
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'no_rawat' => null
        ], 500);
    }
});

// Route untuk mendapatkan data poli berdasarkan no_rawat
Route::post('/get-poli-from-rawat', function (Request $request) {
    try {
        $no_rawat = $request->input('no_rawat');
        
        if (empty($no_rawat)) {
            return response()->json([
                'success' => false,
                'message' => 'No. Rawat diperlukan',
                'data' => null
            ]);
        }
        
        // Cari data poli dari reg_periksa dan mapping ke pcare
        $poliData = DB::table('reg_periksa')
            ->join('maping_poliklinik_pcare', 'reg_periksa.kd_poli', '=', 'maping_poliklinik_pcare.kd_poli_rs')
            ->select(
                'reg_periksa.kd_poli',
                'maping_poliklinik_pcare.kd_poli_pcare',
                'maping_poliklinik_pcare.nm_poli_pcare'
            )
            ->where('reg_periksa.no_rawat', $no_rawat)
            ->first();
            
        if ($poliData) {
            return response()->json([
                'success' => true,
                'message' => 'Data poli ditemukan',
                'data' => $poliData
            ]);
        }
        
        // Jika tidak ditemukan poli yang sesuai
        return response()->json([
            'success' => false,
            'message' => 'Tidak ada mapping poli untuk registrasi ini',
            'data' => null
        ]);
    } catch (\Exception $e) {
        Log::error('Error saat mendapatkan data poli dari no_rawat', [
            'error' => $e->getMessage(),
            'no_rawat' => $no_rawat ?? 'not provided'
        ]);
        
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
});

// Route untuk mendapatkan data dokter
Route::get('/dokter', function () {
    try {
        $dokter = DB::table('dokter')
            ->select('kd_dokter', 'nm_dokter')
            ->where('status', '1')
            ->orderBy('nm_dokter', 'asc')
            ->get();
            
        return response()->json([
            'status' => 'success',
            'message' => 'Data dokter berhasil dimuat',
            'data' => $dokter
        ]);
    } catch (\Exception $e) {
        Log::error('Error saat mengambil data dokter', [
            'error' => $e->getMessage()
        ]);
        
        return response()->json([
            'status' => 'error',
            'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            'data' => null
        ], 500);
    }
});

// Route untuk pemeriksaan
Route::post('/pemeriksaan/save', [PemeriksaanController::class, 'save']);

// Status CKG tahun berjalan berdasarkan no_rkm_medis
Route::get('/ckg/status/{no_rkm_medis}', function (Request $request, $no_rkm_medis) {
    try {
        $year = $request->query('year');
        $yearInt = $year ? (int)$year : (int)date('Y');

        if (empty($no_rkm_medis)) {
            return response()->json([
                'success' => false,
                'message' => 'No. Rekam Medis diperlukan'
            ], 400);
        }

        // Hitung jumlah entri skrining tahun ini
        $count = DB::table('skrining_pkg')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->whereYear('tanggal_skrining', $yearInt)
            ->count();

        // Ambil entri terbaru tahun ini
        $latest = DB::table('skrining_pkg')
            ->select('id_pkg', 'tanggal_skrining', 'status', 'kunjungan_sehat')
            ->where('no_rkm_medis', $no_rkm_medis)
            ->whereYear('tanggal_skrining', $yearInt)
            ->orderBy('tanggal_skrining', 'desc')
            ->first();

        $statusText = null;
        if ($latest) {
            $st = (string)($latest->status ?? '');
            $statusText = $st === '1' ? 'Selesai' : ($st === '2' ? 'Sedang Diproses' : 'Belum Selesai');
        }

        return response()->json([
            'success' => true,
            'year' => $yearInt,
            'has_skrining' => $count > 0,
            'count' => $count,
            'latest' => $latest,
            'status_text' => $statusText
        ]);
    } catch (\Exception $e) {
        Log::error('Error get CKG status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
});

// Cek Status SRK (Skrining Riwayat Kesehatan) via Mobile JKN
Route::get('/bpjs/srk-status', function (Request $request) {
    try {
        $nomorkartu = preg_replace('/[^0-9]/', '', (string)$request->query('nomorkartu', ''));
        $nik = preg_replace('/[^0-9]/', '', (string)$request->query('nik', ''));

        if (!$nomorkartu && !$nik) {
            return response()->json([
                'success' => false,
                'message' => 'nomorkartu atau nik diperlukan'
            ], 400);
        }

        // Ambil pasien dari DB
        $pasien = DB::table('pasien')
            ->where(function($q) use ($nomorkartu, $nik) {
                if ($nomorkartu) $q->orWhere('no_peserta', $nomorkartu);
                if ($nik) $q->orWhere('no_ktp', $nik);
            })
            ->first();

        if (!$pasien) {
            return response()->json([
                'success' => false,
                'message' => 'Pasien tidak ditemukan'
            ], 404);
        }

        $today = date('Y-m-d');
        // Payload minimal untuk cek SRK; tidak ditujukan menambah antrean sungguhan
        $payload = [
            'nomorkartu' => $pasien->no_peserta ?? $nomorkartu,
            'nik' => $pasien->no_ktp ?? $nik,
            'nohp' => $pasien->no_tlp ?? '081000000000',
            'kodepoli' => 'INT',
            'namapoli' => 'Poli Internal',
            'norm' => $pasien->no_rkm_medis,
            'tanggalperiksa' => $today,
            'kodedokter' => 999999,
            'namadokter' => 'Dokter SRK Check',
            'jampraktek' => '00:01-00:02',
            'nomorantrean' => '000',
            'angkaantrean' => 0,
            'keterangan' => 'SRK check only'
        ];

        // Panggil WsBPJSController@tambahAntrean
        $controller = new \App\Http\Controllers\API\WsBPJSController();
        $req = new Request($payload);
        $resp = $controller->tambahAntrean($req);

        $json = $resp instanceof \Illuminate\Http\JsonResponse ? json_decode($resp->getContent(), true) : (is_string($resp) ? json_decode($resp, true) : (array)$resp);
        $meta = $json['metadata'] ?? $json['metaData'] ?? null;
        $code = $meta['code'] ?? null;
        $message = $meta['message'] ?? '';

        $belum = false;
        $lowerMsg = is_string($message) ? strtolower($message) : '';
        // Tentukan Belum SRK hanya jika pesan dari BPJS mengandung kata "skrining"
        if ($lowerMsg && (strpos($lowerMsg, 'skrining') !== false)) {
            $belum = true;
        }

        return response()->json([
            'success' => true,
            'srk_status' => $belum ? 'Belum SRK' : 'Sudah SRK',
            'metadata' => $meta,
            'url' => rtrim(env('BPJS_MOBILEJKN_BASE_URL', ''), '/') . '/antrean/add'
        ]);
    } catch (\Exception $e) {
        Log::error('Error cek SRK status: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
});
