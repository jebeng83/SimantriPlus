<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahasaPasien extends Model
{
    protected $table = 'bahasa_pasien';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nama_bahasa'
    ];
} 