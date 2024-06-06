<?php

namespace EngagingIo\HubSpotManager;

use Illuminate\Support\ServiceProvider;

class HubSpotManagerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(HubSpotManager::class, function ($app) {
            return new HubSpotManager();
        });
    }

    public function boot()
    {
        // Optionally publish config file
        $this->publishes([
            __DIR__ . '/../config/hubspotmanager.php' => config_path('hubspotmanager.php'),
        ]);
    }
}
