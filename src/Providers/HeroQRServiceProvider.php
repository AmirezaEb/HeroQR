<?php

namespace HeroQR\Providers;

use Illuminate\Support\ServiceProvider;
use HeroQR\Core\QRCodeGenerator;


class HeroQRServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // If you have any routes or config files, you can load them here
        // For example: $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the QrCodeGenerator class as a singleton
        $this->app->singleton(QrCodeGenerator::class, function ($app) {
            return new QrCodeGenerator();
        });
    }
}
