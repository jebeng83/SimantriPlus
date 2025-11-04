<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalUkm extends Model
{
    /**
     * Tabel yang digunakan oleh model.
     */
    protected $table = 'jadwal_kegiatan_ukm';

    /**
     * Primary key tabel.
     */
    protected $primaryKey = 'kd_jadwal';

    /**
     * Eloquent timestamps.
     * Set false untuk mencegah Eloquent mengharuskan kolom created_at/updated_at.
     */
    public $timestamps = false;

    /**
     * Kolom yang boleh diisi (sesuai struktur tabel hasil describe).
     */
    protected $fillable = [
        'tanggal',
        'nip',
        'kode',
        'kd_kel',
        'Keterangan',
        'status',
    ];

    /**
     * Casting tipe data untuk kolom tertentu.
     */
    protected $casts = [
        'kd_jadwal' => 'integer',
        'tanggal' => 'date',
        'kode' => 'integer',
        'kd_kel' => 'integer',
        'nip' => 'string',
        'Keterangan' => 'string',
        'status' => 'string',
    ];
}