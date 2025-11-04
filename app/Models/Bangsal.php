<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\RawatJalan\Gudangbarang;

class Bangsal extends Model
{
    protected $table = 'bangsal';
    protected $primaryKey = 'kd_bangsal';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'kd_bangsal',
        'nm_bangsal',
        'status'
    ];

    // Relasi ke gudang barang
    public function gudangBarang()
    {
        return $this->hasMany(Gudangbarang::class, 'kd_bangsal', 'kd_bangsal');
    }

    // Relasi ke set depo ralan
    public function setDepoRalan()
    {
        return $this->hasMany(SetDepoRalan::class, 'kd_bangsal', 'kd_bangsal');
    }

    // Scope untuk bangsal aktif
    public function scopeActive($query)
    {
        return $query->where('status', '1');
    }
}