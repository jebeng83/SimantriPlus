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
        // ID dan Informasi Dasar
        'id_anc',
        'no_rawat',
        'no_rkm_medis',
        'id_hamil',
        'tanggal_anc',
        'diperiksa_oleh',
        
        // Informasi Kunjungan
        'usia_kehamilan',
        'trimester',
        'kunjungan_ke',
        'keadaan_pulang',
        
        // 1. Anamnesis
        'keluhan_utama',
        'gravida',
        'partus',
        'abortus',
        'hidup',
        'riwayat_penyakit',
        
        // 2. Pemeriksaan Fisik - BB & TB (T1)
        'berat_badan',
        'tinggi_badan',
        'imt',
        'kategori_imt',
        'jumlah_janin',
        
        // 3. Status Gizi (T3)
        'lila',
        'status_gizi',
        
        // 4. Tekanan Darah (T2)
        'td_sistole',
        'td_diastole',
        
        // 5. Tinggi Fundus Uteri (T4)
        'tinggi_fundus',
        'taksiran_berat_janin',
        
        // 6. DJJ dan Presentasi (T5)
        'denyut_jantung_janin',
        'presentasi',
        'presentasi_janin',
        
        // 7. Status Imunisasi TT (T6)
        'status_tt',
        'imunisasi_tt',
        'tanggal_imunisasi',
        
        // 8. Tablet Fe (T7)
        'jumlah_fe',
        'dosis',
        
        // 9. Pemeriksaan Lab (T8)
        'tanggal_lab',
        'lab',
        'hasil_pemeriksaan_hb',
        'hasil_pemeriksaan_urine_protein',
        'hasil_pemeriksaan_urine_reduksi',
        'pemeriksaan_lab',
        'rujukan_ims',
        'perawatan_payudara',
        
        // 10. Tatalaksana Kasus (T9)
        'jenis_tatalaksana',
        
        // Tatalaksana - Anemia
        'diberikan_tablet_fe',
        'jumlah_tablet_dikonsumsi',
        'jumlah_tablet_ditambahkan',
        'tatalaksana_lainnya',
        
        // Tatalaksana - Makanan Tambahan
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
        
        // 11. Konseling / Temu Wicara (T10)
        'materi',
        'rekomendasi',
        'konseling_menyusui',
        'tanda_bahaya_kehamilan',
        'tanda_bahaya_persalinan',
        'konseling_phbs',
        'konseling_gizi',
        'konseling_ibu_hamil',
        'konseling_lainnya',
        
        // 12. Tindak Lanjut
        'tindak_lanjut',
        'detail_tindak_lanjut',
        'tanggal_kunjungan_berikutnya',
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
