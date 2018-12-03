<?php

namespace j0hnys\Trident;

use Illuminate\Support\ServiceProvider;
use j0hnys\Trident\Console\Commands\GenerateCrud;
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
        $this->app->singleton('trident.generate', function ($app) {
            return new GenerateCrud();
        });
        // . . .

        $this->commands([
            'trident.generate',
            // . . .
        ]);
    }

}