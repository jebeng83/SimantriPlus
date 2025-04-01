<?php

namespace App\Http\Controllers\ANC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DataIbuHamil;
use Illuminate\Support\Facades\DB;
use PDF;
use Carbon\Carbon;

class PartografController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $partografs = DB::table('partograf')
            ->join('data_ibu_hamil', 'partograf.id_hamil', '=', 'data_ibu_hamil.id_hamil')
            ->join('pasien', 'data_ibu_hamil.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select('partograf.*', 'data_ibu_hamil.nama as nama_ibu', 'pasien.nm_pasien')
            ->orderBy('partograf.tanggal_partograf', 'desc')
            ->paginate(10);
            
        return view('anc.partograf.index', compact('partografs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $ibuHamils = DataIbuHamil::where('status', 'Aktif')->get();
        return view('anc.partograf.create', compact('ibuHamils'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'id_hamil' => 'required|exists:data_ibu_hamil,id_hamil',
            'dilatasi_serviks' => 'required|numeric|min:0|max:10',
            'denyut_jantung_janin' => 'required|numeric|min:100|max:200',
            'tekanan_darah_sistole' => 'required|numeric|min:80|max:200',
            'tekanan_darah_diastole' => 'required|numeric|min:40|max:120',
        ]);
        
        try {
            // Ambil data ibu hamil
            $ibuHamil = DataIbuHamil::findOrFail($request->id_hamil);
            
            // Generate ID partograf
            $idPartograf = $this->generateIdPartograf();
            
            // Konversi faktor risiko ke JSON
            $faktorRisiko = [
                'hipertensi' => $request->input('faktor_risiko_hipertensi', false) ? true : false,
                'preeklampsia' => $request->input('faktor_risiko_preeklampsia', false) ? true : false,
                'diabetes' => $request->input('faktor_risiko_diabetes', false) ? true : false,
            ];
            
            $faktorRisikoJson = json_encode($faktorRisiko);
            
            // Data untuk disimpan ke database
            $dataPartograf = [
                'id_partograf' => $idPartograf,
                'no_rawat' => $request->input('no_rawat'),
                'no_rkm_medis' => $ibuHamil->no_rkm_medis,
                'id_hamil' => $ibuHamil->id_hamil,
                'tanggal_partograf' => now(),
                'diperiksa_oleh' => $request->input('diperiksa_oleh', auth()->user()->name ?? 'Petugas'),
                
                // Bagian 1: Informasi Persalinan Awal
                'paritas' => $request->input('paritas'),
                'onset_persalinan' => $request->input('onset_persalinan'),
                'waktu_pecah_ketuban' => $request->input('waktu_pecah_ketuban'),
                'faktor_risiko' => $faktorRisikoJson,
                
                // Bagian 2: Supportive Care
                'pendamping' => $request->input('pendamping'),
                'mobilitas' => $request->input('mobilitas'),
                'manajemen_nyeri' => $request->input('manajemen_nyeri'),
                'intake_cairan' => $request->input('intake_cairan'),
                
                // Bagian 3: Informasi Janin
                'denyut_jantung_janin' => $request->input('denyut_jantung_janin'),
                'kondisi_cairan_ketuban' => $request->input('kondisi_cairan_ketuban'),
                'presentasi_janin' => $request->input('presentasi_janin'),
                'bentuk_kepala_janin' => $request->input('bentuk_kepala_janin'),
                'caput_succedaneum' => $request->input('caput_succedaneum'),
                
                // Bagian 4: Informasi Ibu
                'nadi' => $request->input('nadi'),
                'tekanan_darah_sistole' => $request->input('tekanan_darah_sistole'),
                'tekanan_darah_diastole' => $request->input('tekanan_darah_diastole'),
                'suhu' => $request->input('suhu'),
                'urine_output' => $request->input('urine_output'),
                
                // Bagian 5: Proses Persalinan
                'frekuensi_kontraksi' => $request->input('frekuensi_kontraksi'),
                'durasi_kontraksi' => $request->input('durasi_kontraksi'),
                'dilatasi_serviks' => $request->input('dilatasi_serviks'),
                'penurunan_posisi_janin' => $request->input('penurunan_posisi_janin'),
                
                // Bagian 6: Pengobatan
                'obat_dan_dosis' => $request->input('obat_dan_dosis'),
                'cairan_infus' => $request->input('cairan_infus'),
                
                // Bagian 7: Perencanaan
                'tindakan_yang_direncanakan' => $request->input('tindakan_yang_direncanakan'),
                'hasil_tindakan' => $request->input('hasil_tindakan'),
                'keputusan_bersama' => $request->input('keputusan_bersama'),
                
                'created_at' => now(),
                'updated_at' => now()
            ];
            
            // Simpan data partograf ke database
            DB::table('partograf')->insert($dataPartograf);
            
            return redirect()->route('anc.partograf.show', $idPartograf)
                ->with('success', 'Data partograf berhasil disimpan');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menyimpan data partograf: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $partograf = DB::table('partograf')
            ->join('data_ibu_hamil', 'partograf.id_hamil', '=', 'data_ibu_hamil.id_hamil')
            ->join('pasien', 'data_ibu_hamil.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select('partograf.*', 'data_ibu_hamil.nama as nama_ibu', 'pasien.nm_pasien')
            ->where('partograf.id_partograf', $id)
            ->first();
            
        if (!$partograf) {
            return redirect()->route('anc.partograf.index')
                ->with('error', 'Data partograf tidak ditemukan');
        }
        
        // Ambil riwayat partograf pasien ini
        $riwayatPartograf = DB::table('partograf')
            ->where('id_hamil', $partograf->id_hamil)
            ->where('id_partograf', '!=', $id)
            ->orderBy('tanggal_partograf', 'desc')
            ->get();
            
        // Siapkan data untuk grafik
        $chartData = $this->prepareChartData($partograf->id_hamil);
        
        return view('anc.partograf.show', compact('partograf', 'riwayatPartograf', 'chartData'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $partograf = DB::table('partograf')->where('id_partograf', $id)->first();
        
        if (!$partograf) {
            return redirect()->route('anc.partograf.index')
                ->with('error', 'Data partograf tidak ditemukan');
        }
        
        $ibuHamil = DataIbuHamil::find($partograf->id_hamil);
        
        return view('anc.partograf.edit', compact('partograf', 'ibuHamil'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validasi input
        $validated = $request->validate([
            'dilatasi_serviks' => 'required|numeric|min:0|max:10',
            'denyut_jantung_janin' => 'required|numeric|min:100|max:200',
            'tekanan_darah_sistole' => 'required|numeric|min:80|max:200',
            'tekanan_darah_diastole' => 'required|numeric|min:40|max:120',
        ]);
        
        try {
            // Cek apakah partograf ada
            $partograf = DB::table('partograf')->where('id_partograf', $id)->first();
            
            if (!$partograf) {
                return redirect()->route('anc.partograf.index')
                    ->with('error', 'Data partograf tidak ditemukan');
            }
            
            // Konversi faktor risiko ke JSON
            $faktorRisiko = [
                'hipertensi' => $request->input('faktor_risiko_hipertensi', false) ? true : false,
                'preeklampsia' => $request->input('faktor_risiko_preeklampsia', false) ? true : false,
                'diabetes' => $request->input('faktor_risiko_diabetes', false) ? true : false,
            ];
            
            $faktorRisikoJson = json_encode($faktorRisiko);
            
            // Data untuk update
            $dataPartograf = [
                // Bagian 1: Informasi Persalinan Awal
                'paritas' => $request->input('paritas'),
                'onset_persalinan' => $request->input('onset_persalinan'),
                'waktu_pecah_ketuban' => $request->input('waktu_pecah_ketuban'),
                'faktor_risiko' => $faktorRisikoJson,
                
                // Bagian 2: Supportive Care
                'pendamping' => $request->input('pendamping'),
                'mobilitas' => $request->input('mobilitas'),
                'manajemen_nyeri' => $request->input('manajemen_nyeri'),
                'intake_cairan' => $request->input('intake_cairan'),
                
                // Bagian 3: Informasi Janin
                'denyut_jantung_janin' => $request->input('denyut_jantung_janin'),
                'kondisi_cairan_ketuban' => $request->input('kondisi_cairan_ketuban'),
                'presentasi_janin' => $request->input('presentasi_janin'),
                'bentuk_kepala_janin' => $request->input('bentuk_kepala_janin'),
                'caput_succedaneum' => $request->input('caput_succedaneum'),
                
                // Bagian 4: Informasi Ibu
                'nadi' => $request->input('nadi'),
                'tekanan_darah_sistole' => $request->input('tekanan_darah_sistole'),
                'tekanan_darah_diastole' => $request->input('tekanan_darah_diastole'),
                'suhu' => $request->input('suhu'),
                'urine_output' => $request->input('urine_output'),
                
                // Bagian 5: Proses Persalinan
                'frekuensi_kontraksi' => $request->input('frekuensi_kontraksi'),
                'durasi_kontraksi' => $request->input('durasi_kontraksi'),
                'dilatasi_serviks' => $request->input('dilatasi_serviks'),
                'penurunan_posisi_janin' => $request->input('penurunan_posisi_janin'),
                
                // Bagian 6: Pengobatan
                'obat_dan_dosis' => $request->input('obat_dan_dosis'),
                'cairan_infus' => $request->input('cairan_infus'),
                
                // Bagian 7: Perencanaan
                'tindakan_yang_direncanakan' => $request->input('tindakan_yang_direncanakan'),
                'hasil_tindakan' => $request->input('hasil_tindakan'),
                'keputusan_bersama' => $request->input('keputusan_bersama'),
                
                'updated_at' => now()
            ];
            
            // Update data partograf
            DB::table('partograf')
                ->where('id_partograf', $id)
                ->update($dataPartograf);
            
            return redirect()->route('anc.partograf.show', $id)
                ->with('success', 'Data partograf berhasil diperbarui');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal memperbarui data partograf: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::table('partograf')->where('id_partograf', $id)->delete();
            
            return redirect()->route('anc.partograf.index')
                ->with('success', 'Data partograf berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Gagal menghapus data partograf: ' . $e->getMessage());
        }
    }
    
    /**
     * Display all partograf records by id_hamil.
     *
     * @param  string  $id_hamil
     * @return \Illuminate\Http\Response
     */
    public function showByIdHamil($id_hamil)
    {
        $ibuHamil = DataIbuHamil::findOrFail($id_hamil);
        
        $partografs = DB::table('partograf')
            ->where('id_hamil', $id_hamil)
            ->orderBy('tanggal_partograf', 'desc')
            ->get();
            
        // Siapkan data untuk grafik
        $chartData = $this->prepareChartData($id_hamil);
        
        return view('anc.partograf.by_id_hamil', compact('ibuHamil', 'partografs', 'chartData'));
    }
    
    /**
     * Export partograf to PDF.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function exportPdf($id)
    {
        $partograf = DB::table('partograf')
            ->join('data_ibu_hamil', 'partograf.id_hamil', '=', 'data_ibu_hamil.id_hamil')
            ->join('pasien', 'data_ibu_hamil.no_rkm_medis', '=', 'pasien.no_rkm_medis')
            ->select('partograf.*', 'data_ibu_hamil.nama as nama_ibu', 'pasien.nm_pasien', 'pasien.no_rkm_medis')
            ->where('partograf.id_partograf', $id)
            ->first();
            
        if (!$partograf) {
            return redirect()->back()
                ->with('error', 'Data partograf tidak ditemukan');
        }
        
        // Siapkan data untuk grafik
        $chartData = $this->prepareChartData($partograf->id_hamil);
        
        $pdf = \PDF::loadView('anc.partograf.pdf', compact('partograf', 'chartData'));
        
        return $pdf->download('partograf_' . $partograf->no_rkm_medis . '_' . date('Ymd') . '.pdf');
    }
    
    /**
     * Prepare chart data for partograf visualization.
     *
     * @param  string  $id_hamil
     * @return array
     */
    protected function prepareChartData($id_hamil)
    {
        $partografRecords = DB::table('partograf')
            ->where('id_hamil', $id_hamil)
            ->orderBy('tanggal_partograf', 'asc')
            ->get();
            
        $dilatasi = [];
        $timeLabels = [];
        
        $startTime = null;
        
        foreach ($partografRecords as $index => $record) {
            if ($index === 0) {
                $startTime = Carbon::parse($record->tanggal_partograf);
            }
            
            $currentTime = Carbon::parse($record->tanggal_partograf);
            $hourDiff = $startTime->diffInHours($currentTime);
            
            if ($record->dilatasi_serviks) {
                $dilatasi[$hourDiff] = $record->dilatasi_serviks;
                $timeLabels[] = $hourDiff;
            }
        }
        
        // Sort by time
        ksort($dilatasi);
        
        return [
            'labels' => array_keys($dilatasi),
            'dilatasi' => array_values($dilatasi)
        ];
    }
    
    /**
     * Generate a unique ID for partograf.
     *
     * @return string
     */
    protected function generateIdPartograf()
    {
        $lastId = DB::table('partograf')
            ->where('id_partograf', 'like', 'PART%')
            ->orderBy('id_partograf', 'desc')
            ->value('id_partograf');
            
        if ($lastId) {
            $lastNumber = (int) substr($lastId, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        return 'PART' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
} 