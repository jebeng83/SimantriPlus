<?php

namespace App\Http\Controllers\ANC;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ANC\DataIbuNifas;
use App\Models\Pasien;

class DataIbuNifasController extends Controller
{
    public function index()
    {
        $dataIbuNifas = DataIbuNifas::with('pasien')->latest()->paginate(10);
        return view('anc.data-ibu-nifas.index', compact('dataIbuNifas'));
    }

    public function create()
    {
        return view('anc.data-ibu-nifas.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'tanggal_persalinan' => 'required|date',
            'tempat_persalinan' => 'required',
            'penolong_persalinan' => 'required',
            'cara_persalinan' => 'required',
            'kondisi_ibu' => 'required',
            'kondisi_bayi' => 'required',
            'berat_bayi' => 'required|numeric',
            'tinggi_bayi' => 'required|numeric',
            'komplikasi' => 'nullable',
            'asi_eksklusif' => 'required',
            'kb_pasca_salin' => 'required',
            'keterangan' => 'nullable'
        ]);

        DataIbuNifas::create($validated);

        return redirect()->route('anc.data-ibu-nifas.index')
            ->with('success', 'Data ibu nifas berhasil disimpan');
    }

    public function show($id)
    {
        $dataIbuNifas = DataIbuNifas::with('pasien')->findOrFail($id);
        return view('anc.data-ibu-nifas.show', compact('dataIbuNifas'));
    }

    public function edit($id)
    {
        $dataIbuNifas = DataIbuNifas::findOrFail($id);
        return view('anc.data-ibu-nifas.edit', compact('dataIbuNifas'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'no_rm' => 'required',
            'tanggal_kunjungan' => 'required|date',
            'tanggal_persalinan' => 'required|date',
            'tempat_persalinan' => 'required',
            'penolong_persalinan' => 'required',
            'cara_persalinan' => 'required',
            'kondisi_ibu' => 'required',
            'kondisi_bayi' => 'required',
            'berat_bayi' => 'required|numeric',
            'tinggi_bayi' => 'required|numeric',
            'komplikasi' => 'nullable',
            'asi_eksklusif' => 'required',
            'kb_pasca_salin' => 'required',
            'keterangan' => 'nullable'
        ]);

        $dataIbuNifas = DataIbuNifas::findOrFail($id);
        $dataIbuNifas->update($validated);

        return redirect()->route('anc.data-ibu-nifas.index')
            ->with('success', 'Data ibu nifas berhasil diperbarui');
    }

    public function destroy($id)
    {
        $dataIbuNifas = DataIbuNifas::findOrFail($id);
        $dataIbuNifas->delete();

        return redirect()->route('anc.data-ibu-nifas.index')
            ->with('success', 'Data ibu nifas berhasil dihapus');
    }

    public function getDataPasien($nik)
    {
        $pasien = Pasien::where('no_ktp', $nik)->first();
        return response()->json($pasien);
    }
} 