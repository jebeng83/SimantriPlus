<?php

namespace App\Models\RawatJalan;

use Illuminate\Database\Eloquent\Model;
use App\Models\Bangsal;
use App\Models\RawatJalan\Databarang;

class Gudangbarang extends Model
{
    protected $table = 'gudangbarang';
    protected $primaryKey = ['kode_brng', 'kd_bangsal', 'no_batch', 'no_faktur'];
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'kd_bangsal',
        'stok',
        'no_batch',
        'no_faktur'
    ];

    protected $casts = [
        'stok' => 'float'
    ];

    // Override getKeyName untuk composite key
    public function getKeyName()
    {
        return $this->primaryKey;
    }

    // Override getKey untuk composite key
    public function getKey()
    {
        $key = [];
        foreach ($this->getKeyName() as $keyName) {
            $key[$keyName] = $this->getAttribute($keyName);
        }
        return $key;
    }

    // Override setKeysForSaveQuery untuk composite key
    protected function setKeysForSaveQuery($query)
    {
        foreach ($this->getKeyName() as $keyName) {
            $query->where($keyName, '=', $this->getAttribute($keyName));
        }
        return $query;
    }

    // Relasi ke databarang
    public function databarang()
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }

    // Relasi ke bangsal (jika ada model Bangsal)
    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    // Scope untuk filter berdasarkan bangsal
    public function scopeByBangsal($query, $kdBangsal)
    {
        return $query->where('kd_bangsal', $kdBangsal);
    }

    // Scope untuk barang dengan stok > 0
    public function scopeHasStock($query)
    {
        return $query->where('stok', '>', 0);
    }

    // Method untuk mendapatkan total stok per barang per bangsal
    public static function getTotalStokByBarangBangsal($kodeBarang, $kdBangsal)
    {
        return static::where('kode_brng', $kodeBarang)
                    ->where('kd_bangsal', $kdBangsal)
                    ->sum('stok');
    }
}