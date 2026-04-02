<?php

namespace App\Models\RawatJalan;

use Illuminate\Database\Eloquent\Model;
use App\Models\RawatJalan\Gudangbarang;

class Databarang extends Model
{
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kode_satbesar',
        'kode_sat',
        'letak_barang',
        'dasar',
        'h_beli',
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan',
        'stokminimal',
        'kdjns',
        'isi',
        'kapasitas',
        'expire',
        'status',
        'kode_industri',
        'kode_kategori',
        'kode_golongan'
    ];

    protected $casts = [
        'dasar' => 'float',
        'h_beli' => 'float',
        'ralan' => 'float',
        'kelas1' => 'float',
        'kelas2' => 'float',
        'kelas3' => 'float',
        'utama' => 'float',
        'vip' => 'float',
        'vvip' => 'float',
        'beliluar' => 'float',
        'jualbebas' => 'float',
        'karyawan' => 'float',
        'stokminimal' => 'float',
        'isi' => 'float',
        'kapasitas' => 'float',
        'expire' => 'date'
    ];

    // Relasi ke gudang barang
    public function gudangBarang()
    {
        return $this->hasMany(Gudangbarang::class, 'kode_brng', 'kode_brng');
    }

    // Scope untuk barang aktif
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }

    // Scope untuk pencarian nama barang
    public function scopeSearch($query, $search)
    {
        return $query->where('nama_brng', 'like', '%' . $search . '%')
                    ->orWhere('kode_brng', 'like', '%' . $search . '%');
    }

    // Method untuk mendapatkan total stok berdasarkan bangsal
    public function getTotalStokByBangsal($kdBangsal)
    {
        return $this->gudangBarang()
                   ->where('kd_bangsal', $kdBangsal)
                   ->sum('stok');
    }

    // Method untuk mendapatkan total stok dari semua bangsal
    public function getTotalStok()
    {
        return $this->gudangBarang()->sum('stok');
    }

    // Method untuk mendapatkan data obat dengan stok (join dengan gudangbarang)
    public static function getObatWithStok($search = null, $kdBangsal = null, $limit = 50)
    {
        $query = static::select([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_satbesar',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.isi',
                'databarang.kapasitas',
                'databarang.expire',
                'databarang.status'
            ])
            ->selectRaw('COALESCE(SUM(gudangbarang.stok), 0) as total_stok')
            ->leftJoin('gudangbarang', 'databarang.kode_brng', '=', 'gudangbarang.kode_brng')
            ->where('databarang.status', '1')
            ->groupBy([
                'databarang.kode_brng',
                'databarang.nama_brng',
                'databarang.kode_satbesar',
                'databarang.kode_sat',
                'databarang.ralan',
                'databarang.isi',
                'databarang.kapasitas',
                'databarang.expire',
                'databarang.status'
            ]);

        // Filter berdasarkan bangsal jika ada
        if ($kdBangsal) {
            $query->where('gudangbarang.kd_bangsal', $kdBangsal);
        }

        // Filter pencarian jika ada
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('databarang.nama_brng', 'like', '%' . $search . '%')
                  ->orWhere('databarang.kode_brng', 'like', '%' . $search . '%');
            });
        }

        // Hanya tampilkan yang ada stoknya
        $query->havingRaw('total_stok > 0');

        return $query->limit($limit)->get();
    }

    // Method untuk mendapatkan stok detail per bangsal untuk obat tertentu
    public function getStokDetailPerBangsal()
    {
        return $this->gudangBarang()
                   ->select('kd_bangsal', 'stok', 'no_batch', 'no_faktur')
                   ->where('stok', '>', 0)
                   ->get()
                   ->groupBy('kd_bangsal')
                   ->map(function($items) {
                       return $items->sum('stok');
                   });
    }
}