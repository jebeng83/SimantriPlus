<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisSekolah extends Model
{
    use HasFactory;

    protected $table = 'jenis_sekolah';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'nama',
        'keterangan'
    ];

    /**
     * Relasi dengan sekolah
     */
    public function dataSekolah()
    {
        return $this->hasMany(DataSekolah::class, 'id_jenis_sekolah', 'id');
    }
}