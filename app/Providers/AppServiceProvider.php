<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL; // <-- Tambahkan baris ini

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
        // Paksa Laravel pakai HTTPS kalau lagi jalan di Vercel/Production
        if (config('app.env') === 'production' || isset($_SERVER['IS_VERCEL'])) {
            URL::forceScheme('https');
        }
    }
}