<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\IlpDewasa;
use App\Helpers\UrlHelper;
use Carbon\Carbon;

class IlpDewasaController extends Controller
{
    /**
     * Menampilkan form ILP Dewasa
     *
     * @param string $noRawat
     * @return \Illuminate\View\View
     */
    public function index($noRawat)
    {
        try {
            // Log untuk debugging
            Log::info('Mengakses ILP Dewasa dengan noRawat: ' . $noRawat);
            
            // Ambil data pasien dan reg_periksa
            $pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'reg_periksa.no_rawat',
                    'reg_periksa.no_rkm_medis',
                    'pasien.nm_pasien',
                    'pasien.no_ktp',
                    'pasien.jk',
                    'pasien.tmp_lahir',
                    'pasien.tgl_lahir',
                    'pasien.nm_ibu',
                    'pasien.alamat',
                    'pasien.gol_darah',
                    'pasien.pekerjaan',
                    'pasien.stts_nikah',
                    'pasien.agama',
                    'pasien.tgl_daftar',
                    'pasien.no_tlp',
                    'pasien.umur',
                    'pasien.pnd',
                    'pasien.keluarga',
                    'pasien.namakeluarga',
                    'pasien.kd_pj',
                    'pasien.no_peserta',
                    'pasien.no_kk'
                )
                ->where('reg_periksa.no_rawat', $noRawat)
                ->first();
                
            if (!$pasien) {
                Log::error('Data pasien tidak ditemukan untuk noRawat: ' . $noRawat);
                return redirect()->route('ralan.pasien')->with('error', 'Data pasien tidak ditemukan');
            }
            
            Log::info('Data pasien ditemukan: ' . $pasien->nm_pasien . ', No KTP: ' . $pasien->no_ktp);
            
            // Cek apakah data ILP Dewasa sudah ada
            $ilpDewasa = IlpDewasa::where('no_rawat', $noRawat)->first();
            if ($ilpDewasa) {
                Log::info('Data ILP Dewasa sudah ada untuk no_rawat: ' . $noRawat);
            } else {
                Log::info('Data ILP Dewasa belum ada untuk no_rawat: ' . $noRawat);
            }
            
            return view('ilp.dewasa.form', [
                'noRawat' => $noRawat,
                'pasien' => $pasien
            ]);
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasaController@index: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('ralan.pasien')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menyimpan data ILP Dewasa
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        try {
            // Log untuk debugging
            Log::info('Menyimpan data ILP Dewasa dengan no_rawat: ' . $request->no_rawat);
            
            // Validasi input
            $validated = $request->validate([
                'no_rawat' => 'required|string|max:17',
                'berat_badan' => 'nullable|string|max:5',
                'tinggi_badan' => 'nullable|string|max:5',
                'imt' => 'nullable|string|max:5',
                'lp' => 'nullable|string|max:4',
                'td' => 'nullable|string|max:8',
                'gula_darah' => 'nullable|string|max:4',
                'metode_mata' => 'nullable|string',
                'hasil_mata' => 'nullable|string',
                'tes_berbisik' => 'nullable|string',
                'gigi' => 'nullable|string',
                'kesehatan_jiwa' => 'nullable|string',
                'tbc' => 'nullable|string|max:50',
                'fungsi_hari' => 'nullable|string',
                'status_tt' => 'nullable|string',
                'penyakit_lain_catin' => 'nullable|string',
                'kanker_payudara' => 'nullable|string',
                'iva_test' => 'nullable|string',
                'resiko_jantung' => 'nullable|string',
                'gds' => 'nullable|string|max:5',
                'asam_urat' => 'nullable|string|max:5',
                'kolesterol' => 'nullable|string|max:5',
                'trigliserida' => 'nullable|string|max:5',
                'charta' => 'nullable|string',
                'ureum' => 'nullable|string|max:6',
                'kreatinin' => 'nullable|string|max:6',
                'resiko_kanker_usus' => 'nullable|string',
                'skor_puma' => 'nullable|string',
                'skilas' => 'nullable|string|max:100',
                'riwayat_diri_sendiri' => 'nullable|string',
                'riwayat_keluarga' => 'nullable|string',
                'merokok' => 'nullable|string',
                'konsumsi_tinggi' => 'nullable|string|max:25',
            ]);
            
            // Ambil data pasien untuk field yang tidak diinput
            $pasien = DB::table('reg_periksa')
                ->join('pasien', 'reg_periksa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
                ->select(
                    'pasien.tgl_lahir',
                    'pasien.stts_nikah',
                    'pasien.jk',
                    'pasien.no_kk',
                    'pasien.no_tlp',
                    'pasien.pekerjaan'
                )
                ->where('reg_periksa.no_rawat', $request->no_rawat)
                ->first();
                
            if (!$pasien) {
                Log::warning('Data pasien tidak ditemukan untuk no_rawat: ' . $request->no_rawat);
            } else {
                Log::info('Data pasien ditemukan untuk no_rawat: ' . $request->no_rawat);
            }
            
            // Cek apakah data sudah ada
            $ilpDewasa = IlpDewasa::where('no_rawat', $request->no_rawat)->first();
            
            if ($ilpDewasa) {
                // Update data yang sudah ada
                $ilpDewasa->update(array_merge($validated, [
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                ]));
                
                Log::info('Data ILP Dewasa berhasil diupdate untuk no_rawat: ' . $request->no_rawat);
            } else {
                // Buat data baru
                $ilpDewasa = IlpDewasa::create(array_merge($validated, [
                    'tanggal' => Carbon::now()->format('Y-m-d'),
                    'tgl_lahir' => $pasien->tgl_lahir ?? null,
                    'stts_nikah' => $pasien->stts_nikah ?? '',
                    'jk' => $pasien->jk ?? '',
                    'no_kk' => $pasien->no_kk ?? '',
                    'no_tlp' => $pasien->no_tlp ?? '',
                    'pekerjaan' => $pasien->pekerjaan ?? '',
                ]));
                
                Log::info('Data ILP Dewasa berhasil dibuat untuk no_rawat: ' . $request->no_rawat);
            }
            
            return redirect()->route('ilp.pelayanan')->with('success', 'Data ILP Dewasa berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasaController@store: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    /**
     * Menghapus data ILP Dewasa
     *
     * @param string $noRawat
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($noRawat)
    {
        try {
            // Log untuk debugging
            Log::info('Menghapus ILP Dewasa dengan noRawat: ' . $noRawat);
            
            $ilpDewasa = IlpDewasa::where('no_rawat', $noRawat)->first();
            
            if ($ilpDewasa) {
                $ilpDewasa->delete();
                Log::info('Data ILP Dewasa berhasil dihapus untuk no_rawat: ' . $noRawat);
                return redirect()->route('ilp.pelayanan')->with('success', 'Data ILP Dewasa berhasil dihapus');
            }
            
            Log::warning('Data ILP Dewasa tidak ditemukan untuk no_rawat: ' . $noRawat);
            return redirect()->route('ilp.pelayanan')->with('error', 'Data ILP Dewasa tidak ditemukan');
        } catch (\Exception $e) {
            Log::error('Error pada IlpDewasaController@destroy: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            return redirect()->route('ilp.pelayanan')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
