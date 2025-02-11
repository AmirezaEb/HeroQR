<?php

namespace HeroQR\Providers;

use HeroQR\Core\QRCodeGenerator;
use Illuminate\Support\ServiceProvider;

/**
 * Class HeroQRServiceProvider
 * 
 * This service provider is responsible for registering and bootstrapping the QR code generation services 
 * for the HeroQR package in a Laravel application. It includes registering the QRCodeGenerator class as 
 * a singleton, which ensures that the same instance is used throughout the application. 
 * Additionally, this class can be extended to load routes, configuration files, and other services 
 * needed by the HeroQR package.
 * 
 * @package HeroQR\Providers
 */

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
