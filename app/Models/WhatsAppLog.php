<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class WhatsAppLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'phone_number',
        'message_type',
        'message_content',
        'document_path',
        'status',
        'response_data',
        'error_message',
        'gateway_message_id',
        'whatsapp_message_id',
        'sent_at',
        'delivered_at',
        'retry_count'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'response_data' => 'array'
    ];

    // Scope untuk filter berdasarkan status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope untuk filter berdasarkan nomor telepon
    public function scopeByPhoneNumber($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    // Scope untuk filter berdasarkan tanggal
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    // Method untuk menandai pesan sebagai terkirim
    public function markAsSent($gatewayMessageId = null, $whatsappMessageId = null)
    {
        $this->update([
            'status' => 'sent',
            'sent_at' => Carbon::now(),
            'gateway_message_id' => $gatewayMessageId,
            'whatsapp_message_id' => $whatsappMessageId
        ]);
    }

    // Method untuk menandai pesan sebagai terdelivered
    public function markAsDelivered()
    {
        $this->update([
            'status' => 'delivered',
            'delivered_at' => Carbon::now()
        ]);
    }

    // Method untuk menandai pesan sebagai gagal
    public function markAsFailed($errorMessage)
    {
        $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage
        ]);
    }

    // Method untuk increment retry count
    public function incrementRetry()
    {
        $this->increment('retry_count');
    }
}
