<?php

namespace App\Http\Controllers\Farmasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DataBarangController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');
        $perPage = (int) $request->get('perPage', 10);

        $itemsQuery = DB::table('databarang')
            ->select([
                'kode_brng', 'nama_brng', 'kode_sat', 'kode_satbesar',
                'dasar', 'h_beli',
                'ralan', 'kelas1', 'kelas2', 'kelas3', 'utama', 'vip', 'vvip', 'beliluar', 'jualbebas', 'karyawan',
                'stokminimal', 'status', 'letak_barang', 'isi', 'kapasitas', 'kdjns', 'expire',
                'kode_industri', 'kode_kategori', 'kode_golongan'
            ])
            ->when($q, function ($query) use ($q) {
                $query->where(function ($q2) use ($q) {
                    $q2->where('kode_brng', 'like', "%$q%")
                        ->orWhere('nama_brng', 'like', "%$q%")
                        ->orWhere('kode_sat', 'like', "%$q%");
                });
            })
            ->orderBy('kode_brng', 'asc');

        // If client expects JSON (selector/autocomplete), return lightweight list
        if ($request->wantsJson()) {
            $items = $itemsQuery->limit(50)->get();
            return response()->json([
                'items' => $items,
                'filters' => [ 'q' => $q, 'perPage' => $perPage ],
            ]);
        }

        // Fallback to Blade view which mounts React page
        return view('farmasi.data-obat');
    }

    private function generateNextCode(): string
    {
        // Ambil kode terakhir secara descending; gunakan pola huruf + angka
        $latest = DB::table('databarang')->select('kode_brng')->orderBy('kode_brng', 'desc')->first();
        // Jika belum ada data, mulai dari A000000000
        if (!$latest || !isset($latest->kode_brng)) {
            return 'A000000000';
        }
        $current = strtoupper($latest->kode_brng);
        // Pola: Huruf + 9 angka (mis. A000000001)
        $prefix = substr($current, 0, 1);
        $number = (int) substr($current, 1);
        $number++;
        if ($number > 999999999) {
            // Increment prefix: A->B ... Z->AA (sederhana: looping kembali ke A)
            $prefix = chr(((ord($prefix) - 65 + 1) % 26) + 65);
            $number = 0;
        }
        return sprintf('%s%09d', $prefix, $number);
    }

    public function store(Request $request)
    {
        $payload = $request->all();
        if (empty($payload['kode_brng'])) {
            $payload['kode_brng'] = $this->generateNextCode();
        }
        // Default values for required non-nullable columns
        $payload['kode_satbesar'] = $payload['kode_satbesar'] ?? 'UNIT';
        $payload['dasar'] = isset($payload['dasar']) ? (float) $payload['dasar'] : 0;
        $payload['isi'] = isset($payload['isi']) ? (float) $payload['isi'] : 1;
        $payload['kapasitas'] = isset($payload['kapasitas']) ? (float) $payload['kapasitas'] : 0;
        $payload['status'] = $payload['status'] ?? '1';
        $payload['letak_barang'] = $payload['letak_barang'] ?? 'Apotek';

        $validated = $request->validate([
            'kode_brng' => 'required|string|max:15|unique:databarang,kode_brng',
            'nama_brng' => 'required|string|max:80',
            'kode_sat' => 'nullable|string|max:4',
            'kode_satbesar' => 'required|string|max:4',
            'dasar' => 'required|numeric',
            'h_beli' => 'nullable|numeric',
            'ralan' => 'nullable|numeric',
            'kelas1' => 'nullable|numeric',
            'kelas2' => 'nullable|numeric',
            'kelas3' => 'nullable|numeric',
            'utama' => 'nullable|numeric',
            'vip' => 'nullable|numeric',
            'vvip' => 'nullable|numeric',
            'beliluar' => 'nullable|numeric',
            'jualbebas' => 'nullable|numeric',
            'karyawan' => 'nullable|numeric',
            'isi' => 'required|numeric',
            'kapasitas' => 'required|numeric',
            'status' => 'required|in:0,1',
            'letak_barang' => 'nullable|string|max:100',
            'stokminimal' => 'nullable|numeric',
            'kdjns' => 'nullable|string|max:4',
            'expire' => 'nullable|date',
            'kode_industri' => 'nullable|string|max:5',
            'kode_kategori' => 'nullable|string|max:4',
            'kode_golongan' => 'nullable|string|max:4',
        ], [], [
            'kode_brng' => 'Kode Barang',
            'nama_brng' => 'Nama Barang',
        ]);

        DB::table('databarang')->insert($validated);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data obat berhasil ditambahkan.'
            ]);
        }

        return redirect()->route('data-obat')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function update(Request $request, string $kode_brng)
    {
        $validated = $request->validate([
            'nama_brng' => 'required|string|max:80',
            'kode_sat' => 'nullable|string|max:4',
            'kode_satbesar' => 'required|string|max:4',
            'dasar' => 'required|numeric',
            'h_beli' => 'nullable|numeric',
            'ralan' => 'nullable|numeric',
            'kelas1' => 'nullable|numeric',
            'kelas2' => 'nullable|numeric',
            'kelas3' => 'nullable|numeric',
            'utama' => 'nullable|numeric',
            'vip' => 'nullable|numeric',
            'vvip' => 'nullable|numeric',
            'beliluar' => 'nullable|numeric',
            'jualbebas' => 'nullable|numeric',
            'karyawan' => 'nullable|numeric',
            'isi' => 'required|numeric',
            'kapasitas' => 'required|numeric',
            'status' => 'required|in:0,1',
            'letak_barang' => 'nullable|string|max:100',
            'stokminimal' => 'nullable|numeric',
            'kdjns' => 'nullable|string|max:4',
            'expire' => 'nullable|date',
            'kode_industri' => 'nullable|string|max:5',
            'kode_kategori' => 'nullable|string|max:4',
            'kode_golongan' => 'nullable|string|max:4',
        ]);

        DB::table('databarang')->where('kode_brng', $kode_brng)->update($validated);

        return redirect()->route('data-obat')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(string $kode_brng)
    {
        DB::table('databarang')->where('kode_brng', $kode_brng)->delete();
        return redirect()->route('data-obat')->with('success', 'Data obat berhasil dihapus.');
}

    /**
     * Bulk update seluruh harga jual pada tabel databarang sesuai konfigurasi set_harga_obat
     * - setharga: Umum | Per Jenis | Per Barang
     * - hargadasar: Harga Beli | Harga Diskon (pakai kolom dasar jika diskon tidak tersedia)
     * - ppn: Yes/No (tambah 11% jika Yes)
     */
    public function updateHargaSemua(Request $request)
    {
        // Ambil konfigurasi set_harga_obat dengan fallback aman jika tidak ada baris
        $cfg = DB::table('set_harga_obat')->select('setharga', 'hargadasar', 'ppn')->first();
        $setharga = $cfg ? ($cfg->setharga ?? 'Umum') : 'Umum';
        $hargadasar = $cfg ? ($cfg->hargadasar ?? 'Harga Beli') : 'Harga Beli';
        $ppn = $cfg ? ($cfg->ppn ?? 'Yes') : 'Yes';

        // Sumber persentase umum (jika mode umum)
        $persenUmum = null;
        if ($setharga === 'Umum') {
            $persenUmum = DB::table('setpenjualanumum')->select(
                'ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'
            )->first();
        }

        // Ambil semua barang
        $items = DB::table('databarang')->select([
            'kode_brng','dasar','h_beli','kdjns'
        ])->orderBy('kode_brng','asc')->get();

        $updated = 0;
        foreach ($items as $it) {
            // Tentukan persentase per item sesuai mode
            $persen = null;
            if ($setharga === 'Umum') {
                $persen = $persenUmum;
            } elseif ($setharga === 'Per Jenis') {
                if (!empty($it->kdjns)) {
                    $persen = DB::table('setpenjualan')->select(
                        'ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'
                    )->where('kdjns', $it->kdjns)->first();
                }
            } elseif ($setharga === 'Per Barang') {
                $persen = DB::table('setpenjualanperbarang')->select(
                    'ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'
                )->where('kode_brng', $it->kode_brng)->first();
            }

            // Fallback jika tidak ada persentase: gunakan 0% agar tidak mengubah harga
            $ps = [
                'ralan' => (float)($persen->ralan ?? 0),
                'kelas1' => (float)($persen->kelas1 ?? 0),
                'kelas2' => (float)($persen->kelas2 ?? 0),
                'kelas3' => (float)($persen->kelas3 ?? 0),
                'utama' => (float)($persen->utama ?? 0),
                'vip' => (float)($persen->vip ?? 0),
                'vvip' => (float)($persen->vvip ?? 0),
                'beliluar' => (float)($persen->beliluar ?? 0),
                'jualbebas' => (float)($persen->jualbebas ?? 0),
                'karyawan' => (float)($persen->karyawan ?? 0),
            ];

            // Pilih harga dasar
            $base = 0.0;
            if ($hargadasar === 'Harga Beli') {
                $base = (float)($it->h_beli ?? $it->dasar ?? 0);
            } else {
                // Harga Diskon: pakai kolom dasar sebagai fallback jika tidak ada kolom diskon terpisah
                $base = (float)($it->dasar ?? $it->h_beli ?? 0);
            }

            // Hitung harga jual untuk setiap kunci
            $apply = function (float $b, float $percent) use ($ppn): float {
                $harga = $b * (1.0 + ($percent / 100.0));
                if ($ppn === 'Yes') { $harga *= 1.11; }
                // bulatkan 2 desimal
                return round($harga, 2);
            };

            $updates = [
                'ralan' => $apply($base, $ps['ralan']),
                'kelas1' => $apply($base, $ps['kelas1']),
                'kelas2' => $apply($base, $ps['kelas2']),
                'kelas3' => $apply($base, $ps['kelas3']),
                'utama' => $apply($base, $ps['utama']),
                'vip' => $apply($base, $ps['vip']),
                'vvip' => $apply($base, $ps['vvip']),
                'beliluar' => $apply($base, $ps['beliluar']),
                'jualbebas' => $apply($base, $ps['jualbebas']),
                'karyawan' => $apply($base, $ps['karyawan']),
            ];

            DB::table('databarang')->where('kode_brng', $it->kode_brng)->update($updates);
            $updated++;
        }

        return back()->with('success', "Harga jual berhasil diperbarui untuk {$updated} barang.");
    }

    public function dropdowns(Request $request)
    {
        $dropdowns = [
            'kodesatuan' => DB::table('kodesatuan')->select('kode_sat', 'satuan')->orderBy('kode_sat')->get(),
            'jenis' => DB::table('jenis')->select('kdjns', 'nama')->orderBy('kdjns')->get(),
            'industrifarmasi' => DB::table('industrifarmasi')->select('kode_industri', 'nama_industri')->orderBy('kode_industri')->get(),
            'kategori_barang' => DB::table('kategori_barang')->select('kode', 'nama')->orderBy('kode')->get(),
            'golongan_barang' => DB::table('golongan_barang')->select('kode', 'nama')->orderBy('kode')->get(),
        ];
        return response()->json($dropdowns);
    }

    public function nextCode()
    {
        return response()->json(['nextCode' => $this->generateNextCode()]);
    }
}