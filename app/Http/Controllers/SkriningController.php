<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class SkriningController extends Controller
{
    /**
     * Menampilkan halaman skrining minimal
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('skrining.index');
    }

    /**
     * Menyimpan data demografi
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanDemografi(Request $request)
    {
        try {
            // Implementasi penyimpanan data demografi
            return response()->json(['success' => true, 'message' => 'Data demografi berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving demografi: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data demografi'], 500);
        }
    }

    /**
     * Menyimpan data tekanan darah
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanTekananDarah(Request $request)
    {
        try {
            // Implementasi penyimpanan data tekanan darah
            return response()->json(['success' => true, 'message' => 'Data tekanan darah berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving tekanan darah: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data tekanan darah'], 500);
        }
    }

    /**
     * Menyimpan data perilaku merokok
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanPerilakuMerokok(Request $request)
    {
        try {
            // Implementasi penyimpanan data perilaku merokok
            return response()->json(['success' => true, 'message' => 'Data perilaku merokok berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving perilaku merokok: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data perilaku merokok'], 500);
        }
    }

    /**
     * Menyimpan data kesehatan jiwa
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanKesehatanJiwa(Request $request)
    {
        try {
            // Implementasi penyimpanan data kesehatan jiwa
            return response()->json(['success' => true, 'message' => 'Data kesehatan jiwa berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving kesehatan jiwa: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data kesehatan jiwa'], 500);
        }
    }

    /**
     * Menyimpan data skrining
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function simpanSkrining(Request $request)
    {
        try {
            // Implementasi penyimpanan data skrining
            return response()->json(['success' => true, 'message' => 'Data skrining berhasil disimpan']);
        } catch (\Exception $e) {
            Log::error('Error saving skrining: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan data skrining'], 500);
        }
    }

    /**
     * Debug endpoint
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function debug(Request $request)
    {
        return response()->json([
            'message' => 'Debug endpoint aktif',
            'request_data' => $request->all(),
            'timestamp' => now()
        ]);
    }
}