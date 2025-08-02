<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class AntreanBpjsLog extends Model
{
    use HasFactory;
    
    protected $table = 'antrean_bpjs_log';
    
    protected $fillable = [
        'no_rawat',
        'no_rkm_medis', 
        'status',
        'response'
    ];
    
    // Only use created_at timestamp
    public $timestamps = true;
    const UPDATED_AT = null;
    
    // Override touch method to only update created_at
    public function touch($attribute = null)
    {
        if ($attribute) {
            $this->$attribute = $this->freshTimestamp();
        } else {
            $this->created_at = $this->freshTimestamp();
        }
        
        return $this->save();
    }
    
    /**
     * Log BPJS antrean activity safely
     */
    public static function logActivity($data)
    {
        try {
            // Strictly filter data to only include valid columns
            $logData = [];
            
            if (isset($data['no_rawat'])) {
                $logData['no_rawat'] = $data['no_rawat'];
            }
            
            if (isset($data['no_rkm_medis'])) {
                $logData['no_rkm_medis'] = $data['no_rkm_medis'];
            }
            
            // Handle status - convert from old format if needed
            if (isset($data['status'])) {
                $logData['status'] = $data['status'];
            } elseif (isset($data['action'])) {
                // Convert old 'action' field to 'status'
                $logData['status'] = $data['action'];
            } else {
                $logData['status'] = 'Unknown';
            }
            
            // Handle response - combine old response_data and request_data if needed
            if (isset($data['response'])) {
                $logData['response'] = is_array($data['response']) ? json_encode($data['response']) : $data['response'];
            } elseif (isset($data['response_data']) || isset($data['request_data'])) {
                // Combine old format data
                $responseData = [
                    'request' => $data['request_data'] ?? null,
                    'response' => $data['response_data'] ?? null,
                    'success' => $data['success'] ?? false,
                    'error_message' => $data['error_message'] ?? null
                ];
                $logData['response'] = json_encode($responseData);
            }
            
            return self::create($logData);
        } catch (\Exception $e) {
            \Log::error('Failed to log BPJS antrean activity: ' . $e->getMessage(), [
                'filtered_data' => $logData ?? [],
                'original_data' => $data,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
