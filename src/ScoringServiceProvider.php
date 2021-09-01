<?php

namespace Arttiger\Scoring;

use Illuminate\Support\ServiceProvider;

class ScoringServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/scoring.php' => config_path('scoring.php'),
        ], 'config');

        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/scoring.php', 'scoring');

        // Register the service the package provides.
        $this->app->singleton('scoring', function ($app) {
            return new Scoring;
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['scoring'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__.'/../config/scoring.php' => config_path('scoring.php'),
        ], 'config');
    }
}
