<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataBarang extends Model
{
    use HasFactory;

    protected $connection = 'fufufafa';
    protected $table = 'databarang';
    protected $primaryKey = 'kode_brng';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'nama_brng',
        'kode_sat',
        'kode_satbesar',
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
        'kode_golongan',
    ];

    protected $casts = [
        'expire' => 'date',
        'h_beli' => 'decimal:2',
        'ralan' => 'decimal:2',
        'kelas1' => 'decimal:2',
        'kelas2' => 'decimal:2',
        'kelas3' => 'decimal:2',
        'utama' => 'decimal:2',
        'vip' => 'decimal:2',
        'vvip' => 'decimal:2',
        'beliluar' => 'decimal:2',
        'jualbebas' => 'decimal:2',
        'karyawan' => 'decimal:2',
        'stokminimal' => 'integer',
        'isi' => 'integer',
        'kapasitas' => 'integer',
    ];

    // Search scope
    public function scopeSearch($query, $search)
    {
        return $query->where(function ($q) use ($search) {
            $q->where('databarang.kode_brng', 'like', "%{$search}%")
              ->orWhere('databarang.nama_brng', 'like', "%{$search}%")
              ->orWhere('databarang.kode_sat', 'like', "%{$search}%")
              ->orWhere('databarang.letak_barang', 'like', "%{$search}%");
        });
    }

    // Accessor untuk format harga
    public function getHargaBeliFormattedAttribute()
    {
        return 'Rp ' . number_format($this->h_beli, 0, ',', '.');
    }

    public function getHargaRalanFormattedAttribute()
    {
        return 'Rp ' . number_format($this->ralan, 0, ',', '.');
    }

    public function getStatusFormattedAttribute()
    {
        return $this->status === '1' ? 'Aktif' : 'Non-Aktif';
    }

    public function getExpireFormattedAttribute()
    {
        return $this->expire ? $this->expire->format('d/m/Y') : '-';
    }

    // Relasi ke detailbeli untuk mendapatkan created_at terbaru
    public function detailBeli()
    {
        return $this->hasMany(DetailBeli::class, 'kode_brng', 'kode_brng');
    }

    public function latestDetailBeli()
    {
        return $this->hasOne(DetailBeli::class, 'kode_brng', 'kode_brng')
                    ->latest('created_at');
    }
}
