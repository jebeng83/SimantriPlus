<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PemeriksaanAnc extends Model
{
    use HasFactory;
    
    /**
     * Nama tabel yang terkait dengan model.
     *
     * @var string
     */
    protected $table = 'pemeriksaan_anc';
    
    /**
     * Primary key table.
     *
     * @var string
     */
    protected $primaryKey = 'id_anc';
    
    /**
     * Tipe data primary key.
     *
     * @var string
     */
    protected $keyType = 'string';
    
    /**
     * Indikasi apakah ID auto-increment.
     *
     * @var bool
     */
    public $incrementing = false;
    
    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array
     */
    protected $fillable = [
        'id_anc',
        'no_rawat',
        'no_rkm_medis',
        'id_hamil',
        'tanggal_anc',
        'diperiksa_oleh',
        'usia_kehamilan',
        'trimester',
        'kunjungan_ke',
        'berat_badan',
        'tinggi_badan',
        'lila',
        'imt',
        'kategori_imt',
        'jumlah_janin',
        'td_sistole',
        'td_diastole',
        'jumlah_fe',
        'dosis',
        'pemeriksaan_lab',
        'jenis_tatalaksana',
        'materi',
        'rekomendasi',
        'konseling_menyusui',
        'tanda_bahaya_kehamilan',
        'tanda_bahaya_persalinan',
        'konseling_phbs',
        'konseling_gizi',
        'konseling_ibu_hamil',
        'konseling_lainnya',
        'keadaan_pulang',
        // Data anamnesis
        'keluhan_utama',
        'gravida',
        'partus',
        'abortus',
        'hidup',
        'riwayat_penyakit',
        // Data pemeriksaan fisik
        'status_gizi',
        'tfu',
        'taksiran_berat_janin',
        'djj',
        'presentasi',
        'status_tt',
        'tanggal_imunisasi',
        // Lab
        'tanggal_lab',
        'lab',
        'rujukan_ims',
        // Tindak lanjut
        'tindak_lanjut',
        'detail_tindak_lanjut',
        'tanggal_kunjungan_berikutnya',
        // Tatalaksana - Anemia
        'diberikan_tablet_fe',
        'jumlah_tablet_dikonsumsi',
        'jumlah_tablet_ditambahkan',
        'tatalaksana_lainnya',
        // Tatalaksana - Makanan Tambahan Ibu Hamil
        'pemberian_mt',
        'jumlah_mt',
        // Tatalaksana - Hipertensi
        'pantau_tekanan_darah',
        'pantau_protein_urine',
        'pantau_kondisi_janin',
        'hipertensi_lainnya',
        // Tatalaksana - Eklampsia
        'pantau_tekanan_darah_eklampsia',
        'pantau_protein_urine_eklampsia',
        'pantau_kondisi_janin_eklampsia',
        'pemberian_antihipertensi',
        'pemberian_mgso4',
        'pemberian_diazepam',
        // Tatalaksana - KEK
        'edukasi_gizi',
        'kek_lainnya',
        // Tatalaksana - Obesitas
        'edukasi_gizi_obesitas',
        'obesitas_lainnya',
        // Tatalaksana - Infeksi
        'pemberian_antipiretik',
        'pemberian_antibiotik',
        'infeksi_lainnya',
        // Tatalaksana - Penyakit Jantung
        'edukasi',
        'jantung_lainnya',
        // Tatalaksana - HIV
        'datang_dengan_hiv',
        'persalinan_pervaginam',
        'persalinan_perapdoinam',
        'ditawarkan_tes',
        'dilakukan_tes',
        'hasil_tes_hiv',
        'mendapatkan_art',
        'vct_pict',
        'periksa_darah',
        'serologi',
        'arv_profilaksis',
        'hiv_lainnya',
        // Tatalaksana - TB
        'diperiksa_dahak',
        'tbc',
        'obat_tb',
        'sisa_obat',
        'tb_lainnya',
        // Tatalaksana - Malaria
        'diberikan_kelambu',
        'darah_malaria_rdt',
        'darah_malaria_mikroskopis',
        'ibu_hamil_malaria_rdt',
        'ibu_hamil_malaria_mikroskopis',
        'hasil_test_malaria',
        'obat_malaria',
        'malaria_lainnya',
    ];
    
    /**
     * Atribut yang harus dikonversi ke tipe tertentu.
     *
     * @var array
     */
    protected $casts = [
        'tanggal_anc' => 'datetime',
        'tanggal_imunisasi' => 'date',
        'tanggal_lab' => 'date',
        'tanggal_kunjungan_berikutnya' => 'date',
        'riwayat_penyakit' => 'json',
        'lab' => 'json',
    ];
    
    /**
     * Boot method untuk model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Event untuk auto-generate id_anc sebelum create
        static::creating(function ($model) {
            if (empty($model->id_anc)) {
                $model->id_anc = self::generateIdAnc();
            }
        });
    }
    
    /**
     * Generate ID ANC baru dengan format ANC+4 angka
     */
    public static function generateIdAnc(): string
    {
        // Cari ID terakhir dengan prefix ANC
        $lastId = DB::table('pemeriksaan_anc')
            ->where('id_anc', 'like', 'ANC%')
            ->orderBy('id_anc', 'desc')
            ->value('id_anc');
            
        if ($lastId) {
            // Ambil angka dari ID terakhir
            $lastNumber = (int) substr($lastId, 3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }
        
        // Format angka dengan leading zero
        return 'ANC' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Relasi dengan pasien
     */
    public function pasien()
    {
        return $this->belongsTo(Pasien::class, 'no_rkm_medis', 'no_rkm_medis');
    }
    
    /**
     * Relasi dengan registrasi pasien
     */
    public function regPeriksaRalan()
    {
        return $this->belongsTo(RegPeriksa::class, 'no_rawat', 'no_rawat');
    }
    
    /**
     * Relasi dengan data ibu hamil
     */
    public function ibuHamil()
    {
        return $this->belongsTo(DataIbuHamil::class, 'id_hamil', 'id_hamil');
    }
}
