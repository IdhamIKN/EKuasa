<?php
// app/Providers/AppServiceProvider.php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register WhatsApp Service
        $this->app->singleton(\App\Services\WhatsAppService::class, function ($app) {
            return new \App\Services\WhatsAppService();
        });

        // Register PDF Service
        $this->app->singleton(\App\Services\PDFService::class, function ($app) {
            return new \App\Services\PDFService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Bootstrap for pagination
        Paginator::useBootstrapFive();
    }
}
