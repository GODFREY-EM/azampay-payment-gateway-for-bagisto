<?php

namespace Webkul\AzamPay\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * AzamPay Service Provider
 * Handles loading of routes, translations, views, configs, and asset publishing for the AzamPay payment integration.
 */
class AzamPayServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');
        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'azampay');

        $viewsPath = __DIR__ . '/../Resources/views';
        if (is_dir($viewsPath)) {
            $this->loadViewsFrom($viewsPath, 'azampay');
            $this->publishes([
                $viewsPath => resource_path('views/vendor/azampay'),
            ], 'azampay-views');
        }

        $this->publishes([
            dirname(__DIR__) . '/Config/paymentmethods.php' => config_path('azampay-paymentmethods.php'),
            dirname(__DIR__) . '/Config/system.php' => config_path('azampay-system.php'),
        ], 'azampay-config');

        $assetPath = __DIR__ . '/../Resources/assets';
        if (is_dir($assetPath)) {
            $this->publishes([
                $assetPath => public_path('vendor/azampay'),
            ], 'azampay-assets');
        }

        if (file_exists($helpers = __DIR__ . '/../Helpers/AzamPayHelper.php')) {
            require_once $helpers;
        }
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
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php',
            'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }
}
