<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class SatuanBarangController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->input('q');
        $perPage = (int)($request->input('perPage') ?? 10);

        $query = DB::table('kodesatuan');
        if ($q) {
            $query->where(function ($builder) use ($q) {
                $builder->where('kode_sat', 'like', "%{$q}%")
                        ->orWhere('satuan', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('kode_sat', 'asc')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return Inertia::render('farmasi/SatuanBarang', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'flash' => [
                'success' => session('success'),
            ],
            'nextCode' => $this->generateNextCode(),
        ]);
    }

    // Added JSON listing endpoint to support React pages without Inertia
    public function indexJson(Request $request)
    {
        $q = $request->input('q');
        $perPage = (int)($request->input('perPage') ?? 10);

        $query = DB::table('kodesatuan');
        if ($q) {
            $query->where(function ($builder) use ($q) {
                $builder->where('kode_sat', 'like', "%{$q}%")
                        ->orWhere('satuan', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('kode_sat', 'asc')->paginate($perPage)->appends(['q' => $q, 'perPage' => $perPage]);

        return response()->json([
            'items' => $items,
            'nextCode' => $this->generateNextCode(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_sat' => ['required', 'string', 'size:4', Rule::unique('kodesatuan', 'kode_sat')],
            'satuan' => ['required', 'string', 'max:30'],
        ]);

        DB::table('kodesatuan')->insert($validated);

        if ($request->expectsJson() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            return response()->json(['message' => 'Satuan barang berhasil ditambahkan.']);
        }

        return redirect()->route('farmasi.satuan-barang.index')
            ->with('success', 'Satuan barang berhasil ditambahkan.');
    }

    public function update(Request $request, string $kode_sat)
    {
        $validated = $request->validate([
            'satuan' => ['required', 'string', 'max:30'],
        ]);

        $affected = DB::table('kodesatuan')
            ->where('kode_sat', $kode_sat)
            ->update($validated);

        if ($request->expectsJson() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            if (!$affected) {
                return response()->json(['message' => 'Tidak ada perubahan atau kode tidak ditemukan.'], 404);
            }
            return response()->json(['message' => 'Satuan barang berhasil diperbarui.']);
        }

        if (!$affected) {
            return redirect()->route('farmasi.satuan-barang.index')
                ->with('success', 'Tidak ada perubahan atau kode tidak ditemukan.');
        }

        return redirect()->route('farmasi.satuan-barang.index')
            ->with('success', 'Satuan barang berhasil diperbarui.');
    }

    public function destroy(string $kode_sat, Request $request)
    {
        $deleted = DB::table('kodesatuan')->where('kode_sat', $kode_sat)->delete();

        if ($request->expectsJson() || $request->wantsJson() || $request->header('Accept') === 'application/json') {
            if (!$deleted) {
                return response()->json(['message' => 'Kode tidak ditemukan.'], 404);
            }
            return response()->json(['message' => 'Satuan barang berhasil dihapus.']);
        }

        return redirect()->route('farmasi.satuan-barang.index')
            ->with('success', 'Satuan barang berhasil dihapus.');
    }

    private function generateNextCode(): string
    {
        // Default prefix S dengan 3 digit, total 4 karakter: S001, S002, ...
        $max = DB::table('kodesatuan')
            ->selectRaw('MAX(CAST(SUBSTRING(kode_sat,2,3) AS UNSIGNED)) as max_num')
            ->whereRaw("kode_sat REGEXP '^[A-Za-z][0-9]{3}$'")
            ->first();

        $nextNumber = isset($max->max_num) ? ((int)$max->max_num + 1) : 1;
        return 'S' . str_pad((string)$nextNumber, 3, '0', STR_PAD_LEFT);
    }
}