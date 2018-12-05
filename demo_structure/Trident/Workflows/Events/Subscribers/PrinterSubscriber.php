<?php 

namespace App\Trident\Workflows\Events\Subscribers;

class PrinterSubscriber { 

    /**
     * Handle user login events. 
     */ 
    public function onUserLogin($event) {}

    /**
     * Handle user logout events.
     */
    public function onUserLogout($event) {}

    public function subscribe($events)
    {
        $events->listen(
            'App\Events\UserLoggedIn',
            'App\Listeners\UserEventListener@onUserLogin'
        );

        $events->listen(
            'App\Events\UserLoggedOut',
            'App\Listeners\UserEventListener@onUserLogout'
        );
    }
}