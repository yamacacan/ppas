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
        // Taglenmemiş aktiviteleri her dakika kontrol et ve tagle
        // withoutOverlapping: Önceki işlem bitmeden yenisi başlamaz
        // runInBackground: Arka planda çalışır
        $schedule->command('performance:auto-tag-new --limit=5000')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();

        // Browser datalarını senkronize et (URL eşleştirme)
        $schedule->command('performance:sync-browser-data')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground();
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
