<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\AutoGenerateSavings::class,
    ];

    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('savings:auto-generate')->monthlyOn(1, '01:00');
        $schedule->command('savings:auto-generate')->dailyAt('10:00');

    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}