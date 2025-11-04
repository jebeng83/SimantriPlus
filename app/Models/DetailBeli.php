<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailBeli extends Model
{
    use HasFactory;

    protected $connection = 'fufufafa';
    protected $table = 'detailbeli';
    public $timestamps = false;

    protected $fillable = [
        'no_faktur',
        'kode_brng',
        'jumlah',
        'h_beli',
        'subtotal',
        'dis',
        'besardis',
        'total',
        'no_batch',
        'jumlah2',
        'kadaluarsa',
        'created_at'
    ];

    protected $casts = [
        'h_beli' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'besardis' => 'decimal:2',
        'total' => 'decimal:2',
        'created_at' => 'datetime',
        'kadaluarsa' => 'date'
    ];

    // Relasi ke databarang
    public function dataBarang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }
}
