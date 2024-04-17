<?php

namespace App\Console;

use Commands\EditSuperAdmins;
use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\ChangeMicroappAcceptStatus;
use App\Console\Commands\UpdateEDirectorate;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();

        $schedule->command('microapps:accept_not')
            ->daily();
            // ->appendOutputTo(storage_path('logs/custom_cron_commands.log'));
        // ->dailyAt('19:03');

        $schedule->command('change-active-month')
            ->monthly();
            // ->appendOutputTo(storage_path('logs/custom_cron_commands.log'));

        // $schedule->command('update-e-directorate')
        //     ->dailyAt('16:00');

        $schedule->command('queue:work --stop-when-empty')
            ->everyMinute(); 
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
