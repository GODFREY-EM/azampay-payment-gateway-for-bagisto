<?php

namespace Webkul\AzamPay\Providers;

use Illuminate\Support\ServiceProvider;

class AzamPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');  // Ensure you have a proper routes file for AzamPay

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'azampay');  // Make sure AzamPay translations are in place

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'azampay');  // Views related to AzamPay (payment gateway page, etc.)
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Register package config.
     */
    protected function registerConfig(): void
    {
        // Merges AzamPay specific payment method configurations
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/paymentmethods.php', 'payment_methods'
        );

        // Optionally, merge other configs if needed (e.g., system config, core config)
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/system.php', 'core'
        );
    }
}
