<?php

namespace App\Http\Controllers\Farmasi;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class KategoriBarangController
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
            $query = DB::table('kategori_barang')
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

        $query = DB::table('kategori_barang');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode', 'like', "%$q%")
                  ->orWhere('nama', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('kode')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return Inertia::render('farmasi/KategoriObat', [
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Generate kode otomatis jika tidak diisi, format: K01, K02, ...
        $incomingKode = strtoupper(trim((string) $request->input('kode')));
        if ($incomingKode === '') {
            $incomingKode = $this->generateNextCode();
        }
        // Merge kode yang dihasilkan ke request untuk divalidasi
        $request->merge(['kode' => $incomingKode]);

        $validated = $request->validate([
            'kode' => ['required', 'string', 'max:4', Rule::unique('kategori_barang', 'kode')],
            'nama' => ['required', 'string', 'max:30'],
        ]);

        DB::table('kategori_barang')->insert($validated);

        return redirect()->route('farmasi.kategori-obat.index')
            ->with('success', 'Kategori obat berhasil ditambahkan.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $kode)
    {
        $exists = DB::table('kategori_barang')->where('kode', $kode)->exists();
        if (!$exists) {
            return redirect()->route('farmasi.kategori-obat.index')
                ->with('error', 'Data tidak ditemukan.');
        }

        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:30'],
        ]);

        DB::table('kategori_barang')->where('kode', $kode)->update($validated);

        return redirect()->route('farmasi.kategori-obat.index')
            ->with('success', 'Kategori obat berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $kode)
    {
        DB::table('kategori_barang')->where('kode', $kode)->delete();

        return redirect()->route('farmasi.kategori-obat.index')
            ->with('success', 'Kategori obat berhasil dihapus.');
    }

    /**
     * Menghasilkan kode berikutnya dengan format K01, K02, ..., K99, K100, dst.
     */
    protected function generateNextCode(): string
    {
        $latest = DB::table('kategori_barang')
            ->where('kode', 'like', 'K%')
            ->orderByRaw('CAST(SUBSTRING(kode, 2) AS UNSIGNED) DESC')
            ->select('kode')
            ->first();

        $nextNumber = 1;
        if ($latest && preg_match('/^K(\d+)$/', $latest->kode, $m)) {
            $nextNumber = ((int) $m[1]) + 1;
        }

        // Gunakan padding 2 digit minimal agar format seperti K01, K02
        return 'K' . str_pad((string) $nextNumber, 2, '0', STR_PAD_LEFT);
    }
}