<?php

namespace App\Console;

use Commands\EditSuperAdmins;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ChangeMicroappAcceptStatus;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('microapps:accept_not')->daily();
        // ->dailyAt('19:03');
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
