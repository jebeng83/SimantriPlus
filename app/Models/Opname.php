<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Opname extends Model
{
    use HasFactory;

    protected $table = 'opname';
    protected $primaryKey = null;
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'kode_brng',
        'h_beli',
        'tanggal',
        'stok',
        'real',
        'selisih',
        'lebih',
        'nomihilang',
        'nomilebih',
        'keterangan',
        'kd_bangsal',
        'no_batch',
        'no_faktur'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'h_beli' => 'decimal:2',
        'stok' => 'decimal:2',
        'real' => 'decimal:2',
        'selisih' => 'decimal:2',
        'lebih' => 'decimal:2',
        'nomihilang' => 'decimal:2',
        'nomilebih' => 'decimal:2'
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
     * Get data opname dengan join ke tabel terkait
     */
    public static function getOpnameData($kdBangsal = null, $tanggal = null)
    {
        $query = DB::table('opname')
            ->select(
                'opname.kode_brng',
                'databarang.nama_brng',
                'opname.h_beli',
                'databarang.kode_sat',
                'opname.tanggal',
                'opname.stok',
                'opname.real',
                'opname.selisih',
                'opname.lebih',
                DB::raw('(opname.real * opname.h_beli) as totalreal'),
                'opname.nomihilang',
                'opname.nomilebih',
                'opname.keterangan',
                'bangsal.kd_bangsal',
                'bangsal.nm_bangsal',
                'opname.no_batch',
                'opname.no_faktur',
                'jenis.nama as jenis_nama'
            )
            ->join('databarang', 'opname.kode_brng', '=', 'databarang.kode_brng')
            ->join('bangsal', 'opname.kd_bangsal', '=', 'bangsal.kd_bangsal')
            ->join('jenis', 'databarang.kdjns', '=', 'jenis.kdjns')
            ->join('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->join('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode');

        if ($kdBangsal) {
            $query->where('opname.kd_bangsal', $kdBangsal);
        }

        if ($tanggal) {
            $query->whereDate('opname.tanggal', $tanggal);
        }

        return $query->orderBy('opname.tanggal', 'desc')
                    ->orderBy('databarang.nama_brng')
                    ->get();
    }

    /**
     * Calculate selisih dan nominal
     */
    public function calculateDifference()
    {
        $this->selisih = $this->real - $this->stok;
        
        if ($this->selisih > 0) {
            $this->lebih = $this->selisih;
            $this->nomilebih = $this->lebih * $this->h_beli;
            $this->nomihilang = 0;
        } else {
            $this->lebih = 0;
            $this->nomilebih = 0;
            $this->nomihilang = abs($this->selisih) * $this->h_beli;
        }
    }
}