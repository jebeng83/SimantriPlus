<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ResetKunjunganSehat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ckg:reset-kunjungan-sehat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset kolom kunjungan_sehat di tabel skrining_pkg menjadi 0 setiap awal bulan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Log start process
            $this->info('Memulai proses reset kunjungan_sehat...');
            Log::info('Starting reset kunjungan_sehat process');
            
            // Get count before reset
            $countBefore = DB::table('skrining_pkg')
                ->where('kunjungan_sehat', '1')
                ->count();
            
            // Reset all kunjungan_sehat to 0
            $affected = DB::table('skrining_pkg')
                ->where('kunjungan_sehat', '1')
                ->update([
                    'kunjungan_sehat' => '0',
                    'updated_at' => now()
                ]);
            
            // Log results
            $message = "Reset kunjungan_sehat berhasil. {$affected} record diupdate dari {$countBefore} record yang memiliki kunjungan_sehat = 1";
            $this->info($message);
            Log::info($message);
            
            return Command::SUCCESS;
            
        } catch (\Exception $e) {
            $errorMessage = 'Error saat reset kunjungan_sehat: ' . $e->getMessage();
            $this->error($errorMessage);
            Log::error($errorMessage, [
                'exception' => $e->getTraceAsString()
            ]);
            
            return Command::FAILURE;
        }
    }
}