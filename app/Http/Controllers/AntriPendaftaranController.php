<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

class AntriPendaftaranController extends Controller
{
    /**
     * GET /api/antripendaftaran/next
     * Ambil nomor antrian berikutnya (status=0) dan sisa antrian untuk tanggal tertentu.
     */
    public function next(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $nextRow = DB::table('antripendaftaran_nomor')
            ->select('nomor')
            ->whereDate('jam', $date)
            ->where('status', '0')
            ->orderBy('jam', 'asc')
            ->first();

        $sisa = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('status', '0')
            ->count();

        return response()->json([
            'success' => true,
            'date' => $date,
            'nomor' => $nextRow->nomor ?? null,
            'sisa' => $sisa,
        ]);
    }

    /**
     * GET /api/antripendaftaran/stats
     * Statistik sederhana untuk tanggal tertentu.
     */
    public function stats(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        $total = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->count();

        $sisa = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('status', '0')
            ->count();

        // Hitung yang sedang dipanggil sesuai skema display (status=2)
        $dipanggil = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('status', '2')
            ->count();

        return response()->json([
            'success' => true,
            'date' => $date,
            'total' => $total,
            'sisa' => $sisa,
            'dipanggil' => $dipanggil,
        ]);
    }

    /**
     * GET /api/antripendaftaran/current
     * Ambil nomor yang sedang dipanggil (status=2). Mengembalikan nomor terakhir berdasarkan waktu.
     */
    public function current(Request $request): JsonResponse
    {
        $date = $request->query('date', now()->toDateString());

        // Ambil nomor terakhir dengan status=2 (dipanggil)
        $row = DB::table('antripendaftaran_nomor')
            ->select('nomor', 'loket', 'jam', 'status')
            ->whereDate('jam', $date)
            ->where('status', '2')
            ->orderBy('jam', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'date' => $date,
            'nomor' => $row->nomor ?? null,
            'loket' => $row->loket ?? null,
            'status' => $row->status ?? null,
        ]);
    }

    /**
     * POST /api/antripendaftaran/call
     * Panggil nomor antrian berikutnya atau nomor yang diberikan.
     * Body: { date: YYYY-MM-DD, loket: string, nomor?: string }
     */
    public function call(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $loket = $request->input('loket', 'LOKET 1');
        $nomor = $request->input('nomor');
        $isRecall = filter_var($request->input('recall', false), FILTER_VALIDATE_BOOLEAN);
        $repeat = filter_var($request->input('repeat', false), FILTER_VALIDATE_BOOLEAN);

        // Jika ini fallback recall melalui endpoint /call,
        // jangan mengubah status di DB. Hanya kembalikan payload.
        if ($isRecall || $repeat) {
            if (!$nomor) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor tidak diberikan untuk recall',
                    'date' => $date,
                ]);
            }

            $sisa = DB::table('antripendaftaran_nomor')
                ->whereDate('jam', $date)
                ->where('status', '0')
                ->count();

            return response()->json([
                'success' => true,
                'date' => $date,
                'nomor' => $nomor,
                'loket' => $loket,
                'sisa' => $sisa,
                'recall' => true,
                'message' => 'Recall via /call (no DB status change)'
            ]);
        }

        // Normal call: ambil nomor berikutnya jika tidak diberikan, lalu ubah status ke 1 (dipanggil oleh loket)
        if (!$nomor) {
            $row = DB::table('antripendaftaran_nomor')
                ->select('nomor')
                ->whereDate('jam', $date)
                ->where('status', '0')
                ->orderBy('jam', 'asc')
                ->first();
            $nomor = $row->nomor ?? null;
        }

        if (!$nomor) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada nomor antrian tersisa',
                'date' => $date,
            ]);
        }

        $updated = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('nomor', $nomor)
            ->update([
                // Sesuaikan dengan aplikasi Java/display yang sudah berjalan:
                // 0 = menunggu, 1 = dipanggil oleh loket (siap untuk display), 2 = sedang diputar di display, 3 = selesai
                // Di sini kita set ke 1 agar display yang aktif dapat menaikkan menjadi 2 saat audio diputar.
                'status' => '1',
                'loket' => $loket,
            ]);

        if ($updated === 0) {
            Log::warning('Panggil antrian: tidak ada baris yang diupdate', ['date' => $date, 'nomor' => $nomor]);
        }

        $sisa = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('status', '0')
            ->count();

        return response()->json([
            'success' => true,
            'date' => $date,
            'nomor' => $nomor,
            'loket' => $loket,
            'sisa' => $sisa,
        ]);
    }

    /**
     * POST /api/antripendaftaran/recall
     * Panggil ulang nomor terakhir yang sudah dipanggil.
     * Mengubah status menjadi 2 agar display memutar ulang audio.
     * Body: { date: YYYY-MM-DD, loket: string, nomor: string }
     */
    public function recall(Request $request): JsonResponse
    {
        $date = $request->input('date', now()->toDateString());
        $loket = $request->input('loket', 'LOKET 1');
        $nomor = $request->input('nomor');

        if (!$nomor) {
            return response()->json([
                'success' => false,
                'message' => 'Nomor terakhir tidak ditemukan',
                'date' => $date,
            ]);
        }

        // Set ulang ke status=1 untuk nomor ini agar display (yang aktif) akan menaikkan ke 2 dan memutar kembali
        $updated = DB::table('antripendaftaran_nomor')
            ->whereDate('jam', $date)
            ->where('nomor', $nomor)
            ->update([
                'status' => '1',
                'loket' => $loket,
            ]);

        if ($updated === 0) {
            Log::warning('Recall antrian: tidak ada baris yang diupdate', ['date' => $date, 'nomor' => $nomor]);
        }

        return response()->json([
            'success' => true,
            'date' => $date,
            'nomor' => $nomor,
            'loket' => $loket,
            'recall' => true,
            'message' => 'Recall succeeded (status set to 1)'
        ]);
    }
}