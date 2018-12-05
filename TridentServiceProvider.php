<?php

namespace j0hnys\Trident;

use Illuminate\Support\ServiceProvider;
use j0hnys\Trident\Console\Commands\GenerateCrud;
use j0hnys\Trident\Console\Commands\GenerateWorkflow;
use j0hnys\Trident\Console\Commands\Install;
// . . .

class TridentServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
        //vvv pio meta ayto...
        //
        // $configPath = __DIR__.'/../config/laravel_generator.php';
        // $this->publishes([
        //     $configPath => config_path('infyom/laravel_generator.php'),
        // ]);
        //
        //^^^
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('trident.generate_crud', function ($app) {
            return new GenerateCrud();
        });
        $this->app->singleton('trident.generate_workflow', function ($app) {
            return new GenerateWorkflow();
        });
        $this->app->singleton('trident.install', function ($app) {
            return new Install();
        });
        // . . .

        $this->commands([
            'trident.generate_crud',
            'trident.generate_workflow',
            'trident.install',
            // . . .
        ]);
    }

}