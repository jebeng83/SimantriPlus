<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PermintaanMedisController extends Controller
{
    /**
     * Generate next No.Permintaan: PMYYYYMMDDNNN
     * - tanggal param expected in format YYYY-MM-DD
     */
    public function nextNumber(Request $request)
    {
        $tanggal = trim((string)$request->query('tanggal', ''));
        if (!$tanggal) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter tanggal diperlukan (format YYYY-MM-DD)'
            ], 400);
        }

        try {
            // Prefix PM + yyyymmdd
            $yyyymmdd = preg_replace('/[^0-9]/', '', $tanggal);
            if (strlen($yyyymmdd) !== 8) {
                // Fallback jika format tidak persis, coba parse
                $yyyymmdd = date('Ymd', strtotime($tanggal));
            }
            $prefix = 'PM' . $yyyymmdd;

            // Ambil max sequence 3 digit dari no_permintaan pada tanggal tsb.
            $row = DB::selectOne(
                "select IFNULL(MAX(CONVERT(RIGHT(permintaan_medis.no_permintaan,3),signed)),0) as maxseq \n                 from permintaan_medis \n                 where permintaan_medis.tanggal = ?",
                [$tanggal]
            );
            $max = intval($row->maxseq ?? 0);
            $nextInt = $max + 1;
            $seq = str_pad((string)$nextInt, 3, '0', STR_PAD_LEFT);
            $nomor = $prefix . $seq;

            return response()->json([
                'success' => true,
                'tanggal' => $tanggal,
                'prefix' => $prefix,
                'sequence' => $seq,
                'nomor' => $nomor,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal generate nomor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Simpan Permintaan Medis: menulis ke tabel permintaan_medis dan detail_permintaan_medis
     */
    public function store(Request $request)
    {
        try {
            $payload = $request->json()->all();
            $header = $payload['header'] ?? [];
            $items = $payload['items'] ?? [];

            // Validasi dasar
            if (!is_array($header) || !is_array($items)) {
                return response()->json(['success' => false, 'message' => 'Payload tidak valid'], 422);
            }
            $noPermintaan = trim((string)($header['noPermintaan'] ?? ''));
            $tanggal = trim((string)($header['tanggal'] ?? ''));
            $kdGudangTujuan = trim((string)($header['kdGudangTujuan'] ?? ''));
            $nmGudangTujuan = trim((string)($header['nmGudangTujuan'] ?? ''));
            $kdPetugas = trim((string)($header['kdPetugas'] ?? ''));
            $nmPetugas = trim((string)($header['nmPetugas'] ?? ''));
            $asalPermintaan = trim((string)($header['asalPermintaan'] ?? ''));

            if ($noPermintaan === '' || $tanggal === '') {
                return response()->json(['success' => false, 'message' => 'noPermintaan dan tanggal wajib diisi'], 422);
            }
            $validItems = array_values(array_filter($items, function ($r) {
                $j = (float)($r['jumlah'] ?? 0);
                $kode = trim((string)($r['kodeBarang'] ?? ''));
                return $j > 0 && $kode !== '';
            }));
            if (count($validItems) === 0) {
                return response()->json(['success' => false, 'message' => 'Tidak ada item yang valid untuk disimpan'], 422);
            }

            // Dapatkan kd_gudang_asal dari nama bangsal bila tidak disediakan
            $kdGudangAsal = trim((string)($header['kdGudangAsal'] ?? ''));
            if ($kdGudangAsal === '' && $asalPermintaan !== '') {
                $bangsal = DB::table('bangsal')->select('kd_bangsal')->where('nm_bangsal', $asalPermintaan)->first();
                if ($bangsal) {
                    $kdGudangAsal = $bangsal->kd_bangsal;
                }
            }

            // Pastikan kd_gudang_tujuan terisi; jika belum, derive dari nmGudangTujuan
            if ($kdGudangTujuan === '' && $nmGudangTujuan !== '') {
                $bangsalTujuan = DB::table('bangsal')->select('kd_bangsal')->where('nm_bangsal', $nmGudangTujuan)->first();
                if ($bangsalTujuan) {
                    $kdGudangTujuan = $bangsalTujuan->kd_bangsal;
                }
            }
            if ($kdGudangTujuan === '') {
                return response()->json(['success' => false, 'message' => 'Gudang tujuan (kd_bangsaltujuan) wajib diisi'], 422);
            }

            // Cek duplikasi no_permintaan
            if (DB::table('permintaan_medis')->where('no_permintaan', $noPermintaan)->exists()) {
                return response()->json(['success' => false, 'message' => 'No.Permintaan sudah ada'], 409);
            }

            DB::transaction(function () use ($noPermintaan, $tanggal, $kdGudangAsal, $kdGudangTujuan, $kdPetugas, $validItems) {
                // Insert header sesuai skema tabel:
                DB::table('permintaan_medis')->insert([
                    'no_permintaan'    => $noPermintaan,
                    'kd_bangsal'       => $kdGudangAsal !== '' ? $kdGudangAsal : null,
                    'nip'              => $kdPetugas !== '' ? $kdPetugas : null,
                    'tanggal'          => $tanggal,
                    'status'           => 'Baru',
                    'kd_bangsaltujuan' => $kdGudangTujuan !== '' ? $kdGudangTujuan : null,
                ]);

                // Insert detail
                foreach ($validItems as $it) {
                    DB::table('detail_permintaan_medis')->insert([
                        'no_permintaan' => $noPermintaan,
                        'kode_brng'     => $it['kodeBarang'],
                        'kode_sat'      => $it['satuan'] ?? null,
                        'jumlah'        => (float)$it['jumlah'],
                        'keterangan'    => isset($it['keterangan']) ? str_replace(['\'', '"'], '', (string)$it['keterangan']) : null,
                    ]);
                }
            });

            Log::info('[PermintaanMedis] store saved', [
                'no_permintaan' => $noPermintaan,
                'items_saved' => count($validItems)
            ]);

            return response()->json(['success' => true, 'message' => 'Permintaan disimpan']);
        } catch (\Throwable $e) {
            Log::error('[PermintaanMedis] store error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()], 500);
        }
    }
}