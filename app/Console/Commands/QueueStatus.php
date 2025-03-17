<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;

class QueueStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'queue:status {queue? : Nama antrian yang ingin diperiksa (default: default)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menampilkan status antrian di aplikasi';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $queueName = $this->argument('queue') ?: 'default';
        $connection = config('queue.default');
        
        $this->info("Memeriksa status antrian: {$queueName} pada koneksi: {$connection}");
        
        switch ($connection) {
            case 'database':
                $this->showDatabaseQueueStatus($queueName);
                break;
            case 'redis':
                $this->showRedisQueueStatus($queueName);
                break;
            case 'sync':
                $this->info("Koneksi antrian 'sync' tidak menyimpan pekerjaan. Pekerjaan dijalankan langsung secara sinkron.");
                break;
            default:
                $this->info("Status untuk koneksi antrian '{$connection}' belum didukung oleh perintah ini.");
                break;
        }
        
        return 0;
    }
    
    /**
     * Menampilkan status antrian database.
     *
     * @param string $queueName
     * @return void
     */
    protected function showDatabaseQueueStatus($queueName)
    {
        $table = config('queue.connections.database.table', 'jobs');
        
        $pending = DB::table($table)
            ->where('queue', $queueName)
            ->count();
            
        $failed = DB::table('failed_jobs')
            ->where('queue', $queueName)
            ->count();
            
        $this->table(
            ['Status', 'Jumlah'],
            [
                ['Menunggu', $pending],
                ['Gagal', $failed],
            ]
        );
    }
    
    /**
     * Menampilkan status antrian Redis.
     *
     * @param string $queueName
     * @return void
     */
    protected function showRedisQueueStatus($queueName)
    {
        $prefix = config('queue.connections.redis.queue');
        $queueKey = "{$prefix}:{$queueName}";
        
        $pending = 0;
        
        try {
            $redis = Redis::connection(config('queue.connections.redis.connection', 'default'));
            $pending = $redis->llen($queueKey);
            
            $this->table(
                ['Status', 'Jumlah'],
                [
                    ['Menunggu', $pending],
                ]
            );
        } catch (\Exception $e) {
            $this->error("Tidak dapat terhubung ke Redis: " . $e->getMessage());
        }
    }
} 