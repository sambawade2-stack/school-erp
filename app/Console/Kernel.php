<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\BackupService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // ✅ Backup automatique chaque jour à 2h du matin
        $schedule->call(function () {
            app(BackupService::class)->backup();
        })
        ->daily()
        ->at('02:00')
        ->name('backup-database')
        ->withoutOverlapping()
        ->onFailure(function () {
            \Illuminate\Support\Facades\Log::error('Backup automatique échoué');
        })
        ->onSuccess(function () {
            \Illuminate\Support\Facades\Log::info('Backup automatique complété');
        });

        // ✅ Nettoyer les logs au bout de 7 jours
        $schedule->command('log:clear')
            ->weekly()
            ->monday()
            ->at('03:00')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
