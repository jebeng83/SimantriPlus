<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianDipanggil implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $no_reg;
    public $nama;
    public $poli;
    public $is_ulang;

    public function __construct($data)
    {
        $this->no_reg = $data['no_reg'];
        $this->nama = $data['nama'];
        $this->poli = $data['poli'];
        $this->is_ulang = $data['is_ulang'];
    }

    public function broadcastOn()
    {
        return new Channel('antrian');
    }

    public function broadcastAs()
    {
        return 'antrian.dipanggil';
    }
} 