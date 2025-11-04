<?php

namespace App\Http\Controllers\Farmasi;
use App\Http\Controllers\Controller;

// use App\Models\SetPenjualanUmum; // legacy mapping to setpenjualanumum
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class SetHargaObatController extends Controller
{
    /**
     * Display the settings page
     */
    public function index()
    {
        try {
            // Ambil data dari tabel set_harga_obat (sesuai permintaan)
            $hargaObat = DB::table('set_harga_obat')->first();

            if (!$hargaObat) {
                // Jika belum ada data, buat data default satu baris
                // Ambil struktur kolom untuk memastikan kolom tersedia
                $schema = DB::select('DESCRIBE set_harga_obat');
                $columns = collect($schema)->pluck('Field')->all();

                // Data default yang akan diinsert (akan difilter sesuai kolom tersedia)
                $defaults = [
                    'setharga' => 'Umum',
                    'hargadasar' => 'Harga Beli',
                    'ppn' => 'Yes',
                    'ralan' => 20,
                    'kelas1' => 20,
                    'kelas2' => 20,
                    'kelas3' => 20,
                    'utama' => 20,
                    'vip' => 20,
                    'vvip' => 20,
                    'beliluar' => 20,
                    'jualbebas' => 20,
                    'karyawan' => 20,
                ];

                $insertData = collect($defaults)->filter(function ($value, $key) use ($columns) {
                    return in_array($key, $columns, true);
                })->all();

                DB::table('set_harga_obat')->insert($insertData);
                $hargaObat = DB::table('set_harga_obat')->first();
            }

            // Ambil struktur tabel menggunakan DESCRIBE
            $schema = DB::select('DESCRIBE set_harga_obat');

            // Ambil pengaturan harga umum dari tabel setpenjualanumum
            $penjualanUmum = DB::table('setpenjualanumum')->first();
            if (!$penjualanUmum) {
                // Inisialisasi baris default jika kosong
                DB::table('setpenjualanumum')->insert([
                    'ralan' => 20,
                    'kelas1' => 20,
                    'kelas2' => 20,
                    'kelas3' => 20,
                    'utama' => 20,
                    'vip' => 20,
                    'vvip' => 20,
                    'beliluar' => 20,
                    'jualbebas' => 20,
                    'karyawan' => 20,
                ]);
                $penjualanUmum = DB::table('setpenjualanumum')->first();
            }

            // Ambil data pengaturan per-jenis dari tabel setpenjualan dan join nama jenis
            $penjualanPerJenis = DB::table('setpenjualan')
                ->leftJoin('jenis', 'jenis.kdjns', '=', 'setpenjualan.kdjns')
                ->select('setpenjualan.*', DB::raw('jenis.nama as nama_jenis'))
                ->orderBy('setpenjualan.kdjns')
                ->get();

            // Ambil data pengaturan per-barang dari tabel setpenjualanperbarang dan join nama barang
            $penjualanPerBarang = DB::table('setpenjualanperbarang')
                ->leftJoin('databarang', 'databarang.kode_brng', '=', 'setpenjualanperbarang.kode_brng')
                ->select('setpenjualanperbarang.*', DB::raw('databarang.nama_brng as nama_barang'))
                ->orderBy('setpenjualanperbarang.kode_brng')
                ->get();

            // Render Blade view yang akan me-mount React page
            return view('farmasi.set-harga-obat');
        } catch (\Exception $e) {
            // Tetap render halaman Blade; React akan melakukan fetch data
            return view('farmasi.set-harga-obat');
        }
    }

    /**
     * Update the settings
     */
    public function update(Request $request)
    {
        // Bangun aturan validasi secara dinamis berdasarkan kolom yang ada di tabel
        $schema = DB::select('DESCRIBE set_harga_obat');
        $columns = collect($schema)->pluck('Field')->all();

        $rules = [];
        if (in_array('setharga', $columns, true)) {
            $rules['setharga'] = 'required|in:Umum,Per Jenis,Per Barang';
        }
        if (in_array('hargadasar', $columns, true)) {
            $rules['hargadasar'] = 'required|in:Harga Beli,Harga Diskon';
        }
        if (in_array('ppn', $columns, true)) {
            $rules['ppn'] = 'required|in:Yes,No';
        }

        // Kolom numeric opsional: hanya validasi jika memang ada di tabel
        $numericKeys = ['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'];
        foreach ($numericKeys as $key) {
            if (in_array($key, $columns, true)) {
                $rules[$key] = 'required|numeric|min:0';
            }
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            $data = $validator->validated();

            // Pastikan hanya kolom yang ada di DB yang dikirim saat update/insert
            $filtered = collect($data)->filter(function ($value, $key) use ($columns) {
                return in_array($key, $columns, true);
            })->all();

            // Casting nilai numerik bila ada kolomnya
            foreach ($numericKeys as $key) {
                if (array_key_exists($key, $filtered)) {
                    $filtered[$key] = (float) $filtered[$key];
                }
            }

            // Update existing row (single-row table) atau insert jika belum ada
            $existing = DB::table('set_harga_obat')->first();
            if ($existing) {
                DB::table('set_harga_obat')->update($filtered);
            } else {
                DB::table('set_harga_obat')->insert($filtered);
            }
            DB::commit();
            
            return redirect()->back()->with('success', 'Pengaturan harga obat berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            // Kembalikan sebagai validation error agar Inertia memicu onError di frontend
            return redirect()->back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Update general price settings (setpenjualanumum)
     */
    public function updatePenjualanUmum(Request $request)
    {
        // Validasi field numerik (double) yang diminta
        $rules = [
            'ralan' => 'required|numeric|min:0',
            'kelas1' => 'required|numeric|min:0',
            'kelas2' => 'required|numeric|min:0',
            'kelas3' => 'required|numeric|min:0',
            'utama' => 'required|numeric|min:0',
            'vip' => 'required|numeric|min:0',
            'vvip' => 'required|numeric|min:0',
            'beliluar' => 'required|numeric|min:0',
            'jualbebas' => 'required|numeric|min:0',
            'karyawan' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            $data = $validator->validated();

            // Casting to double (float) untuk konsistensi
            foreach ($data as $key => $value) {
                $data[$key] = (double) $value;
            }

            $existing = DB::table('setpenjualanumum')->first();
            if ($existing) {
                DB::table('setpenjualanumum')->update($data);
            } else {
                DB::table('setpenjualanumum')->insert($data);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengaturan harga umum berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Store or update per-jenis price settings (setpenjualan)
     */
    public function storePenjualanPerJenis(Request $request)
    {
        // Validasi input: kdjns harus 4 karakter dan ada di tabel jenis
        $rules = [
            'kdjns' => 'required|string|size:4|exists:jenis,kdjns',
            'ralan' => 'required|numeric|min:0',
            'kelas1' => 'required|numeric|min:0',
            'kelas2' => 'required|numeric|min:0',
            'kelas3' => 'required|numeric|min:0',
            'utama' => 'required|numeric|min:0',
            'vip' => 'required|numeric|min:0',
            'vvip' => 'required|numeric|min:0',
            'beliluar' => 'required|numeric|min:0',
            'jualbebas' => 'required|numeric|min:0',
            'karyawan' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();
            $data = $validator->validated();

            // Normalisasi kdjns menjadi uppercase
            $data['kdjns'] = strtoupper(trim($data['kdjns']));

            // Casting ke double untuk konsistensi penyimpanan
            foreach (['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'] as $key) {
                $data[$key] = (double) $data[$key];
            }

            // Upsert ke tabel setpenjualan berdasarkan kdjns
            $exists = DB::table('setpenjualan')->where('kdjns', $data['kdjns'])->exists();
            if ($exists) {
                DB::table('setpenjualan')->where('kdjns', $data['kdjns'])->update($data);
            } else {
                DB::table('setpenjualan')->insert($data);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengaturan harga per jenis berhasil disimpan');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete pengaturan harga per Jenis (setpenjualan) by kdjns
     */
    public function destroyPenjualanPerJenis(string $kdjns)
    {
        try {
            $kode = strtoupper(trim($kdjns));
            DB::table('setpenjualan')->where('kdjns', $kode)->delete();
            return redirect()->back()->with('success', 'Pengaturan harga per jenis berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['general' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Store or update pengaturan harga per Barang ke tabel setpenjualanperbarang
     * Kolom: ralan, kelas1, kelas2, kelas3, utama, vip, vvip, beliluar, jualbebas, karyawan, kode_brng
     */
    public function storePenjualanPerBarang(Request $request)
    {
        $rules = [
            'kode_brng' => 'required|string|max:15|exists:databarang,kode_brng',
            'ralan' => 'required|numeric|min:0',
            'kelas1' => 'required|numeric|min:0',
            'kelas2' => 'required|numeric|min:0',
            'kelas3' => 'required|numeric|min:0',
            'utama' => 'required|numeric|min:0',
            'vip' => 'required|numeric|min:0',
            'vvip' => 'required|numeric|min:0',
            'beliluar' => 'required|numeric|min:0',
            'jualbebas' => 'required|numeric|min:0',
            'karyawan' => 'required|numeric|min:0',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        try {
            DB::beginTransaction();

            $data = $validator->validated();
            $data['kode_brng'] = strtoupper(trim($data['kode_brng']));

            // Cast numeric fields to double
            foreach (['ralan','kelas1','kelas2','kelas3','utama','vip','vvip','beliluar','jualbebas','karyawan'] as $key) {
                $data[$key] = (double) $data[$key];
            }

            // Upsert to setpenjualanperbarang by kode_brng
            $exists = DB::table('setpenjualanperbarang')
                ->where('kode_brng', $data['kode_brng'])
                ->exists();

            if ($exists) {
                DB::table('setpenjualanperbarang')
                    ->where('kode_brng', $data['kode_brng'])
                    ->update($data);
            } else {
                DB::table('setpenjualanperbarang')->insert($data);
            }

            DB::commit();
            return redirect()->back()->with('success', 'Pengaturan harga per barang berhasil disimpan ke setpenjualanperbarang');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withErrors(['general' => 'Terjadi kesalahan: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete pengaturan harga per Barang (setpenjualanperbarang) by kode_brng
     */
    public function destroyPenjualanPerBarang(string $kode_brng)
    {
        try {
            $kode = strtoupper(trim($kode_brng));
            DB::table('setpenjualanperbarang')->where('kode_brng', $kode)->delete();
            return redirect()->back()->with('success', 'Pengaturan harga per barang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['general' => 'Gagal menghapus: ' . $e->getMessage()]);
        }
    }

    /**
     * Show pengaturan harga per barang yang tersimpan di setpenjualanperbarang
     */
    public function showPenjualanPerBarang(string $kode_brng)
    {
        try {
            $kode = strtoupper(trim($kode_brng));
            $row = DB::table('setpenjualanperbarang')
                ->where('kode_brng', $kode)
                ->first();

            if (!$row) {
                // Jika belum ada pengaturan, kembalikan default kosong agar frontend bisa menampilkan kosong tanpa error
                return response()->json([
                    'success' => true,
                    'data' => (object)[],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kode_brng' => $row->kode_brng,
                    'ralan' => (double) $row->ralan,
                    'kelas1' => (double) $row->kelas1,
                    'kelas2' => (double) $row->kelas2,
                    'kelas3' => (double) $row->kelas3,
                    'utama' => (double) $row->utama,
                    'vip' => (double) $row->vip,
                    'vvip' => (double) $row->vvip,
                    'beliluar' => (double) $row->beliluar,
                    'jualbebas' => (double) $row->jualbebas,
                    'karyawan' => (double) $row->karyawan,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show pengaturan harga umum (setpenjualanumum) sebagai JSON
     */
    public function showPenjualanUmum()
    {
        try {
            $row = DB::table('setpenjualanumum')->first();
            if (!$row) {
                // Inisialisasi default bila kosong
                $defaults = [
                    'ralan' => 20,
                    'kelas1' => 20,
                    'kelas2' => 20,
                    'kelas3' => 20,
                    'utama' => 20,
                    'vip' => 20,
                    'vvip' => 20,
                    'beliluar' => 20,
                    'jualbebas' => 20,
                    'karyawan' => 20,
                ];
                DB::table('setpenjualanumum')->insert($defaults);
                $row = (object) $defaults;
            }
            return response()->json([
                'success' => true,
                'data' => [
                    'ralan' => (double) $row->ralan,
                    'kelas1' => (double) $row->kelas1,
                    'kelas2' => (double) $row->kelas2,
                    'kelas3' => (double) $row->kelas3,
                    'utama' => (double) $row->utama,
                    'vip' => (double) $row->vip,
                    'vvip' => (double) $row->vvip,
                    'beliluar' => (double) $row->beliluar,
                    'jualbebas' => (double) $row->jualbebas,
                    'karyawan' => (double) $row->karyawan,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show pengaturan harga per Jenis (setpenjualan) sebagai JSON
     */
    public function showPenjualanPerJenis(string $kdjns)
    {
        try {
            $kode = strtoupper(trim($kdjns));
            // Jika jenis tidak ditemukan, kembalikan data null (hindari 404 agar frontend bisa fallback)
            $existsJenis = DB::table('jenis')->where('kdjns', $kode)->exists();
            if (!$existsJenis) {
                return response()->json([
                    'success' => true,
                    'data' => (object)[],
                ]);
            }

            $row = DB::table('setpenjualan')->where('kdjns', $kode)->first();
            if (!$row) {
                return response()->json([
                    'success' => true,
                    'data' => (object)[],
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'kdjns' => $kode,
                    'ralan' => (double) $row->ralan,
                    'kelas1' => (double) $row->kelas1,
                    'kelas2' => (double) $row->kelas2,
                    'kelas3' => (double) $row->kelas3,
                    'utama' => (double) $row->utama,
                    'vip' => (double) $row->vip,
                    'vvip' => (double) $row->vvip,
                    'beliluar' => (double) $row->beliluar,
                    'jualbebas' => (double) $row->jualbebas,
                    'karyawan' => (double) $row->karyawan,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all pengaturan harga per Jenis (setpenjualan) as JSON
     */
    public function listPenjualanPerJenis()
    {
        try {
            $rows = DB::table('setpenjualan')
                ->leftJoin('jenis', 'jenis.kdjns', '=', 'setpenjualan.kdjns')
                ->select('setpenjualan.*', DB::raw('jenis.nama as nama_jenis'))
                ->orderBy('setpenjualan.kdjns')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * List all pengaturan harga per Barang (setpenjualanperbarang) as JSON
     */
    public function listPenjualanPerBarang()
    {
        try {
            $rows = DB::table('setpenjualanperbarang')
                ->leftJoin('databarang', 'databarang.kode_brng', '=', 'setpenjualanperbarang.kode_brng')
                ->select('setpenjualanperbarang.*', DB::raw('databarang.nama_brng as nama_barang'))
                ->orderBy('setpenjualanperbarang.kode_brng')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get percentage data for API
     */
    public function getPercentageData()
    {
        try {
            $hargaObat = DB::table('set_harga_obat')->first();
            if (!$hargaObat) {
                DB::table('set_harga_obat')->insert([
                    'ralan' => 20,
                    'kelas1' => 20,
                    'kelas2' => 20,
                    'kelas3' => 20,
                    'utama' => 20,
                    'vip' => 20,
                    'vvip' => 20,
                    'beliluar' => 20,
                    'jualbebas' => 20,
                    'karyawan' => 20,
                ]);
                $hargaObat = DB::table('set_harga_obat')->first();
            }
            
            return response()->json([
                'success' => true,
                'data' => $hargaObat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat mengambil data: ' . $e->getMessage()
            ], 500);
        }
    }
}