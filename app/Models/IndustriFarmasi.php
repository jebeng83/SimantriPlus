<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IndustriFarmasi extends Model
{
    // Map to legacy table name
    protected $table = 'industrifarmasi';

    // Primary key is a non-incrementing string
    protected $primaryKey = 'kode_industri';
    public $incrementing = false;
    protected $keyType = 'string';

    // Table does not have timestamps
    public $timestamps = false;

    protected $fillable = [
        'kode_industri',
        'nama_industri',
        'alamat',
        'kota',
        'no_telp',
    ];
}