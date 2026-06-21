<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
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
        // Railway termina el SSL en su proxy y reenvía el tráfico a tu app
        // por HTTP internamente. Sin esto, Laravel genera URLs de formularios
        // y assets en http:// aunque el usuario esté en https://, lo que
        // dispara la advertencia "información no protegida" del navegador.
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }
    }
}