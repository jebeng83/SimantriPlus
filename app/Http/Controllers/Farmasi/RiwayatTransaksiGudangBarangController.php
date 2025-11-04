<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RiwayatTransaksiGudangBarang;
use App\Models\DataBarang;
use App\Models\Bangsal;
use Illuminate\Support\Facades\DB;

class RiwayatTransaksiGudangBarangController extends Controller
{
    public function index(Request $request)
    {
        $query = RiwayatTransaksiGudangBarang::with(['dataBarang', 'bangsal', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan kode barang
        if ($request->filled('kode_brng')) {
            $query->where('kode_brng', 'like', '%' . $request->kode_brng . '%');
        }

        // Filter berdasarkan bangsal
        if ($request->filled('kd_bangsal')) {
            $query->where('kd_bangsal', $request->kd_bangsal);
        }

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        // Filter berdasarkan tipe transaksi
        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        $riwayat = $query->paginate(50);

        return view('farmasi.riwayat-transaksi-gudang', compact('riwayat'));
    }

    public function api(Request $request)
    {
        $query = RiwayatTransaksiGudangBarang::with(['dataBarang', 'bangsal', 'user'])
            ->orderBy('created_at', 'desc');

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        // Filter berdasarkan kode barang
        if ($request->filled('kode_brng')) {
            $query->where('kode_brng', 'like', '%' . $request->kode_brng . '%');
        }

        // Filter berdasarkan bangsal
        if ($request->filled('kd_bangsal')) {
            $query->where('kd_bangsal', $request->kd_bangsal);
        }

        // Filter berdasarkan jenis transaksi
        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        // Filter berdasarkan tipe transaksi
        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        $riwayat = $query->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }

    public function getBangsal()
    {
        $bangsal = Bangsal::select('kd_bangsal', 'nm_bangsal')
            ->orderBy('nm_bangsal')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bangsal
        ]);
    }

    public function detail($id)
    {
        $riwayat = RiwayatTransaksiGudangBarang::with(['dataBarang', 'bangsal', 'user'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $riwayat
        ]);
    }

    public function export(Request $request)
    {
        $query = RiwayatTransaksiGudangBarang::with(['dataBarang', 'bangsal', 'user'])
            ->orderBy('created_at', 'desc');

        // Apply same filters as index
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('created_at', '>=', $request->tanggal_mulai);
        }
        
        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('created_at', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('kode_brng')) {
            $query->where('kode_brng', 'like', '%' . $request->kode_brng . '%');
        }

        if ($request->filled('kd_bangsal')) {
            $query->where('kd_bangsal', $request->kd_bangsal);
        }

        if ($request->filled('jenis_transaksi')) {
            $query->where('jenis_transaksi', $request->jenis_transaksi);
        }

        if ($request->filled('tipe_transaksi')) {
            $query->where('tipe_transaksi', $request->tipe_transaksi);
        }

        $riwayat = $query->get();

        $filename = 'riwayat_transaksi_gudang_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($riwayat) {
            $file = fopen('php://output', 'w');
            
            // Header CSV
            fputcsv($file, [
                'Tanggal',
                'Kode Barang',
                'Nama Barang',
                'Bangsal',
                'No Batch',
                'No Faktur',
                'Jenis Transaksi',
                'Tipe Transaksi',
                'Stok Sebelum',
                'Stok Sesudah',
                'Selisih',
                'Keterangan',
                'User'
            ]);

            foreach ($riwayat as $item) {
                fputcsv($file, [
                    $item->created_at->format('Y-m-d H:i:s'),
                    $item->kode_brng,
                    $item->dataBarang->nama_brng ?? '',
                    $item->bangsal->nm_bangsal ?? '',
                    $item->no_batch,
                    $item->no_faktur,
                    $item->jenis_transaksi,
                    $item->tipe_transaksi,
                    $item->stok_sebelum,
                    $item->stok_sesudah,
                    $item->stok_sesudah - $item->stok_sebelum,
                    $item->keterangan,
                    $item->user->name ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}