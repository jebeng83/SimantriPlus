<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SetPenjualanUmum extends Model
{
    use HasFactory;

    protected $table = 'setpenjualanumum';
    public $timestamps = false;
    public $incrementing = false;
    protected $primaryKey = null;

    protected $fillable = [
        'ralan',
        'kelas1',
        'kelas2',
        'kelas3',
        'utama',
        'vip',
        'vvip',
        'beliluar',
        'jualbebas',
        'karyawan'
    ];

    protected $casts = [
        'ralan' => 'double',
        'kelas1' => 'double',
        'kelas2' => 'double',
        'kelas3' => 'double',
        'utama' => 'double',
        'vip' => 'double',
        'vvip' => 'double',
        'beliluar' => 'double',
        'jualbebas' => 'double',
        'karyawan' => 'double'
    ];
}