<?php

namespace LaravelAutoCache;

use Illuminate\Support\ServiceProvider;
use LaravelAutoCache\CacheManager;
use LaravelAutoCache\Observer;

class AutocacheServiceProvider extends ServiceProvider
{
    const CONFIG_FILE = __DIR__ . '/../config/autocache.php';

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config file
        $this->publishes([
            self::CONFIG_FILE => config_path('autocache.php'),
        ]);

        // Load observer
        foreach (config('autocache.models') as $model) {
            app($model)->observe(Observer::class);
        }
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Create CacheManager singleton
        $this->app->singleton(CacheManager::class, function () {
            return new CacheManager;
        });

        // Set configuration
        $this->mergeConfigFrom(self::CONFIG_FILE, 'autocache');
    }
}
