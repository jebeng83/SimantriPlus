<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WaOutbox extends Model
{
    use HasFactory;

    protected $table = 'wa_outbox';
    protected $primaryKey = 'nomor';
    public $timestamps = false;

    protected $fillable = [
        'nowa',
        'pesan',
        'tanggal_jam',
        'status',
        'source',
        'sender',
        'success',
        'response',
        'request',
        'type',
        'file'
    ];

    protected $casts = [
        'tanggal_jam' => 'datetime',
        'nomor' => 'integer'
    ];

    // Status constants
    const STATUS_ANTRIAN = 'ANTRIAN';
    const STATUS_TERKIRIM = 'TERKIRIM';
    const STATUS_GAGAL = 'GAGAL';
    const STATUS_PROSES = 'PROSES';

    // Type constants
    const TYPE_TEXT = 'TEXT';
    const TYPE_IMAGE = 'IMAGE';
    const TYPE_VIDEO = 'VIDEO';
    const TYPE_DOCUMENT = 'DOCUMENT';

    // Sender constants
    const SENDER_NODEJS = 'NODEJS';
    const SENDER_QISCUS = 'QISCUS';
    const SENDER_ANY = 'ANY';

    /**
     * Scope untuk pesan dalam antrian
     */
    public function scopeAntrian($query)
    {
        return $query->where('status', self::STATUS_ANTRIAN);
    }

    /**
     * Scope untuk pesan terkirim
     */
    public function scopeTerkirim($query)
    {
        return $query->where('status', self::STATUS_TERKIRIM);
    }

    /**
     * Scope untuk pesan gagal
     */
    public function scopeGagal($query)
    {
        return $query->where('status', self::STATUS_GAGAL);
    }

    /**
     * Scope untuk pesan berdasarkan nomor WhatsApp
     */
    public function scopeByNowa($query, $nowa)
    {
        return $query->where('nowa', $nowa);
    }

    /**
     * Scope untuk pesan berdasarkan sender
     */
    public function scopeBySender($query, $sender)
    {
        return $query->where('sender', $sender);
    }

    /**
     * Scope untuk pesan berdasarkan tipe
     */
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Format nomor WhatsApp
     */
    public function setNowaAttribute($value)
    {
        // Format nomor WhatsApp (hapus karakter non-numerik)
        $phone = preg_replace('/[^0-9]/', '', $value);
        
        // Hapus awalan 0 dan ganti dengan kode negara Indonesia (62)
        if (substr($phone, 0, 1) === '0') {
            $phone = '62' . substr($phone, 1);
        }
        
        // Jika belum ada kode negara, tambahkan kode negara Indonesia
        if (substr($phone, 0, 2) !== '62') {
            $phone = '62' . $phone;
        }
        
        $this->attributes['nowa'] = $phone;
    }

    /**
     * Set tanggal jam otomatis jika kosong
     */
    public function setTanggalJamAttribute($value)
    {
        $this->attributes['tanggal_jam'] = $value ?? now();
    }

    /**
     * Tandai pesan sebagai terkirim
     */
    public function markAsSent($response = null)
    {
        $this->update([
            'status' => self::STATUS_TERKIRIM,
            'success' => '1',
            'response' => $response
        ]);
    }

    /**
     * Tandai pesan sebagai gagal
     */
    public function markAsFailed($response = null)
    {
        $this->update([
            'status' => self::STATUS_GAGAL,
            'success' => '0',
            'response' => $response
        ]);
    }

    /**
     * Tandai pesan sedang diproses
     */
    public function markAsProcessing()
    {
        $this->update([
            'status' => self::STATUS_PROSES
        ]);
    }

    /**
     * Buat pesan baru dalam antrian
     */
    public static function createMessage($nowa, $pesan, $options = [])
    {
        return self::create(array_merge([
            'nowa' => $nowa,
            'pesan' => $pesan,
            'tanggal_jam' => now(),
            'status' => self::STATUS_ANTRIAN,
            'sender' => self::SENDER_NODEJS,
            'type' => self::TYPE_TEXT,
            'source' => 'EDOKTER'
        ], $options));
    }

    /**
     * Ambil pesan antrian berikutnya
     */
    public static function getNextInQueue($sender = null)
    {
        $query = self::antrian()->orderBy('tanggal_jam', 'asc');
        
        if ($sender) {
            $query->where(function($q) use ($sender) {
                $q->where('sender', $sender)
                  ->orWhere('sender', self::SENDER_ANY);
            });
        }
        
        return $query->first();
    }

    /**
     * Statistik pesan
     */
    public static function getStats($days = 7)
    {
        $startDate = now()->subDays($days);
        
        return [
            'total' => self::where('tanggal_jam', '>=', $startDate)->count(),
            'antrian' => self::antrian()->where('tanggal_jam', '>=', $startDate)->count(),
            'terkirim' => self::terkirim()->where('tanggal_jam', '>=', $startDate)->count(),
            'gagal' => self::gagal()->where('tanggal_jam', '>=', $startDate)->count(),
            'success_rate' => self::getSuccessRate($days)
        ];
    }

    /**
     * Tingkat keberhasilan pengiriman
     */
    public static function getSuccessRate($days = 7)
    {
        $startDate = now()->subDays($days);
        
        $total = self::where('tanggal_jam', '>=', $startDate)
                    ->whereIn('status', [self::STATUS_TERKIRIM, self::STATUS_GAGAL])
                    ->count();
        
        if ($total == 0) return 0;
        
        $success = self::terkirim()
                      ->where('tanggal_jam', '>=', $startDate)
                      ->count();
        
        return round(($success / $total) * 100, 2);
    }
}