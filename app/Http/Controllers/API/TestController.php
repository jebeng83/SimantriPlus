<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataIbuHamil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestController extends Controller
{
    public function testDataIbuHamil()
    {
        try {
            // Periksa struktur tabel data_ibu_hamil
            $columns = Schema::getColumnListing('data_ibu_hamil');
            
            // Periksa sequence
            $sequence = DB::table('data_ibu_hamil_sequence')->first();
            
            // Buat data dummy untuk testing
            $dataIbuHamil = new DataIbuHamil();
            $dataIbuHamil->nik = '1234567890123456';
            $dataIbuHamil->no_rkm_medis = 'RM123456';
            $dataIbuHamil->kehamilan_ke = '1';
            $dataIbuHamil->tgl_lahir = '1990-01-01';
            $dataIbuHamil->nomor_kk = '1234567890123456';
            $dataIbuHamil->nama = 'Test User';
            $dataIbuHamil->kepemilikan_buku_kia = true;
            $dataIbuHamil->status = 'Hamil';
            $dataIbuHamil->provinsi = 'Jawa Tengah';
            $dataIbuHamil->kabupaten = 'Karanganyar';
            $dataIbuHamil->kecamatan = 'Kerjo';
            $dataIbuHamil->puskesmas = 'Kerjo';
            $dataIbuHamil->desa = 'Tawangmangu';
            $dataIbuHamil->data_posyandu = 'POSYANDU 1';
            $dataIbuHamil->alamat_lengkap = 'Jl. Test No. 123';
            $dataIbuHamil->save();
            
            // Ambil data yang baru dibuat
            $newData = DataIbuHamil::find($dataIbuHamil->id_hamil);
            
            // Hapus data test
            $dataIbuHamil->delete();
            
            // Sequence baru
            $newSequence = DB::table('data_ibu_hamil_sequence')->first();
            
            return response()->json([
                'columns' => $columns,
                'sequence_before' => $sequence,
                'new_data' => $newData,
                'sequence_after' => $newSequence,
                'primary_key' => $dataIbuHamil->getKeyName(),
                'incrementing' => $dataIbuHamil->getIncrementing(),
                'key_type' => $dataIbuHamil->getKeyType()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Terjadi kesalahan pada koneksi database',
                'message' => 'Silakan coba lagi dalam beberapa saat',
                'detail' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
