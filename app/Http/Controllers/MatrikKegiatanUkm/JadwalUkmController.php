<?php

namespace App\Http\Controllers\MatrikKegiatanUkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class JadwalUkmController extends Controller
{
    protected string $table = 'jadwal_kegiatan_ukm';

    /**
     * Halaman React mount
     */
    public function page(Request $request)
    {
        return view('react.jadwal_ukm');
    }

    /**
     * Meta: daftar kolom & primary key
     */
    public function meta(Request $request)
    {
        try {
            $columns = DB::select(
                "SELECT COLUMN_NAME as name, DATA_TYPE as type, IS_NULLABLE as nullable, COLUMN_DEFAULT as default_value FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION",
                [$this->table]
            );
            $primaryKey = $this->getPrimaryKey();
            return response()->json(['columns' => $columns, 'primary_key' => $primaryKey]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm meta error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mendapatkan metadata tabel.'], 500);
        }
    }

    /**
     * Describe: tampilkan struktur lengkap tabel (kolom, index, dan CREATE TABLE SQL)
     */
    public function describe(Request $request)
    {
        try {
            if (!Schema::hasTable($this->table)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Tabel ' . $this->table . ' tidak ditemukan. Buat migrasi terlebih dahulu.',
                ], 422);
            }
            $columns = DB::select("SHOW FULL COLUMNS FROM `{$this->table}`");
            $indexes = DB::select("SHOW INDEX FROM `{$this->table}`");
            $create = DB::selectOne("SHOW CREATE TABLE `{$this->table}`");
            $createSql = $create && isset($create->{'Create Table'}) ? $create->{'Create Table'} : null;
            return response()->json([
                'table' => $this->table,
                'columns' => $columns,
                'indexes' => $indexes,
                'create_sql' => $createSql,
            ]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm describe error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mendeskripsikan struktur tabel', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * List data
     */
    public function data(Request $request)
    {
        try {
            $existsRow = DB::selectOne("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?", [$this->table]);
            $exists = ($existsRow && isset($existsRow->cnt)) ? (int)$existsRow->cnt > 0 : false;
            if (!$exists) {
                return response()->json(['data' => [], 'warning' => 'Tabel ' . $this->table . ' belum dibuat.']);
            }
            
            $pk = $this->getPrimaryKey();
            $columns = $this->getColumnsNames();
            $orderCol = $pk;
            if (!$orderCol || !in_array($orderCol, $columns)) {
                foreach (['updated_at','created_at','kd_jadwal','tanggal'] as $cand) {
                    if (in_array($cand, $columns)) { $orderCol = $cand; break; }
                }
                if (!$orderCol || !in_array($orderCol, $columns)) { $orderCol = null; }
            }
            $query = DB::table($this->table);
            if ($orderCol) $query = $query->orderBy($orderCol, 'desc');
            $rows = $query->limit(500)->get();
            return response()->json(['data' => $rows]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm data error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengambil data jadwal', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Create
     */
    public function store(Request $request)
    {
        try {
            if (!Schema::hasTable($this->table)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Tabel ' . $this->table . ' tidak ditemukan. Buat migrasi terlebih dahulu.',
                ], 422);
            }

            // Validasi input
            $validated = $request->validate([
                'tanggal' => ['required', 'date'],
                'nip' => ['required', 'string', 'max:20', 'exists:petugas,nip'],
                'kode' => ['required', 'integer', 'exists:kegiatan_ukm,kode'],
                'kd_kel' => ['required', 'integer', 'exists:kelurahan,kd_kel'],
                'Keterangan' => ['required', 'string', 'max:200'],
                'status' => ['required', 'in:Belum,Tunda,Sudah,Batal'],
            ], [
                'tanggal.required' => 'Tanggal wajib diisi',
                'tanggal.date' => 'Tanggal tidak valid',
                'nip.required' => 'NIP petugas wajib diisi',
                'nip.max' => 'NIP maksimal 20 karakter',
                'nip.exists' => 'NIP tidak ditemukan',
                'kode.required' => 'Kode kegiatan wajib diisi',
                'kode.integer' => 'Kode harus berupa angka',
                'kode.exists' => 'Kode kegiatan tidak ditemukan',
                'kd_kel.required' => 'Kelurahan wajib diisi',
                'kd_kel.integer' => 'Kode kelurahan harus berupa angka',
                'kd_kel.exists' => 'Kode kelurahan tidak ditemukan',
                'Keterangan.required' => 'Keterangan wajib diisi',
                'Keterangan.max' => 'Keterangan maksimal 200 karakter',
                'status.required' => 'Status wajib diisi',
                'status.in' => 'Status harus salah satu dari Belum, Tunda, Sudah, atau Batal',
            ]);

            $pk = $this->getPrimaryKey();
            $id = null;
            $row = null;

            try {
                $id = DB::table($this->table)->insertGetId($validated);
            } catch (\Throwable $insertIdErr) {
                $ok = DB::table($this->table)->insert($validated);
                if (!$ok) {
                    throw $insertIdErr;
                }
            }

            if ($id && $pk) {
                $row = DB::table($this->table)->where($pk, $id)->first();
            } else {
                $row = $validated;
            }

            return response()->json([
                'success' => true,
                'message' => 'Jadwal berhasil disimpan',
                'data' => $row,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyimpan jadwal',
                'detail' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update
     */
    public function update(Request $request, $id)
    {
        try {
            $columns = $this->getColumnsNames();
            $pk = $this->getPrimaryKey();
            if (!$pk) {
                return response()->json(['error' => true, 'message' => 'Primary key tidak ditemukan pada tabel ' . $this->table], 422);
            }

            // Validasi partial (only provided fields)
            $validated = $request->validate([
                'tanggal' => ['sometimes', 'required', 'date'],
                'nip' => ['sometimes', 'required', 'string', 'max:20', 'exists:petugas,nip'],
                'kode' => ['sometimes', 'required', 'integer', 'exists:kegiatan_ukm,kode'],
                'kd_kel' => ['sometimes', 'required', 'integer', 'exists:kelurahan,kd_kel'],
                'Keterangan' => ['sometimes', 'required', 'string', 'max:200'],
                'status' => ['sometimes', 'required', 'in:Belum,Tunda,Sudah,Batal'],
            ]);

            $payload = collect($validated)
                ->except(['_token', '_method', $pk, 'created_at', 'updated_at'])
                ->toArray();
            $updateData = array_intersect_key($payload, array_flip($columns));
            if (empty($updateData)) {
                return response()->json(['error' => true, 'message' => 'Tidak ada field yang valid untuk diupdate'], 422);
            }
            DB::table($this->table)->where($pk, $id)->update($updateData);
            $row = DB::table($this->table)->where($pk, $id)->first();
            return response()->json(['success' => true, 'data' => $row]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm update error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengupdate jadwal', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete
     */
    public function destroy($id)
    {
        try {
            $pk = $this->getPrimaryKey();
            if (!$pk) {
                return response()->json(['error' => true, 'message' => 'Primary key tidak ditemukan pada tabel ' . $this->table], 422);
            }
            DB::table($this->table)->where($pk, $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm destroy error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal menghapus jadwal', 'detail' => $e->getMessage()], 500);
        }
    }

    /**
     * Helper: dapatkan primary key dari tabel
     */
    protected function getPrimaryKey(): ?string
    {
        try {
            return Cache::remember('pk:' . $this->table, 600, function () {
                $row = DB::selectOne("SHOW KEYS FROM `{$this->table}` WHERE Key_name = 'PRIMARY'");
                if ($row && isset($row->Column_name)) {
                    return $row->Column_name;
                }
                return null;
            });
        } catch (\Throwable $e) {
            Log::warning('getPrimaryKey error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Helper: dapatkan daftar nama kolom
     */
    /**
     * Data jadwal bulan berjalan dengan join nama kegiatan, petugas, dan kelurahan
     * Query param:
     * - month: format YYYY-MM (opsional, default bulan ini)
     */
    public function monthly(Request $request)
    {
        try {
            // Tentukan rentang tanggal bulan
            $month = $request->query('month');
            // Normalisasi nilai bulan (YYYY-MM), default ke bulan ini jika tidak valid
            $ym = ($month && preg_match('/^\d{4}-\d{2}$/', $month)) ? $month : date('Y-m');
            $year = (int)substr($ym, 0, 4);
            $mon = (int)substr($ym, 5, 2);
            // Hitung rentang tanggal start-end untuk informasi di response
            try {
                $start = \Carbon\Carbon::createFromFormat('Y-m', $ym)->startOfMonth()->toDateString();
                $end = \Carbon\Carbon::createFromFormat('Y-m', $ym)->endOfMonth()->toDateString();
            } catch (\Throwable $e) {
                $start = sprintf('%04d-%02d-01', $year, $mon);
                $end = date('Y-m-t', strtotime($start));
            }

            // Bangun fallback nama kegiatan berbasis kolom yang BENAR-BENAR ada
            $kgCandidates = ['nama_kegiatan', 'nm_kegiatan', 'nama']; // jangan gunakan 'kegiatan' jika tidak ada
            $kgExisting = [];
            foreach ($kgCandidates as $col) {
                if (Schema::hasColumn('kegiatan_ukm', $col)) { $kgExisting[] = 'k.' . $col; }
            }
            $nameExpr = !empty($kgExisting)
                ? 'COALESCE(' . implode(', ', $kgExisting) . ', "")'
                : '""';
            // Tentukan kolom order kedua yang valid
            $orderSecond = null;
            if (!empty($kgExisting)) {
                $orderSecond = $kgExisting[0]; // pakai kolom pertama yang ada
            }

            // Join dengan tabel referensi untuk mendapatkan nama
            $query = DB::table($this->table . ' as j')
                ->leftJoin('kegiatan_ukm as k', 'j.kode', '=', 'k.kode')
                ->leftJoin('petugas as p', 'j.nip', '=', 'p.nip')
                ->leftJoin('kelurahan as l', 'j.kd_kel', '=', 'l.kd_kel')
                ->select(
                    DB::raw('j.kd_jadwal as id'),
                    'j.tanggal',
                    'j.kode',
                    'j.nip',
                    'j.kd_kel',
                    'j.Keterangan',
                    'j.status',
                    DB::raw($nameExpr . ' as nama_kegiatan'),
                    DB::raw('COALESCE(p.nama, "") as petugas_nama'),
                    DB::raw('COALESCE(l.nm_kel, "") as nm_kel')
                )
                ->whereBetween('j.tanggal', [$start, $end])
                ->whereYear('j.tanggal', $year)
                ->whereMonth('j.tanggal', $mon)
                ->orderBy('j.tanggal', 'asc');
            if ($orderSecond) {
                $query = $query->orderBy(DB::raw($orderSecond), 'asc');
            }
            $rows = $query->get();

            return response()->json([
                'success' => true,
                'month' => $month ?: date('Y-m'),
                'start' => $start,
                'end' => $end,
                'data' => $rows,
            ]);
        } catch (\Throwable $e) {
            Log::error('JadwalUkm monthly error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengambil data jadwal bulanan', 'detail' => $e->getMessage()], 500);
        }
    }

    protected function getColumnsNames(): array
    {
        try {
            return Cache::remember('columns:' . $this->table, 600, function () {
                $columns = DB::select("SHOW COLUMNS FROM `{$this->table}`");
                return array_map(fn($c) => $c->Field, $columns);
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
}