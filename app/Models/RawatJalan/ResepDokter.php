<?php

namespace App\Models\RawatJalan;

use Illuminate\Database\Eloquent\Model;
use App\Models\RawatJalan\Databarang;
use App\Models\RawatJalan\ResepObat;

class ResepDokter extends Model
{
    protected $table = 'resep_dokter';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'no_resep',
        'kode_brng',
        'jml',
        'aturan_pakai'
    ];

    protected $casts = [
        'jml' => 'double'
    ];

    // Relasi ke resep_obat
    public function resepObat()
    {
        return $this->belongsTo(ResepObat::class, 'no_resep', 'no_resep');
    }

    // Relasi ke databarang
    public function databarang()
    {
        return $this->belongsTo(Databarang::class, 'kode_brng', 'kode_brng');
    }

    // Method untuk menyimpan detail resep
    public static function saveResepDetail($noResep, $obatList)
    {
        $savedItems = [];
        
        foreach ($obatList as $obat) {
            $resepDetail = static::create([
                'no_resep' => $noResep,
                'kode_brng' => $obat['kode_brng'],
                'jml' => $obat['jml'],
                'aturan_pakai' => $obat['aturan_pakai'] ?? ''
            ]);
            
            $savedItems[] = $resepDetail;
        }
        
        return $savedItems;
    }

    // Method untuk mendapatkan detail resep dengan informasi obat
    public static function getResepDetail($noResep)
    {
        return static::with('databarang')
            ->where('no_resep', $noResep)
            ->get();
    }
}