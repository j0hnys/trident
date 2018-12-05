<?php

namespace App\Providers;

use Illuminate\Container\Container as App;
use Illuminate\Support\ServiceProvider;

class TridentServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //EDW THA PREPEI NA KANW GENERATED TOYS PROVIDERS MOY!!!
        // \App::bind('App\Trident\Business\Logic\Printer',function(){
        //     return new \App\Trident\Business\Logic\Printer();
        // });
        // \App::bind('App\Trident\Workflows\Logic\Printer',function($app){
        //     return new \App\Trident\Workflows\Logic\Printer($app);
        // });
    }
}
