<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

class KonversiSatuanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $perPage = (int) $request->input('perPage', 10);

        $query = DB::table('konver_sat as ks')
            ->join('kodesatuan as s1', 'ks.kode_sat', '=', 's1.kode_sat')
            ->join('kodesatuan as s2', 'ks.sat_konversi', '=', 's2.kode_sat')
            ->select('ks.*', 's1.satuan as satuan_asal', 's2.satuan as satuan_tujuan');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('ks.kode_sat', 'like', "%{$q}%")
                  ->orWhere('ks.sat_konversi', 'like', "%{$q}%")
                  ->orWhere('s1.satuan', 'like', "%{$q}%")
                  ->orWhere('s2.satuan', 'like', "%{$q}%");
            });
        }

        $items = $query->orderBy('ks.kode_sat')->orderBy('ks.sat_konversi')->paginate($perPage)->withQueryString();

        $satuanOptions = DB::table('kodesatuan')->select('kode_sat', 'satuan')->orderBy('kode_sat')->get();

        return Inertia::render('farmasi/KonversiSatuan', [
            'items' => $items,
            'filters' => [
                'q' => $q,
                'perPage' => $perPage,
            ],
            'satuanOptions' => $satuanOptions,
            'flash' => [
                'success' => session('success')
            ],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_sat' => ['required', 'string', 'max:4'],
            'sat_konversi' => ['required', 'string', 'max:4'],
            'nilai' => ['required', 'numeric', 'gt:0'],
            'nilai_konversi' => ['required', 'numeric', 'gt:0'],
        ]);

        // Pastikan kode satuan valid
        $existsSrc = DB::table('kodesatuan')->where('kode_sat', $validated['kode_sat'])->exists();
        $existsDst = DB::table('kodesatuan')->where('kode_sat', $validated['sat_konversi'])->exists();
        if (!$existsSrc || !$existsDst) {
            return redirect()->back()->withErrors(['kode_sat' => 'Kode satuan tidak valid.', 'sat_konversi' => 'Satuan konversi tidak valid.'])->withInput();
        }

        // Cegah duplikasi (berdasarkan kombinasi semua kolom kunci)
        $dup = DB::table('konver_sat')
            ->where('kode_sat', $validated['kode_sat'])
            ->where('sat_konversi', $validated['sat_konversi'])
            ->where('nilai', $validated['nilai'])
            ->where('nilai_konversi', $validated['nilai_konversi'])
            ->exists();
        if ($dup) {
            return redirect()->back()->withErrors(['kode_sat' => 'Konversi satuan dengan kombinasi yang sama sudah ada.'])->withInput();
        }

        DB::table('konver_sat')->insert([
            'kode_sat' => strtoupper($validated['kode_sat']),
            'sat_konversi' => strtoupper($validated['sat_konversi']),
            'nilai' => (double) $validated['nilai'],
            'nilai_konversi' => (double) $validated['nilai_konversi'],
        ]);

        return redirect()->route('farmasi.konversi-satuan.index')->with('success', 'Konversi satuan berhasil ditambahkan.');
    }

    public function update(Request $request, string $kode_sat, string $sat_konversi, $nilai, $nilai_konversi)
    {
        $validated = $request->validate([
            'kode_sat' => ['required', 'string', 'max:4'],
            'sat_konversi' => ['required', 'string', 'max:4'],
            'nilai' => ['required', 'numeric', 'gt:0'],
            'nilai_konversi' => ['required', 'numeric', 'gt:0'],
        ]);

        // Cek baris asli ada
        $originalExists = DB::table('konver_sat')
            ->where('kode_sat', $kode_sat)
            ->where('sat_konversi', $sat_konversi)
            ->where('nilai', $nilai)
            ->where('nilai_konversi', $nilai_konversi)
            ->exists();
        if (!$originalExists) {
            return redirect()->back()->withErrors(['kode_sat' => 'Data asli tidak ditemukan.'])->withInput();
        }

        // Jika kombinasi baru sama dengan lama, cukup update nilai (meski pada banyak DB ini tetap dianggap sama)
        $newKode = strtoupper($validated['kode_sat']);
        $newSat = strtoupper($validated['sat_konversi']);
        $newNilai = (double) $validated['nilai'];
        $newNilaiKonv = (double) $validated['nilai_konversi'];

        DB::beginTransaction();
        try {
            // Hapus baris lama
            DB::table('konver_sat')
                ->where('kode_sat', $kode_sat)
                ->where('sat_konversi', $sat_konversi)
                ->where('nilai', $nilai)
                ->where('nilai_konversi', $nilai_konversi)
                ->delete();

            // Masukkan baris baru
            DB::table('konver_sat')->insert([
                'kode_sat' => $newKode,
                'sat_konversi' => $newSat,
                'nilai' => $newNilai,
                'nilai_konversi' => $newNilaiKonv,
            ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['kode_sat' => 'Gagal memperbarui konversi: ' . $e->getMessage()])->withInput();
        }

        return redirect()->route('farmasi.konversi-satuan.index')->with('success', 'Konversi satuan berhasil diperbarui.');
    }

    public function destroy(string $kode_sat, string $sat_konversi, $nilai, $nilai_konversi)
    {
        DB::table('konver_sat')
            ->where('kode_sat', $kode_sat)
            ->where('sat_konversi', $sat_konversi)
            ->where('nilai', $nilai)
            ->where('nilai_konversi', $nilai_konversi)
            ->delete();

        return redirect()->route('farmasi.konversi-satuan.index')->with('success', 'Konversi satuan berhasil dihapus.');
    }
}