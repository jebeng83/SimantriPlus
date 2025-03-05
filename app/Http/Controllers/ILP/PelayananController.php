<?php

namespace App\Http\Controllers\ILP;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\IlpDewasa;
use App\Models\Posyandu;
use Session;
use Barryvdh\DomPDF\Facade\Pdf;

class PelayananController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('loginauth');
    }

    /**
     * Show the ILP pelayanan page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $kd_poli = session()->get('kd_poli');
        $kd_dokter = session()->get('username');
        
        // Ambil data dari tabel ilp_dewasa dengan semua kolom
        $pemeriksaan = IlpDewasa::select(
            'ilp_dewasa.*',
            'pasien.nm_pasien',
            'pegawai.nama as nama_dokter'
        )
        ->join('pasien', 'ilp_dewasa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
        ->leftJoin('pegawai', 'ilp_dewasa.nip', '=', 'pegawai.nik')
        ->orderBy('ilp_dewasa.tanggal', 'desc')
        ->get()
        ->map(function($item, $index) {
            return [
                'no' => $index + 1,
                'id' => $item->id,
                'no_rawat' => $item->no_rawat,
                'no_rm' => $item->no_rkm_medis,
                'nama_pasien' => $item->nm_pasien,
                'no_ktp' => $item->no_ktp,
                'data_posyandu' => $item->data_posyandu,
                'nip' => $item->nip,
                'nama_dokter' => $item->nama_dokter,
                'tanggal' => date('d-m-Y', strtotime($item->tanggal)),
                'tgl_lahir' => $item->tgl_lahir ? date('d-m-Y', strtotime($item->tgl_lahir)) : '-',
                'stts_nikah' => $item->stts_nikah,
                'jk' => $item->jk,
                'no_kk' => $item->no_kk,
                'no_tlp' => $item->no_tlp,
                'pekerjaan' => $item->pekerjaan,
                'riwayat_diri_sendiri' => $item->riwayat_diri_sendiri,
                'riwayat_keluarga' => $item->riwayat_keluarga,
                'merokok' => $item->merokok,
                'konsumsi_tinggi' => $item->konsumsi_tinggi,
                'berat_badan' => $item->berat_badan,
                'tinggi_badan' => $item->tinggi_badan,
                'imt' => $item->imt,
                'lp' => $item->lp,
                'td' => $item->td,
                'gula_darah' => $item->gula_darah,
                'metode_mata' => $item->metode_mata,
                'hasil_mata' => $item->hasil_mata,
                'tes_berbisik' => $item->tes_berbisik,
                'gigi' => $item->gigi,
                'kesehatan_jiwa' => $item->kesehatan_jiwa,
                'tbc' => $item->tbc,
                'fungsi_hari' => $item->fungsi_hari,
                'status_tt' => $item->status_tt,
                'penyakit_lain_catin' => $item->penyakit_lain_catin,
                'kanker_payudara' => $item->kanker_payudara,
                'iva_test' => $item->iva_test,
                'resiko_jantung' => $item->resiko_jantung,
                'gds' => $item->gds,
                'asam_urat' => $item->asam_urat,
                'kolesterol' => $item->kolesterol,
                'trigliserida' => $item->trigliserida,
                'charta' => $item->charta,
                'ureum' => $item->ureum,
                'kreatinin' => $item->kreatinin,
                'resiko_kanker_usus' => $item->resiko_kanker_usus,
                'skor_puma' => $item->skor_puma,
                'skilas' => $item->skilas,
                'status' => $item->status_pemeriksaan ?? 'Menunggu'
            ];
        });
        
        // Ambil data posyandu untuk dropdown filter
        $data_posyandu = Posyandu::orderBy('nama_posyandu', 'asc')->get();
        
        return view('ilp.pelayanan', [
            'nm_dokter' => $this->getDokter($kd_dokter),
            'pemeriksaan' => $pemeriksaan,
            'data_posyandu' => $data_posyandu
        ]);
    }
    
    /**
     * Update the specified ILP Dewasa record.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'tekanan_darah' => 'required|string',
            'status_pemeriksaan' => 'required|string',
            'kesimpulan' => 'nullable|string'
        ]);
        
        try {
            // Update data ILP Dewasa
            $ilpDewasa = IlpDewasa::findOrFail($id);
            
            // Hitung IMT (Indeks Massa Tubuh)
            $bb = $request->berat_badan;
            $tb = $request->tinggi_badan / 100; // konversi cm ke m
            $imt = round($bb / ($tb * $tb), 2);
            
            $ilpDewasa->berat_badan = $bb;
            $ilpDewasa->tinggi_badan = $request->tinggi_badan;
            $ilpDewasa->imt = $imt;
            $ilpDewasa->td = $request->tekanan_darah;
            $ilpDewasa->status_pemeriksaan = $request->status_pemeriksaan;
            $ilpDewasa->skilas = $request->kesimpulan;
            $ilpDewasa->save();
            
            return redirect()->route('ilp.pelayanan')->with('success', 'Data pemeriksaan ILP Dewasa berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->route('ilp.pelayanan')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
    
    private function getDokter($kd_dokter)
    {
        $dokter = DB::table('pegawai')->where('nik', $kd_dokter)->first();
        return $dokter ? $dokter->nama : 'Dokter';
    }

    /**
     * Generate PDF document for ILP Dewasa record.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cetakPdf($id)
    {
        try {
            // Ambil data pemeriksaan berdasarkan ID
            $pemeriksaan = IlpDewasa::select(
                'ilp_dewasa.*',
                'pasien.nm_pasien',
                'pegawai.nama as nama_dokter'
            )
            ->join('pasien', 'ilp_dewasa.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->leftJoin('pegawai', 'ilp_dewasa.nip', '=', 'pegawai.nik')
            ->where('ilp_dewasa.id', $id)
            ->first();
            
            if (!$pemeriksaan) {
                return redirect()->route('ilp.pelayanan')->with('error', 'Data pemeriksaan tidak ditemukan');
            }
            
            // Ambil data pasien
            $pasien = DB::table('pasien')
                ->where('no_rkm_medis', $pemeriksaan->no_rkm_medis)
                ->first();
            
            // Format data untuk tampilan PDF
            $data = [
                'id' => $pemeriksaan->id,
                'no_rawat' => $pemeriksaan->no_rawat,
                'no_rm' => $pemeriksaan->no_rkm_medis,
                'nama_pasien' => $pemeriksaan->nm_pasien,
                'tanggal' => date('d-m-Y', strtotime($pemeriksaan->tanggal)),
                'jk' => $pemeriksaan->jk == 'L' ? 'Laki-laki' : ($pemeriksaan->jk == 'P' ? 'Perempuan' : '-'),
                'tgl_lahir' => $pemeriksaan->tgl_lahir ? date('d-m-Y', strtotime($pemeriksaan->tgl_lahir)) : '-',
                'no_ktp' => $pemeriksaan->no_ktp ?? '-',
                'data_posyandu' => $pemeriksaan->data_posyandu ?? '-',
                'no_kk' => $pemeriksaan->no_kk ?? '-',
                'no_tlp' => $pasien->no_tlp ?? '-',
                'stts_nikah' => $pemeriksaan->stts_nikah ?? '-',
                'pekerjaan' => $pemeriksaan->pekerjaan ?? '-',
                'riwayat_diri_sendiri' => $pemeriksaan->riwayat_diri_sendiri ?? '-',
                'riwayat_keluarga' => $pemeriksaan->riwayat_keluarga ?? '-',
                'merokok' => $pemeriksaan->merokok ?? '-',
                'konsumsi_tinggi' => $pemeriksaan->konsumsi_tinggi ?? '-',
                'berat_badan' => $pemeriksaan->berat_badan ?? '-',
                'tinggi_badan' => $pemeriksaan->tinggi_badan ?? '-',
                'imt' => $pemeriksaan->imt ?? '-',
                'lp' => $pemeriksaan->lp ?? '-',
                'td' => $pemeriksaan->td ?? '-',
                'gula_darah' => $pemeriksaan->gula_darah ?? '-',
                'metode_mata' => $pemeriksaan->metode_mata ?? '-',
                'hasil_mata' => $pemeriksaan->hasil_mata ?? '-',
                'tes_berbisik' => $pemeriksaan->tes_berbisik ?? '-',
                'gigi' => $pemeriksaan->gigi ?? '-',
                'kesehatan_jiwa' => $pemeriksaan->kesehatan_jiwa ?? '-',
                'tbc' => $pemeriksaan->tbc ?? '-',
                'fungsi_hari' => $pemeriksaan->fungsi_hari ?? '-',
                'status_tt' => $pemeriksaan->status_tt ?? '-',
                'penyakit_lain_catin' => $pemeriksaan->penyakit_lain_catin ?? '-',
                'kanker_payudara' => $pemeriksaan->kanker_payudara ?? '-',
                'iva_test' => $pemeriksaan->iva_test ?? '-',
                'resiko_jantung' => $pemeriksaan->resiko_jantung ?? '-',
                'gds' => $pemeriksaan->gds ?? '-',
                'asam_urat' => $pemeriksaan->asam_urat ?? '-',
                'kolesterol' => $pemeriksaan->kolesterol ?? '-',
                'trigliserida' => $pemeriksaan->trigliserida ?? '-',
                'charta' => $pemeriksaan->charta ?? '-',
                'ureum' => $pemeriksaan->ureum ?? '-',
                'kreatinin' => $pemeriksaan->kreatinin ?? '-',
                'resiko_kanker_usus' => $pemeriksaan->resiko_kanker_usus ?? '-',
                'skor_puma' => $pemeriksaan->skor_puma ?? '-',
                'skilas' => $pemeriksaan->skilas ?? '-',
                'nama_dokter' => $pemeriksaan->nama_dokter ?? 'Dokter',
                'status' => $pemeriksaan->status_pemeriksaan ?? 'Menunggu'
            ];
            
            // Generate PDF menggunakan DOMPDF
            $pdf = PDF::loadView('ilp.cetak_pdf', $data);
            
            // Set opsi PDF yang diperlukan
            $pdf->setPaper('A4', 'portrait');
            
            // Return PDF untuk ditampilkan di browser
            return $pdf->stream('Hasil_Pemeriksaan_ILP_' . $data['no_rm'] . '.pdf');
            
        } catch (\Exception $e) {
            return redirect()->route('ilp.pelayanan')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 