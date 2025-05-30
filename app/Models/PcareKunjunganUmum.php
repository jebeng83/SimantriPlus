class PcareKunjunganUmum extends Model
{
    protected $table = 'pcare_kunjungan_umum';
    protected $primaryKey = 'no_rawat';
    public $incrementing = false;
    
    protected $fillable = [
        'no_rawat',
        'noKunjungan',
        'tglDaftar',
        // ... semua kolom sesuai tabel
        'status'
    ];
    
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
    
    public function poli()
    {
        return $this->belongsTo(Poliklinik::class, 'kdPoli', 'kd_poli');
    }
    
    public function dokter()
    {
        return $this->belongsTo(Dokter::class, 'kdDokter', 'kd_dokter');
    }
}
