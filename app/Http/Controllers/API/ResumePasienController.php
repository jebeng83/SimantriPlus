<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\EnkripsiData;

class ResumePasienController extends Controller
{
    use EnkripsiData;

    public function postResume(Request $request, $noRawat)
    {
        $keluhan = $request->get('keluhan_utama');
        $diagnosa = $request->get('diagnosa_utama');
        $terapi = $request->get('terapi');
        $prosedur = $request->get('prosedur_utama');
        $jalannyaPenyakit = $request->get('jalannya_penyakit');
        $pemeriksaanPenunjang = $request->get('pemeriksaan_penunjang');
        $hasilLaborat = $request->get('hasil_laborat');
        $kondisiPulang = $request->get('kondisi_pulang');
        $dokter = session()->get('username');
        $noRawat = $this->decryptData($noRawat);

        // $request->validate([
        //     'keluhan' => 'required',
        //     'diagnosa' => 'required',
        //     'terapi' => 'required',
        //     'prosedur' => 'required',
        //     'jalannya_penyakit' => 'required',
        //     'pemeriksaan_penunjang' => 'required',
        //     'hasil_laborat' => 'required',
        //     'kondisi_pulang' => 'required',
        // ]);

        try {
            DB::beginTransaction();
            $cek = DB::table('resume_pasien')->where('no_rawat', $noRawat)->count('no_rawat');
            if ($cek > 0) {
                DB::table('resume_pasien')->where('no_rawat', $noRawat)->update([
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                    'jalannya_penyakit' => $jalannyaPenyakit,
                    'pemeriksaan_penunjang' => $pemeriksaanPenunjang,
                    'hasil_laborat' => $hasilLaborat,
                    'kondisi_pulang' => $kondisiPulang,
                ]);
                DB::commit();
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Resume medis berhasil diperbarui'
                ]);
            } else {
                DB::table('resume_pasien')->insert([
                    'no_rawat' => $noRawat,
                    'kd_dokter' => $dokter,
                    'keluhan_utama' => $keluhan,
                    'diagnosa_utama' => $diagnosa,
                    'obat_pulang' => $terapi,
                    'prosedur_utama' => $prosedur,
                    'jalannya_penyakit' => $jalannyaPenyakit,
                    'pemeriksaan_penunjang' => $pemeriksaanPenunjang,
                    'hasil_laborat' => $hasilLaborat,
                    'kondisi_pulang' => $kondisiPulang,
                ]);

                DB::commit();
                return response()->json([
                    'status' => 'sukses',
                    'pesan' => 'Resume medis berhasil ditambahkan'
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            DB::rollback();
            return response()->json([
                'status' => 'gagal',
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function getKeluhanUtama($noRawat)
    {
        $noRawat = $this->decryptData($noRawat);

        try {
            $cek = DB::table('reg_periksa')->where('no_rawat', $noRawat)->first();
            if ($cek->status_lanjut == 'Ralan') {
                $data = DB::table('pemeriksaan_ralan')->where('no_rawat', $noRawat)->select('keluhan')->first();
            } else {
                $data = DB::table('pemeriksaan_ranap')->where('no_rawat', $noRawat)->select('keluhan')->first();
            }
            return response()->json([
                'status' => 'sukses',
                'data' => $data->keluhan
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return response()->json([
                'status' => 'gagal',
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function getDiagnosa(Request $request)
    {
        $q = $request->get('q');
        $que = '%' . $q . '%';

        $data = DB::table('penyakit')
            ->where('kd_penyakit', 'like', $que)
            ->orWhere('nm_penyakit', 'like', $que)
            ->get();
        return response()->json($data, 200);
    }
}
