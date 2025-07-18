<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('logs:delete-old')->twiceDaily(1, 13)
            ->appendOutputTo(storage_path('logs/scheduler.log'));
            
        // Reset kunjungan_sehat setiap tanggal 1 jam 00:00:00
        $schedule->command('ckg:reset-kunjungan-sehat')
            ->monthlyOn(1, '00:00')
            ->appendOutputTo(storage_path('logs/reset-kunjungan-sehat.log'));
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
