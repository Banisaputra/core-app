<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ->everyMinute()              // Setiap menit (development only) 
// ->daily()                    // Setiap hari tengah malam
// ->dailyAt('03:00')           // Setiap hari di jam tertentu (custom)
// ->hourly()                   // Setiap jam
// ->everySixHours()            // Setiap 6 jam
// ->twiceDaily(9, 17)          // Jam 9 pagi dan 5 sore

// ->command('savings:auto-generate')
//         ->hourly()
//         ->appendOutputTo(storage_path('logs/auto_saving.log'))
//         ->onOneServer(); // ← Penting untuk production

Schedule::command('savings:auto-generate')->hourly()
    ->appendOutputTo(storage_path('logs/auto_saving.log')); //for develop only

