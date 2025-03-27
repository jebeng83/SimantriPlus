<?php

namespace App\Http\Controllers\ANC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ANC\DataRematri;
use App\Models\Pasien;

class DataRematriController extends Controller
{
    public function index()
    {
        $dataRematri = DataRematri::with('pasien')->latest()->paginate(10);
        return view('anc.data-rematri.index', compact('dataRematri'));
    }

    public function create()
    {
        return view('anc.data-rematri.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'lingkar_lengan' => 'required|numeric',
            'hemoglobin' => 'required|numeric',
            'status_gizi' => 'required',
            'status_anemia' => 'required',
            'pemberian_tablet_tambah_darah' => 'required',
            'konseling_gizi' => 'required',
            'keterangan' => 'nullable'
        ]);

        DataRematri::create($validated);

        return redirect()->route('anc.data-rematri.index')
            ->with('success', 'Data remaja putri berhasil disimpan');
    }

    public function show($id)
    {
        $dataRematri = DataRematri::with('pasien')->findOrFail($id);
        return view('anc.data-rematri.show', compact('dataRematri'));
    }

    public function edit($id)
    {
        $dataRematri = DataRematri::findOrFail($id);
        return view('anc.data-rematri.edit', compact('dataRematri'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'lingkar_lengan' => 'required|numeric',
            'hemoglobin' => 'required|numeric',
            'status_gizi' => 'required',
            'status_anemia' => 'required',
            'pemberian_tablet_tambah_darah' => 'required',
            'konseling_gizi' => 'required',
            'keterangan' => 'nullable'
        ]);

        $dataRematri = DataRematri::findOrFail($id);
        $dataRematri->update($validated);

        return redirect()->route('anc.data-rematri.index')
            ->with('success', 'Data remaja putri berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dataRematri = DataRematri::findOrFail($id);
        $dataRematri->delete();

        return redirect()->route('anc.data-rematri.index')
            ->with('success', 'Data remaja putri berhasil dihapus');
    }

    public function getDataPasien($nik)
    {
        $pasien = Pasien::where('no_ktp', $nik)->first();
        return response()->json($pasien);
    }
} 