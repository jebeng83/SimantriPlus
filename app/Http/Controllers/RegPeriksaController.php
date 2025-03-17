<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RegPeriksaController extends Controller
{
    public function create($no_rkm_medis)
    {
        try {
            \Log::info('Membuka form registrasi periksa untuk pasien: ' . $no_rkm_medis);
            
            // Ambil data pasien dengan join ke penjab
            $pasien = DB::table('pasien')
                ->leftJoin('penjab', 'pasien.kd_pj', '=', 'penjab.kd_pj')
                ->leftJoin('data_posyandu', 'pasien.data_posyandu', '=', 'data_posyandu.nama_posyandu')
                ->select(
                    'pasien.*', 
                    'penjab.png_jawab as penjab_pasien',
                    'data_posyandu.nama_posyandu',
                    'data_posyandu.desa'
                )
                ->where('no_rkm_medis', $no_rkm_medis)
                ->first();

            if (!$pasien) {
                \Log::error('Pasien tidak ditemukan: ' . $no_rkm_medis);
                return redirect()->back()->with('error', 'Data pasien tidak ditemukan');
            }
            
            // Ambil data dokter
            $dokter = DB::table('dokter')->get();
            
            // Ambil data poliklinik
            $poliklinik = DB::table('poliklinik')->get();
            
            // Ambil data penjamin
            $penjab = DB::table('penjab')->get();

            // Ambil data posyandu
            $posyandu = DB::table('data_posyandu')
                ->select('nama_posyandu', 'desa')
                ->orderBy('nama_posyandu')
                ->get();
            
            return view('regperiksa.create', [
                'pasien' => $pasien,
                'dokter' => $dokter,
                'poliklinik' => $poliklinik,
                'penjab' => $penjab,
                'posyandu' => $posyandu
            ]);
        } catch (\Exception $e) {
            \Log::error('Error pada create registrasi: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        \Log::info('Mencoba menyimpan registrasi periksa', $request->all());
        
        try {
            $this->validate($request, [
                'no_reg' => 'required',
                'kd_dokter' => 'required',
                'kd_poli' => 'required',
                'kd_pj' => 'required',
                'no_rkm_medis' => 'required'
            ]);

            // Generate nomor rawat dengan format: tahun/bulan/tanggal/nomor urut
            $today = date('Y/m/d');
            $lastRawat = DB::table('reg_periksa')
                ->where('tgl_registrasi', date('Y-m-d'))
                ->orderBy('no_rawat', 'desc')
                ->first();

            if ($lastRawat) {
                $lastNumber = (int) substr($lastRawat->no_rawat, -6);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            $no_rawat = $today . '/' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
            
            \Log::info('Nomor rawat yang dibuat: ' . $no_rawat);

            $data = [
                'no_reg' => $request->no_reg,
                'no_rawat' => $no_rawat,
                'tgl_registrasi' => date('Y-m-d'),
                'jam_reg' => date('H:i:s'),
                'kd_dokter' => $request->kd_dokter,
                'no_rkm_medis' => $request->no_rkm_medis,
                'kd_poli' => $request->kd_poli,
                'p_jawab' => $request->p_jawab,
                'almt_pj' => $request->almt_pj,
                'hubunganpj' => $request->hubunganpj,
                'biaya_reg' => $request->biaya_reg ?? 0,
                'stts' => 'Belum',
                'stts_daftar' => $request->stts_daftar ?? 'Lama',
                'status_lanjut' => 'Ralan',
                'kd_pj' => $request->kd_pj,
                'umurdaftar' => $request->umurdaftar,
                'sttsumur' => $request->sttsumur ?? 'Th',
                'status_bayar' => 'Belum Bayar',
                'status_poli' => 'Lama'
            ];

            \Log::info('Data yang akan disimpan:', $data);

            DB::beginTransaction();
            
            DB::table('reg_periksa')->insert($data);
            
            DB::commit();

            \Log::info('Registrasi berhasil disimpan untuk no_rawat: ' . $no_rawat);

            return response()->json([
                'success' => true,
                'message' => 'Registrasi berhasil disimpan',
                'no_rawat' => $no_rawat
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal: ' . implode(', ', $e->errors())
            ], 422);
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Error menyimpan registrasi: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function generateNoReg($kd_poli, $tgl_registrasi)
    {
        try {
            \Log::info('Generating nomor registrasi untuk poli: ' . $kd_poli . ' tanggal: ' . $tgl_registrasi);
            
            // Ambil nomor urut terakhir berdasarkan dokter dan tanggal
            $lastReg = DB::table('reg_periksa')
                ->where('kd_dokter', $kd_poli) // Menggunakan kd_dokter sebagai filter
                ->where('tgl_registrasi', $tgl_registrasi)
                ->orderBy(DB::raw('CAST(no_reg AS UNSIGNED)'), 'desc')
                ->first();

            if ($lastReg) {
                $nextReg = str_pad((int)$lastReg->no_reg + 1, 3, '0', STR_PAD_LEFT);
                \Log::info('Nomor registrasi terakhir: ' . $lastReg->no_reg . ', next: ' . $nextReg);
                return $nextReg;
            }

            \Log::info('Belum ada registrasi sebelumnya, menggunakan nomor awal: 001');
            return '001';
        } catch (\Exception $e) {
            \Log::error('Error generating no_reg: ' . $e->getMessage());
            return '001';
        }
    }
} 