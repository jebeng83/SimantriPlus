<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\BpjsService;
use Yajra\DataTables\Facades\DataTables;
use AamDsam\Bpjs\PCare;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PcarePendaftaranController extends Controller
{
    protected $bpjsService;
    protected $config;

    public function __construct(BpjsService $bpjsService)
    {
        $this->bpjsService = $bpjsService;
        $this->config = app('pcare_conf');
        
        // Log konfigurasi PCare
        Log::info('PCare Configuration Loaded', [
            'base_url' => $this->config['base_url'],
            'service_name' => $this->config['service_name']
        ]);
    }

    public function getData(Request $request)
    {
        Log::info('PcarePendaftaran getData called', $request->all());
        
        try {
            $query = DB::table('pcare_pendaftaran as pp')
                ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->select([
                    'pp.no_rawat',
                    'pp.tglDaftar',
                    'pp.no_rkm_medis',
                    'p.nm_pasien',
                    'pp.noKartu',
                    'pp.kdPoli',
                    'pp.nmPoli',
                    'pp.noUrut',
                    'pp.status'
                ]);

            if ($request->tanggal) {
                $query->whereDate('pp.tglDaftar', $request->tanggal);
            }

            if ($request->status) {
                $query->where('pp.status', $request->status);
            }
            
            $result = DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('tglDaftar_formatted', function($row) {
                    return date('Y-m-d', strtotime($row->tglDaftar));
                })
                ->addColumn('action', function($row) {
                    return view('Pcare.partials.action-buttons', compact('row'))->render();
                })
                ->rawColumns(['action'])
                ->make(true);
                
            Log::info('PcarePendaftaran getData success', [
                'total_records' => $result->original['recordsTotal']
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('PcarePendaftaran getData error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function getDetail($noRawat)
    {
        try {
            $data = DB::table('pcare_pendaftaran as pp')
                ->join('reg_periksa as rp', 'pp.no_rawat', '=', 'rp.no_rawat')
                ->join('pasien as p', 'rp.no_rkm_medis', '=', 'p.no_rkm_medis')
                ->leftJoin('pemeriksaan_ralan as pr', 'pp.no_rawat', '=', 'pr.no_rawat')
                ->where('pp.no_rawat', $noRawat)
                ->select([
                    'pp.*',
                    'p.nm_pasien',
                    'pr.keluhan',
                    'pr.pemeriksaan',
                    'pr.tensi',
                    'pr.nadi',
                    'pr.respirasi',
                    'pr.tinggi',
                    'pr.berat',
                    'pr.lingkar_perut',
                    'pr.rtl'
                ])
                ->first();

            if (!$data) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function jadikanKunjungan(Request $request)
    {
        Log::info('Memulai proses jadikan kunjungan', [
            'no_rawat' => $request->no_rawat,
            'timestamp' => now()->toDateTimeString()
        ]);

        try {
            DB::beginTransaction();
            
            // 1. Validasi input yang diperlukan
            $validator = Validator::make($request->all(), [
                'no_rawat' => 'required',
                'noKartu' => 'required|digits:13',
                'kdPoli' => 'required'
            ]);

            if ($validator->fails()) {
                throw new \Exception('Validasi gagal: ' . $validator->errors()->first());
            }

            // 2. Cek status peserta BPJS
            Log::info('Validasi peserta BPJS', [
                'noKartu' => $request->noKartu
            ]);
            
            $peserta = new PCare\Peserta($this->config);
            $cekPeserta = $peserta->keyword($request->noKartu)->show();

            Log::info('Hasil validasi peserta', [
                'response' => $cekPeserta
            ]);

            if (!isset($cekPeserta['response'])) {
                throw new \Exception('Status peserta tidak aktif atau tidak ditemukan');
            }

            // 3. Ambil data pendaftaran
            $pendaftaran = DB::table('pcare_pendaftaran')
                ->where('no_rawat', $request->no_rawat)
                ->first();

            if (!$pendaftaran) {
                throw new \Exception('Data pendaftaran tidak ditemukan');
            }

            // 4. Ambil data pemeriksaan vital signs
            $pemeriksaan = DB::table('pemeriksaan_ralan')
                ->where('no_rawat', $request->no_rawat)
                ->first();

            if (!$pemeriksaan) {
                throw new \Exception('Data pemeriksaan tidak ditemukan');
            }

            // Validasi vital signs
            if (!$pemeriksaan->tensi || !$pemeriksaan->tinggi || !$pemeriksaan->berat || 
                !$pemeriksaan->respirasi || !$pemeriksaan->nadi || !$pemeriksaan->suhu) {
                throw new \Exception('Data vital signs tidak lengkap');
            }

            // 5. Ambil data diagnosa
            $diagnosa = DB::table('diagnosa_pasien')
                ->where('no_rawat', $request->no_rawat)
                ->orderBy('prioritas', 'asc')
                ->limit(3)
                ->get();

            if ($diagnosa->isEmpty()) {
                throw new \Exception('Data diagnosa tidak ditemukan');
            }

            // Validasi format tanggal
            $tglDaftar = \Carbon\Carbon::createFromFormat('Y-m-d', $pendaftaran->tglDaftar)
                ->format('d-m-Y');
            $tglPulang = \Carbon\Carbon::now()->format('d-m-Y');

            // 6. Siapkan data kunjungan sesuai format BPJS
            $dataKunjungan = [
                'noKunjungan' => null,
                'noKartu' => $pendaftaran->noKartu,
                'tglDaftar' => $tglDaftar,
                'kdPoli' => $pendaftaran->kdPoli,
                'keluhan' => $pemeriksaan->keluhan ?: 'Tidak Ada',
                'kdSadar' => '01', // Compos Mentis
                'sistole' => (int)($pemeriksaan->tensi ? explode('/', $pemeriksaan->tensi)[0] : 0),
                'diastole' => (int)($pemeriksaan->tensi ? explode('/', $pemeriksaan->tensi)[1] : 0),
                'beratBadan' => (int)$pemeriksaan->berat,
                'tinggiBadan' => (int)$pemeriksaan->tinggi,
                'respRate' => (int)$pemeriksaan->respirasi,
                'heartRate' => (int)$pemeriksaan->nadi,
                'lingkarPerut' => (int)($pemeriksaan->lingkar_perut ?? 0),
                'kdStatusPulang' => '3', // Berobat Jalan
                'tglPulang' => $tglPulang,
                'kdDokter' => $pendaftaran->kdDokter,
                'kdDiag1' => $diagnosa[0]->kd_penyakit,
                'kdDiag2' => isset($diagnosa[1]) ? $diagnosa[1]->kd_penyakit : null,
                'kdDiag3' => isset($diagnosa[2]) ? $diagnosa[2]->kd_penyakit : null,
                'kdPoliRujukInternal' => null,
                'rujukLanjut' => null,
                'kdTacc' => 0,
                'alasanTacc' => null,
                'suhu' => str_replace('.', ',', $pemeriksaan->suhu),
                'alergiMakan' => '00',
                'alergiUdara' => '00',
                'alergiObat' => '00',
                'kdPrognosa' => '01',
                'anamnesa' => $pemeriksaan->pemeriksaan ?: 'Tidak Ada',
                'terapiObat' => $pemeriksaan->rtl ?: 'Tidak Ada',
                'terapiNonObat' => '-',
                'bmhp' => '-'
            ];

            Log::info('Data kunjungan yang akan dikirim ke BPJS', [
                'endpoint' => $this->config['base_url'] . '/' . $this->config['service_name'] . '/kunjungan',
                'data' => $dataKunjungan
            ]);

            // 7. Kirim data kunjungan ke BPJS
            $kunjungan = new PCare\Kunjungan($this->config);
            
            // Tambahkan retry mechanism
            $maxRetries = 3;
            $attempt = 1;
            $success = false;
            
            while ($attempt <= $maxRetries && !$success) {
                try {
                    Log::info("Mencoba mengirim kunjungan (Percobaan ke-{$attempt})", [
                        'no_rawat' => $request->no_rawat
                    ]);
                    
                    $response = $kunjungan->store($dataKunjungan);
                    
                    Log::info('Response dari BPJS PCare', [
                        'attempt' => $attempt,
                        'response' => $response,
                        'timestamp' => now()->toDateTimeString()
                    ]);
                    
                    if (isset($response['metaData']['code']) && $response['metaData']['code'] == '201') {
                        $success = true;
                        break;
                    }
                    
                    throw new \Exception($response['metaData']['message'] ?? 'Gagal mengirim kunjungan');
        } catch (\Exception $e) {
                    Log::warning("Kegagalan pengiriman kunjungan (Percobaan ke-{$attempt})", [
                        'error' => $e->getMessage()
                    ]);
                    
                    if ($attempt == $maxRetries) {
                        throw $e;
                    }
                    
                    $attempt++;
                    sleep(2); // Tunggu 2 detik sebelum retry
                }
            }

            if ($success) {
                // Update status pendaftaran
                DB::table('pcare_pendaftaran')
                    ->where('no_rawat', $request->no_rawat)
                    ->update(['status' => 'Sudah Dikunjungi']);

                // Simpan data kunjungan
                DB::table('pcare_kunjungan_umum')->insert([
                    'no_rawat' => $request->no_rawat,
                    'noKunjungan' => $response['response']['message'],
                    'status' => 'Terkirim',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                DB::commit();

                Log::info('Transaksi berhasil disimpan', [
                    'no_rawat' => $request->no_rawat,
                    'noKunjungan' => $response['response']['message']
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Berhasil mengirim kunjungan',
                    'data' => $response['response']
                ]);
            }

            throw new \Exception('Gagal mengirim kunjungan setelah ' . $maxRetries . ' percobaan');

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Error jadikan kunjungan', [
                'no_rawat' => $request->no_rawat,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'timestamp' => now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}