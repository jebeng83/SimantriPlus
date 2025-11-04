<?php

namespace App\Http\Controllers\MatrikKegiatanUkm;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class KegiatanUkmController extends Controller
{
    protected string $table = 'kegiatan_ukm';

    // Halaman React mount
    public function page(Request $request)
    {
        return view('react.kegiatan-ukm');
    }

    // Meta: daftar kolom & primary key
    public function meta(Request $request)
    {
        try {
            $columns = DB::select("SELECT COLUMN_NAME as name, DATA_TYPE as type, IS_NULLABLE as nullable, COLUMN_DEFAULT as default_value FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION", [$this->table]);
            // Gunakan helper yang lebih cepat + cache
            $primaryKey = $this->getPrimaryKey();
            return response()->json(['columns' => $columns, 'primary_key' => $primaryKey]);
        } catch (\Throwable $e) {
            Log::error('KegiatanUkm meta error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mendapatkan metadata tabel.'], 500);
        }
    }

    // List data
    public function data(Request $request)
    {
        try {
            // Jika tabel belum ada, kembalikan data kosong (hindari 500)
            $existsRow = DB::selectOne("SELECT COUNT(*) AS cnt FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?", [$this->table]);
            $exists = ($existsRow && isset($existsRow->cnt)) ? (int)$existsRow->cnt > 0 : false;
            if (!$exists) {
                return response()->json(['data' => [], 'warning' => 'Tabel kegiatan_ukm belum dibuat.']);
            }

            $pk = $this->getPrimaryKey();
            $columns = $this->getColumnsNames();
            $orderCol = $pk;
            if (!$orderCol || !in_array($orderCol, $columns)) {
                foreach (['updated_at','created_at','kode_kegiatan','kode','tahun'] as $cand) {
                    if (in_array($cand, $columns)) { $orderCol = $cand; break; }
                }
                if (!$orderCol || !in_array($orderCol, $columns)) { $orderCol = null; }
            }
            $query = DB::table($this->table);
            if ($orderCol) $query = $query->orderBy($orderCol, 'desc');
            $rows = $query->limit(500)->get();
            return response()->json(['data' => $rows]);
        } catch (\Throwable $e) {
            Log::error('KegiatanUkm data error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengambil data kegiatan', 'detail' => $e->getMessage()], 500);
        }
    }

    // Create
    public function store(Request $request)
    {
        try {
            $table = 'kegiatan_ukm';
            if (!Schema::hasTable($table)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Tabel kegiatan_ukm tidak ditemukan. Buat migrasi terlebih dahulu.',
                ], 422);
            }
            // Ambil daftar kolom lengkap untuk validasi dan filter payload
            $columnsInfo = DB::select("SHOW COLUMNS FROM `{$table}`");
            $columnsMap = [];
            foreach ($columnsInfo as $ci) {
                // $ci: Field, Type, Null, Key, Default, Extra
                $columnsMap[$ci->Field] = [
                    'type' => $ci->Type ?? null,
                    'null' => $ci->Null ?? 'YES',
                    'key' => $ci->Key ?? null,
                    'default' => $ci->Default ?? null,
                    'extra' => $ci->Extra ?? null,
                ];
            }
            $allowedColumns = array_keys($columnsMap);

            // Gunakan payload JSON saja (hindari _token/_method) dan filter hanya kolom yang dikenal
            $insertDataRaw = $request->json()->all() ?: $request->all();
            $insertData = array_intersect_key($insertDataRaw, array_flip($allowedColumns));
            
            // Validasi sederhana: field NOT NULL tanpa default dan bukan auto_increment harus ada
            $missingRequired = [];
            foreach ($columnsMap as $name => $info) {
                $isNotNull = strtoupper((string)$info['null']) === 'NO';
                $hasDefault = !is_null($info['default']);
                $isAutoInc = stripos((string)$info['extra'], 'auto_increment') !== false;
                if ($isNotNull && !$hasDefault && !$isAutoInc && !array_key_exists($name, $insertData)) {
                    $missingRequired[] = $name;
                }
            }
            if (!empty($missingRequired)) {
                return response()->json([
                    'error' => true,
                    'message' => 'Field wajib belum diisi: ' . implode(', ', $missingRequired),
                    'fields' => $missingRequired,
                ], 422);
            }

            $pk = $this->getPrimaryKey();
            $id = null;
            $row = null;

            try {
                // Coba insert dengan insertGetId jika tabel punya auto-increment PK
                $id = DB::table($table)->insertGetId($insertData);
            } catch (\Throwable $insertIdErr) {
                // Fallback: gunakan insert biasa jika tidak ada auto-increment PK
                $ok = DB::table($table)->insert($insertData);
                if (!$ok) {
                    throw $insertIdErr; // propagasi error asli jika insert gagal
                }
            }

            if ($id && $pk) {
                $row = DB::table($table)->where($pk, $id)->first();
            } else {
                // Jika tidak auto-increment, coba ambil baris berdasarkan nilai PK yang dikirim
                if ($pk && array_key_exists($pk, $insertData)) {
                    $row = DB::table($table)->where($pk, $insertData[$pk])->first();
                }
                // Jika tetap tidak bisa, kembalikan payload sebagai konfirmasi
                if (!$row) $row = $insertData;
            }

            return response()->json([
                'success' => true,
                'message' => 'Kegiatan berhasil disimpan',
                'data' => $row,
            ]);
        } catch (\Throwable $e) {
            // Log agar lebih mudah ditelusuri dari server
            Log::error('KegiatanUkm store error: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString(),
            ]);
            $status = 500;
            return response()->json([
                'error' => true,
                'message' => 'Gagal menyimpan kegiatan',
                'detail' => $e->getMessage(),
            ], $status);
        }
    }

    // Update
    public function update(Request $request, $id)
    {
        try {
            $columns = $this->getColumnsNames();
            $pk = $this->getPrimaryKey();
            if (!$pk) {
                return response()->json(['error' => true, 'message' => 'Primary key tidak ditemukan pada tabel kegiatan_ukm'], 422);
            }
            $payload = collect($request->all())
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
            Log::error('KegiatanUkm update error: ' . $e->getMessage());
            return response()->json(['error' => true, 'message' => 'Gagal mengupdate kegiatan', 'detail' => $e->getMessage()], 500);
        }
    }

    // Delete
    public function destroy($id)
    {
        try {
            $pk = $this->getPrimaryKey();
            if (!$pk) {
                return response()->json(['error' => true, 'message' => 'Primary key tidak ditemukan pada tabel kegiatan_ukm'], 422);
            }
            // Sebelum menghapus, cek apakah data masih direferensikan oleh tabel jadwal_kegiatan_ukm
            try {
                // Cari kolom referensi yang sesuai di tabel jadwal_kegiatan_ukm
                $jadwalCols = DB::select("SHOW COLUMNS FROM `jadwal_kegiatan_ukm`");
                $cols = array_map(fn($c) => $c->Field, $jadwalCols);
                $refCol = null;
                foreach (["kode", "kode_kegiatan"] as $cand) {
                    if (in_array($cand, $cols)) { $refCol = $cand; break; }
                }
                if ($refCol) {
                    $idStr = is_null($id) ? '' : (string) $id;
                    $refCount = DB::table('jadwal_kegiatan_ukm')->where($refCol, $idStr)->count();
                    if ($refCount > 0) {
                        return response()->json([
                            'error' => true,
                            'message' => 'Tidak bisa dihapus karena kegiatan masih dipakai di jadwal',
                            'detail' => "Terdapat {$refCount} jadwal yang mereferensikan kode '{$idStr}'",
                            'referenced_by' => 'jadwal_kegiatan_ukm',
                            'reference_column' => $refCol,
                            'count' => $refCount,
                        ], 409);
                    }
                }
            } catch (\Throwable $precheckErr) {
                // Jika precheck gagal, lanjutkan proses delete dan biarkan DB mengembalikan error yang sesuai
                Log::warning('KegiatanUkm destroy precheck warning: ' . $precheckErr->getMessage());
            }

            DB::table($this->table)->where($pk, $id)->delete();
            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            Log::error('KegiatanUkm destroy error: ' . $e->getMessage());
            // Tangani spesifik error constraint FK agar pesan lebih informatif
            $status = 500;
            $msg = 'Gagal menghapus kegiatan';
            if ($e instanceof \Illuminate\Database\QueryException) {
                $sqlState = method_exists($e, 'getCode') ? $e->getCode() : null; // MySQL SQLSTATE (mis. 23000)
                $errMsg = $e->getMessage();
                if (stripos($errMsg, 'Integrity constraint violation') !== false || $sqlState === '23000') {
                    $status = 409; // Conflict
                    $msg = 'Tidak bisa dihapus karena kegiatan masih dipakai di tabel lain';
                }
            }
            return response()->json([
                'error' => true,
                'message' => $msg,
                'detail' => $e->getMessage(),
            ], $status);
        }
    }

    // Endpoint: next-code otomatis berbasis tahun (fallback lokal 0001)
    public function nextCode(Request $request)
    {
        try {
            $year = $request->query('tahun');
            $columns = $this->getColumnsNames();
            $codeField = in_array('kode_kegiatan', $columns) ? 'kode_kegiatan' : (in_array('kode', $columns) ? 'kode' : null);
            if (!$codeField) {
                return response()->json(['kode' => '0001']);
            }
            $query = DB::table($this->table)->select($codeField);
            if ($year && in_array('tahun', $columns)) {
                $query = $query->where('tahun', $year);
            }
            $codes = $query->limit(2000)->pluck($codeField)->all();
            $nums = [];
            foreach ($codes as $c) {
                $s = preg_replace('/[^0-9]/', '', (string)$c);
                if ($s === '') continue;
                $n = intval($s);
                if ($n >= 0) $nums[] = $n;
            }
            $max = !empty($nums) ? max($nums) : 0;
            $next = min($max + 1, 9999);
            $kode = str_pad((string)$next, 4, '0', STR_PAD_LEFT);
            return response()->json(['kode' => $kode]);
        } catch (\Throwable $e) {
            Log::error('KegiatanUkm nextCode error: ' . $e->getMessage());
            // fallback aman
            return response()->json(['kode' => '0001', 'error' => true, 'detail' => $e->getMessage()]);
        }
    }

    // Helper untuk dapatkan primary key (lebih cepat + cache)
    protected function getPrimaryKey(): ?string
    {
        try {
            return Cache::remember('pk:' . $this->table, 600, function () {
                $row = DB::selectOne("SHOW KEYS FROM `{$this->table}` WHERE Key_name = 'PRIMARY'");
                // MySQL returns Column_name; MariaDB compatible
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

    protected function getColumnsNames(): array
    {
        try {
            return Cache::remember('columns:' . $this->table, 600, function () {
                // SHOW COLUMNS lebih cepat dibandingkan information_schema
                $columns = DB::select("SHOW COLUMNS FROM `{$this->table}`");
                return array_map(fn($c) => $c->Field, $columns);
            });
        } catch (\Throwable $e) {
            return [];
        }
    }
}