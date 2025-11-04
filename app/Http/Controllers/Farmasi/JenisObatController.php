<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class JenisObatController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $perPage = (int) $request->input('perPage', 10);
        if ($perPage <= 0) { $perPage = 10; }

        // If the client requests JSON (for dropdown/autocomplete), return plain data
        if ($request->wantsJson()) {
            $query = DB::table('jenis')
                ->select('kdjns', 'nama');

            if ($q !== '') {
                $query->where(function ($w) use ($q) {
                    $w->where('kdjns', 'like', "%$q%")
                      ->orWhere('nama', 'like', "%$q%")
                      ->orWhere('keterangan', 'like', "%$q%");
                });
            }

            $items = $query->orderBy('kdjns')
                ->limit(50)
                ->get();

            return response()->json([
                'items' => $items,
            ]);
        }

        // Default: render Inertia page
        $query = DB::table('jenis');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kdjns', 'like', "%$q%")
                  ->orWhere('nama', 'like', "%$q%")
                  ->orWhere('keterangan', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('kdjns')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return Inertia::render('farmasi/JenisObat', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kdjns' => ['required', 'string', 'max:4', Rule::unique('jenis', 'kdjns')],
            'nama' => ['required', 'string', 'max:30'],
            'keterangan' => ['nullable', 'string', 'max:50'],
        ]);

        DB::table('jenis')->insert($validated);

        return redirect()->route('farmasi.jenis-obat.index')
            ->with('success', 'Jenis obat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kdjns)
    {
        $exists = DB::table('jenis')->where('kdjns', $kdjns)->exists();
        if (!$exists) {
            return redirect()->route('farmasi.jenis-obat.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:30'],
            'keterangan' => ['nullable', 'string', 'max:50'],
        ]);

        DB::table('jenis')->where('kdjns', $kdjns)->update($validated);

        return redirect()->route('farmasi.jenis-obat.index')
            ->with('success', 'Jenis obat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kdjns)
    {
        DB::table('jenis')->where('kdjns', $kdjns)->delete();

        return redirect()->route('farmasi.jenis-obat.index')
            ->with('success', 'Jenis obat berhasil dihapus.');
    }
}