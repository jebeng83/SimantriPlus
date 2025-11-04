<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class GolonganBarangController
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $q = trim($request->input('q', ''));
        $perPage = (int) $request->input('perPage', 10);
        if ($perPage <= 0) { $perPage = 10; }

        // Jika klien meminta JSON (dropdown/selector), kembalikan data sederhana
        if ($request->wantsJson()) {
            $query = DB::table('golongan_barang')
                ->select('kode', 'nama');

            if ($q !== '') {
                $query->where(function ($w) use ($q) {
                    $w->where('kode', 'like', "%$q%")
                      ->orWhere('nama', 'like', "%$q%");
                });
            }

            $items = $query->orderBy('kode')
                ->limit(100)
                ->get();

            return response()->json([
                'items' => $items,
            ]);
        }

        $query = DB::table('golongan_barang');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode', 'like', "%$q%")
                  ->orWhere('nama', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('kode')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return Inertia::render('farmasi/GolonganObat', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'flash' => [
                'success' => session('success'),
                'error' => session('error'),
            ],
            'nextCode' => $this->generateNextCode(),
        ]);
    }

    /**
     * JSON listing endpoint for React (non-Inertia).
     */
    public function indexJson(Request $request)
    {
        $q = trim($request->input('q', ''));
        $perPage = (int) $request->input('perPage', 10);
        if ($perPage <= 0) { $perPage = 10; }

        $query = DB::table('golongan_barang');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode', 'like', "%$q%")
                  ->orWhere('nama', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('kode')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return response()->json([
            'items' => $items,
            'filters' => [ 'q' => $q, 'perPage' => $perPage ],
            'nextCode' => $this->generateNextCode(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generate kode otomatis jika tidak diisi, format: G01, G02, ...
        $incomingKode = strtoupper(trim((string) $request->input('kode')));
        if ($incomingKode === '') {
            $incomingKode = $this->generateNextCode();
        }
        // Merge kode yang dihasilkan ke request untuk divalidasi
        $request->merge(['kode' => $incomingKode]);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:4', Rule::unique('golongan_barang', 'kode')],
            'nama' => ['required', 'string', 'max:30'],
        ]);

        DB::table('golongan_barang')->insert($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Golongan obat berhasil ditambahkan.',
                'item' => $validated,
            ], 201);
        }

        return redirect()->route('farmasi.golongan-obat.index')
            ->with('success', 'Golongan obat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kode)
    {
        $exists = DB::table('golongan_barang')->where('kode', $kode)->exists();
        if (!$exists) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Data tidak ditemukan.'], 404);
            }
            return redirect()->route('farmasi.golongan-obat.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:30'],
        ]);

        DB::table('golongan_barang')->where('kode', $kode)->update($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Golongan obat berhasil diperbarui.']);
        }

        return redirect()->route('farmasi.golongan-obat.index')
            ->with('success', 'Golongan obat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $kode)
    {
        DB::table('golongan_barang')->where('kode', $kode)->delete();

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Golongan obat berhasil dihapus.']);
        }

        return redirect()->route('farmasi.golongan-obat.index')
            ->with('success', 'Golongan obat berhasil dihapus.');
    }

    /**
     * Menghasilkan kode berikutnya dengan format G01, G02, ..., G99, G100, dst.
     */
    protected function generateNextCode(): string
    {
        $latest = DB::table('golongan_barang')
            ->where('kode', 'like', 'G%')
            ->orderByRaw('CAST(SUBSTRING(kode, 2) AS UNSIGNED) DESC')
            ->select('kode')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/^G(\d+)$/', $latest->kode, $m)) {
            $nextNumber = ((int) $m[1]) + 1;
        }

        // Padding 2 digit minimal agar format seperti G01, G02
        return 'G' . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
    }
}