<?php

namespace App\Http\Controllers\ANC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ANC\DataBalitaSakit;
use App\Models\Pasien;

class DataBalitaSakitController extends Controller
{
    public function index()
    {
        $dataBalitaSakit = DataBalitaSakit::with('pasien')->latest()->paginate(10);
        return view('anc.data-balita-sakit.index', compact('dataBalitaSakit'));
    }

    public function create()
    {
        return view('anc.data-balita-sakit.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'keluhan' => 'required',
            'diagnosa' => 'required',
            'tindakan' => 'required',
            'obat' => 'required',
            'keterangan' => 'nullable'
        ]);

        DataBalitaSakit::create($validated);

        return redirect()->route('anc.data-balita-sakit.index')
            ->with('success', 'Data balita sakit berhasil disimpan');
    }

    public function show($id)
    {
        $dataBalitaSakit = DataBalitaSakit::with('pasien')->findOrFail($id);
        return view('anc.data-balita-sakit.show', compact('dataBalitaSakit'));
    }

    public function edit($id)
    {
        $dataBalitaSakit = DataBalitaSakit::findOrFail($id);
        return view('anc.data-balita-sakit.edit', compact('dataBalitaSakit'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'berat_badan' => 'required|numeric',
            'tinggi_badan' => 'required|numeric',
            'keluhan' => 'required',
            'diagnosa' => 'required',
            'tindakan' => 'required',
            'obat' => 'required',
            'keterangan' => 'nullable'
        ]);

        $dataBalitaSakit = DataBalitaSakit::findOrFail($id);
        $dataBalitaSakit->update($validated);

        return redirect()->route('anc.data-balita-sakit.index')
            ->with('success', 'Data balita sakit berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dataBalitaSakit = DataBalitaSakit::findOrFail($id);
        $dataBalitaSakit->delete();

        return redirect()->route('anc.data-balita-sakit.index')
            ->with('success', 'Data balita sakit berhasil dihapus');
    }

    public function getDataPasien($nik)
    {
        $pasien = Pasien::where('no_ktp', $nik)->first();
        return response()->json($pasien);
    }
} 