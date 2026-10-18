<?php

namespace Waply;

use Illuminate\Support\ServiceProvider;

class WaplyServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap package services.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/waply.php' => config_path('waply.php'),
            ], 'waply-config');
        }
    }

    /**
     * Register package services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/waply.php', 'waply');

        $this->app->singleton('waply', function ($app) {
            $config = $app['config']['waply'] ?? [];

            $apiKey = $config['api_key'] ?? env('WAPLY_API_KEY', '');
            $baseUrl = $config['base_url'] ?? env('WAPLY_BASE_URL', 'https://waply.web.id');
            $deviceId = $config['device_id'] ?? env('WAPLY_DEVICE_ID', 'auto_rotate');
            $timeout = (int) ($config['timeout'] ?? 30);

            return new WaplyClient($apiKey, $baseUrl, $deviceId, $timeout);
        });

        $this->app->alias('waply', WaplyClient::class);
    }
}
