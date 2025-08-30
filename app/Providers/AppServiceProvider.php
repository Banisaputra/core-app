<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // for ngrok only
        if (env('APP_ENV') === 'local' && strpos(env('APP_URL'), 'ngrok-free.app') !== false) {
            URL::forceScheme('https');
        }
    }
}
