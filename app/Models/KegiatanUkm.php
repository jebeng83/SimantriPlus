<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanUkm extends Model
{
    protected $table = 'kegiatan_ukm';
    // Izinkan mass assignment untuk fleksibilitas (hati-hati di controller untuk validasi)
    protected $guarded = [];
    public $timestamps = false;
}