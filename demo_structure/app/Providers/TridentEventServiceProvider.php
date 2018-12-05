<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class TridentEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        // 'App\Td\Workflows\Events\Triggers\PrinterTrigger' => [
        //     'App\Td\Workflows\Events\Listeners\PrinterListener',
        // ],
    ];

    /**
     * The event subscribers for the application.
     *
     * @var array
     */
    protected $subscribe = [
        // 'App\Td\Workflows\Events\Subscribers\PrinterSubscriber',
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
