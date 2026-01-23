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
        // Taglenmemiş aktiviteleri kuyruğa gönderir (CPU Lock önlemek için asenkron)
        $schedule->command('performance:auto-tag-new --limit=5000')
            ->everyMinute()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();

        // Browser datalarını senkronize et (URL eşleştirme)
        $schedule->command('performance:sync-browser-data')
            ->everyFiveMinutes()
            ->onOneServer()
            ->withoutOverlapping()
            ->runInBackground();

        // Özet tabloyu her gün gece 00:05'te bir önceki gün için doldur
        $schedule->command('activities:sync-summaries --days=1')
            ->dailyAt('00:05')
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
