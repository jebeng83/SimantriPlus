<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class DataSuplierController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->get('q', ''));
        $perPage = (int) ($request->get('perPage', 10));

        $query = DB::table('datasuplier');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kode_suplier', 'like', "%$q%")
                    ->orWhere('nama_suplier', 'like', "%$q%")
                    ->orWhere('alamat', 'like', "%$q%")
                    ->orWhere('kota', 'like', "%$q%")
                    ->orWhere('no_telp', 'like', "%$q%")
                    ->orWhere('nama_bank', 'like', "%$q%")
                    ->orWhere('rekening', 'like', "%$q%");
            });
        }

        $items = $query->orderBy('kode_suplier')
            ->paginate($perPage > 0 ? $perPage : 10)
            ->withQueryString();

        $nextCode = $this->generateNextCode();

        return Inertia::render('farmasi/datasuplier', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage > 0 ? $perPage : 10,
            ],
            'flash' => [
                'success' => session('success'),
            ],
            'nextCode' => $nextCode,
        ]);
    }

    private function generateNextCode(): string
    {
        // Ikuti pola S0001 dan bertambah otomatis
        $next = 'S0001';

        $max = DB::table('datasuplier')
            ->select('kode_suplier')
            ->where('kode_suplier', 'like', 'S____') // 4 digit
            ->orderBy('kode_suplier', 'desc')
            ->first();

        if ($max && preg_match('/^S(\d{4})$/', $max->kode_suplier, $m)) {
            $num = (int) $m[1] + 1;
            $next = 'S' . str_pad((string) $num, 4, '0', STR_PAD_LEFT);
        }

        return $next;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_suplier' => ['required', 'string', 'size:5', Rule::unique('datasuplier', 'kode_suplier')],
            'nama_suplier' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:50'],
            'kota' => ['required', 'string', 'max:20'],
            'no_telp' => ['nullable', 'string', 'max:13'],
            'nama_bank' => ['nullable', 'string', 'max:30'],
            'rekening' => ['nullable', 'string', 'max:20'],
        ]);

        DB::table('datasuplier')->insert($validated);

        return back()->with('success', 'Suplier berhasil ditambahkan');
    }

    public function update(Request $request, string $kode_suplier)
    {
        $validated = $request->validate([
            'nama_suplier' => ['required', 'string', 'max:50'],
            'alamat' => ['nullable', 'string', 'max:50'],
            'kota' => ['required', 'string', 'max:20'],
            'no_telp' => ['nullable', 'string', 'max:13'],
            'nama_bank' => ['nullable', 'string', 'max:30'],
            'rekening' => ['nullable', 'string', 'max:20'],
        ]);

        $affected = DB::table('datasuplier')
            ->where('kode_suplier', $kode_suplier)
            ->update($validated);

        if (!$affected) {
            abort(404, 'Suplier tidak ditemukan');
        }

        return back()->with('success', 'Suplier berhasil diperbarui');
    }

    public function destroy(string $kode_suplier)
    {
        DB::table('datasuplier')
            ->where('kode_suplier', $kode_suplier)
            ->delete();

        return back()->with('success', 'Suplier berhasil dihapus');
    }
}