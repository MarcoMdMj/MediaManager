<?php

namespace MarcoMdMj\MediaManager;

use Illuminate\Support\ServiceProvider;

class MediaManagerServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     * @var bool
     */
    protected $defer = true;    

    /**
     * Bootstrap the application services.
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/mediamanager.php' => config_path('mediamanager.php'),
        ], 'config');
    }

    /**
     * Register the application services.
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MediaManager::class);

        $this->app->bind(
            \MarcoMdMj\MediaManager\Store\StoreInterface::class,
            \MarcoMdMj\MediaManager\Store\LaravelStore::class
        );
    }

    /**
     * Get the services provided by the provider.
     * @return array
     */
    public function provides()
    {
        return [MediaManager::class];
    }
}
