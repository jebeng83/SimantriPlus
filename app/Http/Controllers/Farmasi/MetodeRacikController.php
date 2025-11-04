<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MetodeRacikController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $perPage = (int) $request->input('perPage', 10);

        $query = DB::table('metode_racik');
        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('kd_racik', 'like', "%{$q}%")
                  ->orWhere('nm_racik', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('kd_racik', 'asc')->paginate($perPage)->withQueryString();

        return Inertia::render('farmasi/MetodeRacik', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'nextCode' => $this->generateNextCode(),
            'flash' => [
                'success' => session('success')
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kd_racik' => ['required', 'string', 'max:3'],
            'nm_racik' => ['required', 'string', 'max:30'],
        ]);

        $kode = strtoupper($validated['kd_racik']);
        $nama = $validated['nm_racik'];

        // Cegah duplikasi kode
        $exists = DB::table('metode_racik')->where('kd_racik', $kode)->exists();
        if ($exists) {
            return redirect()->back()->withErrors(['kd_racik' => 'Kode sudah digunakan.'])->withInput();
        }

        DB::table('metode_racik')->insert([
            'kd_racik' => $kode,
            'nm_racik' => $nama,
        ]);

        return redirect()->route('farmasi.metode-racik.index')->with('success', 'Metode racik berhasil ditambahkan.');
    }

    public function update(Request $request, string $kd_racik)
    {
        $validated = $request->validate([
            'nm_racik' => ['required', 'string', 'max:30'],
        ]);

        $affected = DB::table('metode_racik')
            ->where('kd_racik', $kd_racik)
            ->update([
                'nm_racik' => $validated['nm_racik'],
            ]);

        if (!$affected) {
            return redirect()->back()->withErrors(['kd_racik' => 'Data tidak ditemukan atau tidak ada perubahan.']);
        }

        return redirect()->route('farmasi.metode-racik.index')->with('success', 'Metode racik berhasil diperbarui.');
    }

    public function destroy(string $kd_racik)
    {
        DB::table('metode_racik')->where('kd_racik', $kd_racik)->delete();
        return redirect()->route('farmasi.metode-racik.index')->with('success', 'Metode racik berhasil dihapus.');
    }

    protected function generateNextCode(): string
    {
        // Ambil kd_racik numerik 3 digit tertinggi, lalu increment
        $last = DB::table('metode_racik')
            ->select('kd_racik')
            ->whereRaw('kd_racik REGEXP "^[0-9]{3}$"')
            ->orderByRaw('CAST(kd_racik AS UNSIGNED) DESC')
            ->first();

        $nextNumber = $last ? ((int) $last->kd_racik + 1) : 1;
        return str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }
}