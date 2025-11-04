<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use App\Models\IndustriFarmasi;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndustriFarmasiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = trim($request->get('q', ''));

        $query = IndustriFarmasi::query();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('kode_industri', 'like', "%$search%")
                  ->orWhere('nama_industri', 'like', "%$search%")
                  ->orWhere('alamat', 'like', "%$search%")
                  ->orWhere('kota', 'like', "%$search%")
                  ->orWhere('no_telp', 'like', "%$search%");
            });
        }

        $perPage = (int) $request->get('perPage', 10);
        $perPage = $perPage > 0 ? $perPage : 10;

        $items = $query->orderBy('kode_industri')->paginate($perPage)->withQueryString();
        // Generate next kode industri with format I0000 based on last number in table
        $nextCode = $this->generateNextKode();

        return Inertia::render('farmasi/industrifarmasi', [
            'items' => $items,
            'filters' => [
                'q' => $search,
                'perPage' => $perPage,
            ],
            'nextCode' => $nextCode,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            // Allow kode_industri to be nullable; if empty, it will be auto-generated in format I0000 -> I0001 -> ...
            'kode_industri' => ['nullable', 'string', 'max:5', 'unique:industrifarmasi,kode_industri'],
            'nama_industri' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:50'],
            'kota' => ['nullable', 'string', 'max:20'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);
        // If kode_industri is not provided, generate automatically based on the last number
        $validated['kode_industri'] = strtoupper($validated['kode_industri'] ?? $this->generateNextKode());

        IndustriFarmasi::create($validated);

        return redirect()->route('farmasi.industri-farmasi.index')
            ->with('success', 'Industri Farmasi berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kode_industri)
    {
        $industri = IndustriFarmasi::findOrFail($kode_industri);

        $validated = $request->validate([
            // Kode tidak diubah saat update untuk menjaga konsistensi primary key
            'nama_industri' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:50'],
            'kota' => ['nullable', 'string', 'max:20'],
            'no_telp' => ['nullable', 'string', 'max:20'],
        ]);

        $industri->update($validated);

        return redirect()->route('farmasi.industri-farmasi.index')
            ->with('success', 'Industri Farmasi berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kode_industri)
    {
        $industri = IndustriFarmasi::findOrFail($kode_industri);
        $industri->delete();

        return redirect()->route('farmasi.industri-farmasi.index')
            ->with('success', 'Industri Farmasi berhasil dihapus.');
    }

    /**
     * Generate next kode_industri in format I0000 by checking the last number in table.
     */
    protected function generateNextKode(): string
    {
        // Get latest numeric sequence from codes starting with 'I'
        $latest = IndustriFarmasi::where('kode_industri', 'like', 'I%')
            ->orderByRaw('CAST(SUBSTRING(kode_industri, 2) AS UNSIGNED) DESC')
            ->select('kode_industri')
            ->first();

        $nextNumber = 0; // default I0000 when no data
        if ($latest && preg_match('/^I(\d{4})$/', $latest->kode_industri, $m)) {
            $nextNumber = ((int) $m[1]) + 1;
        }

        return 'I' . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
    }
}