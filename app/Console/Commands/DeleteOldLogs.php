<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DeleteOldLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logs:delete-old {--days=2 : Jumlah hari untuk menyimpan log}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Menghapus file log yang lebih tua dari jumlah hari tertentu';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);
        $logPath = storage_path('logs');
        $deletedCount = 0;
        $skippedCount = 0;

        $this->info("Menghapus log yang lebih tua dari {$days} hari...");

        // Ambil semua file di direktori logs
        $files = File::files($logPath);

        foreach ($files as $file) {
            // Lewati .gitignore
            if (basename($file) === '.gitignore') {
                continue;
            }

            // Ambil waktu modifikasi terakhir file
            $lastModified = Carbon::createFromTimestamp(filemtime($file));

            // Jika file lebih tua dari waktu cutoff, hapus file
            if ($lastModified->isBefore($cutoffDate)) {
                $fileName = basename($file);
                try {
                    File::delete($file);
                    $this->line("Berhasil menghapus: {$fileName}");
                    $deletedCount++;
                } catch (\Exception $e) {
                    $this->error("Gagal menghapus {$fileName}: {$e->getMessage()}");
                    $skippedCount++;
                }
            } else {
                $skippedCount++;
            }
        }

        $this->info("Selesai! {$deletedCount} file log berhasil dihapus, {$skippedCount} file dilewati.");
        Log::info("Pembersihan log: {$deletedCount} file dihapus, {$skippedCount} file dilewati.");

        return Command::SUCCESS;
    }
}
