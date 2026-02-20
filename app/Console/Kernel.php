<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Process auction starting every minute
        $schedule->job(new \App\Jobs\ProcessAuctionStarting)->everyMinute();
        
        // Process auction ending every minute
        $schedule->job(new \App\Jobs\ProcessAuctionEnding)->everyMinute();
        
        // Process finalization timeout every hour
        $schedule->job(new \App\Jobs\ProcessFinalizationTimeout)->hourly();
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
