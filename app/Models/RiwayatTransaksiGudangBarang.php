<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class RiwayatTransaksiGudangBarang extends Model
{
    use HasFactory;

    protected $connection = 'fufufafa';
    protected $table = 'riwayat_transaksi_gudangbarang';
    
    protected $fillable = [
        'kode_brng',
        'kd_bangsal',
        'no_batch',
        'no_faktur',
        'jenis_transaksi',
        'stok_sebelum',
        'stok_sesudah',
        'selisih',
        'keterangan',
        'user_id',
        'sumber_transaksi',
        'data_sebelum',
        'data_sesudah',
        'waktu_transaksi'
    ];

    protected $casts = [
        'stok_sebelum' => 'double',
        'stok_sesudah' => 'double',
        'selisih' => 'double',
        'data_sebelum' => 'array',
        'data_sesudah' => 'array',
        'waktu_transaksi' => 'datetime'
    ];

    /**
     * Relasi ke DataBarang
     */
    public function dataBarang()
    {
        return $this->belongsTo(DataBarang::class, 'kode_brng', 'kode_brng');
    }

    /**
     * Relasi ke Bangsal
     */
    public function bangsal()
    {
        return $this->belongsTo(Bangsal::class, 'kd_bangsal', 'kd_bangsal');
    }

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Static method untuk mencatat transaksi INSERT
     */
    public static function catatInsert($kode_brng, $kd_bangsal, $no_batch, $no_faktur, $stok_baru, $sumber_transaksi, $keterangan = null, $data_lengkap = null)
    {
        return self::create([
            'kode_brng' => $kode_brng,
            'kd_bangsal' => $kd_bangsal,
            'no_batch' => $no_batch ?? '',
            'no_faktur' => $no_faktur ?? '',
            'jenis_transaksi' => 'INSERT',
            'stok_sebelum' => null,
            'stok_sesudah' => $stok_baru,
            'selisih' => $stok_baru,
            'keterangan' => $keterangan,
            'user_id' => Auth::id(),
            'sumber_transaksi' => $sumber_transaksi,
            'data_sebelum' => null,
            'data_sesudah' => $data_lengkap,
            'waktu_transaksi' => now()
        ]);
    }

    /**
     * Static method untuk mencatat transaksi UPDATE
     */
    public static function catatUpdate($kode_brng, $kd_bangsal, $no_batch, $no_faktur, $stok_lama, $stok_baru, $sumber_transaksi, $keterangan = null, $data_sebelum = null, $data_sesudah = null)
    {
        return self::create([
            'kode_brng' => $kode_brng,
            'kd_bangsal' => $kd_bangsal,
            'no_batch' => $no_batch ?? '',
            'no_faktur' => $no_faktur ?? '',
            'jenis_transaksi' => 'UPDATE',
            'stok_sebelum' => $stok_lama,
            'stok_sesudah' => $stok_baru,
            'selisih' => $stok_baru - $stok_lama,
            'keterangan' => $keterangan,
            'user_id' => Auth::id(),
            'sumber_transaksi' => $sumber_transaksi,
            'data_sebelum' => $data_sebelum,
            'data_sesudah' => $data_sesudah,
            'waktu_transaksi' => now()
        ]);
    }

    /**
     * Static method untuk mencatat transaksi DELETE
     */
    public static function catatDelete($kode_brng, $kd_bangsal, $no_batch, $no_faktur, $stok_lama, $sumber_transaksi, $keterangan = null, $data_lengkap = null)
    {
        return self::create([
            'kode_brng' => $kode_brng,
            'kd_bangsal' => $kd_bangsal,
            'no_batch' => $no_batch ?? '',
            'no_faktur' => $no_faktur ?? '',
            'jenis_transaksi' => 'DELETE',
            'stok_sebelum' => $stok_lama,
            'stok_sesudah' => null,
            'selisih' => -$stok_lama,
            'keterangan' => $keterangan,
            'user_id' => Auth::id(),
            'sumber_transaksi' => $sumber_transaksi,
            'data_sebelum' => $data_lengkap,
            'data_sesudah' => null,
            'waktu_transaksi' => now()
        ]);
    }

    /**
     * Scope untuk filter berdasarkan periode
     */
    public function scopePeriode($query, $tanggal_mulai, $tanggal_selesai)
    {
        return $query->whereBetween('waktu_transaksi', [$tanggal_mulai, $tanggal_selesai]);
    }

    /**
     * Scope untuk filter berdasarkan barang
     */
    public function scopeBarang($query, $kode_brng)
    {
        return $query->where('kode_brng', $kode_brng);
    }

    /**
     * Scope untuk filter berdasarkan bangsal
     */
    public function scopeBangsal($query, $kd_bangsal)
    {
        return $query->where('kd_bangsal', $kd_bangsal);
    }

    /**
     * Scope untuk filter berdasarkan jenis transaksi
     */
    public function scopeJenisTransaksi($query, $jenis)
    {
        return $query->where('jenis_transaksi', $jenis);
    }

    /**
     * Scope untuk filter berdasarkan sumber transaksi
     */
    public function scopeSumberTransaksi($query, $sumber)
    {
        return $query->where('sumber_transaksi', $sumber);
    }
}