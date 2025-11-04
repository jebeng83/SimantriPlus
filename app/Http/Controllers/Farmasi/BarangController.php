<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    public function search(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            if (strlen($query) < 2) {
                return response()->json([
                    'success' => true,
                    'data' => []
                ]);
            }
            
            $barang = DB::table('databarang')
                ->select(
                    'kode_brng',
                    'nama_brng', 
                    'kode_sat',
                    'h_beli',
                    'stokminimal',
                    'letak_barang'
                )
                ->where(function($q) use ($query) {
                    $q->where('nama_brng', 'LIKE', '%' . $query . '%')
                      ->orWhere('kode_brng', 'LIKE', '%' . $query . '%');
                })
                ->orderBy('nama_brng')
                ->limit(20)
                ->get();
            
            return response()->json([
                'success' => true,
                'data' => $barang
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching barang: ' . $e->getMessage()
            ], 500);
        }
    }
}