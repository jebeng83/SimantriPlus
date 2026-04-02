<?php

namespace App\Models\RawatJalan;

use Illuminate\Database\Eloquent\Model;
use App\Models\RawatJalan\ResepDokter;
use App\Models\RawatJalan\RawatJalan;
use Carbon\Carbon;

class ResepObat extends Model
{
    protected $table = 'resep_obat';
    protected $primaryKey = 'no_resep';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'no_resep',
        'tgl_perawatan',
        'jam',
        'no_rawat',
        'kd_dokter',
        'tgl_peresepan',
        'jam_peresepan',
        'status',
        'tgl_penyerahan',
        'jam_penyerahan'
    ];

    protected $casts = [
        'tgl_perawatan' => 'string',
        'jam' => 'string',
        'tgl_peresepan' => 'string',
        'jam_peresepan' => 'string',
        'tgl_penyerahan' => 'string',
        'jam_penyerahan' => 'string',
    ];

    // Relasi ke resep_dokter
    public function resepDokter()
    {
        return $this->hasMany(ResepDokter::class, 'no_resep', 'no_resep');
    }

    // Relasi ke rawat jalan
    public function rawatJalan()
    {
        return $this->belongsTo(RawatJalan::class, 'no_rawat', 'no_rawat');
    }

    // Relasi ke dokter
    public function dokter()
    {
        return $this->belongsTo(\App\Models\Dokter::class, 'kd_dokter', 'kd_dokter');
    }

    // Method untuk generate nomor resep otomatis
    public static function generateNoResep($tanggal = null)
    {
        if (!$tanggal) {
            $tanggal = Carbon::now()->format('Y-m-d');
        }
        
        $prefix = Carbon::parse($tanggal)->format('Ymd');
        
        // Cari nomor resep terakhir untuk tanggal tersebut
        $lastResep = static::where('no_resep', 'like', $prefix . '%')
            ->orderBy('no_resep', 'desc')
            ->first();
        
        if ($lastResep) {
            // Ambil 4 digit terakhir dan tambah 1
            $lastNumber = (int) substr($lastResep->no_resep, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        // Format dengan 4 digit
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    // Method untuk membuat resep baru
    public static function createResep($data)
    {
        $noResep = static::generateNoResep($data['tgl_peresepan'] ?? null);
        
        $resepData = array_merge($data, ['no_resep' => $noResep]);
        
        return static::create($resepData);
    }
}