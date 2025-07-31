<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    use HasFactory;

    protected $table = 'data_posyandu';
    protected $primaryKey = 'id';
    protected $fillable = [
        'thn',
        'kode_posyandu',
        'nama_posyandu',
        'alamat',
        'desa',
        'no_telp',
        'jumlah_kader',
        'jumlah_kk',
        'jumlah_bumil',
        'jumlah_balita',
        'jumlah_pra_sekolah',
        'jumlah_remaja',
        'jumlah_produktif',
        'jumlah_lansia'
    ];
}