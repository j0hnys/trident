<?php 

namespace App\Trident\Base\Processes\Subscribers;

use App\Trident\Base\Processes\Models\DefaultMarking;
use Symfony\Component\Finder\Finder;

use App\Trident\Base\Processes\CascadeMachine;

class CascadeSubscriber {

    /**
     * Handle workflow guard events.
     */
    public function onGuard($event) {
        
    }

    /**
     * Handle workflow leave event.
     */
    public function onLeave($event) {

    }

    /**
     * Handle workflow transition event.
     */
    public function onTransition($event) {        
        $cascade_machine = CascadeMachine::getInstance();

        $cascade_machine->handleTransition($event);

        $process_step_data = $cascade_machine->getProcessStepData();
    }

    /**
     * Handle workflow enter event.
     */
    public function onEnter($event) {

    }

    /**
     * Handle workflow entered event.
     */
    public function onEntered($event) {
        $originalEvent = $event->getOriginalEvent();

        $subject = $originalEvent->getSubject();
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'J0hnys\TridentWorkflow\Events\GuardEvent',
            'App\Trident\Base\Processes\Subscribers\CascadeSubscriber@onGuard'
        );

        $events->listen(
            'J0hnys\TridentWorkflow\Events\LeaveEvent',
            'App\Trident\Base\Processes\Subscribers\CascadeSubscriber@onLeave'
        );

        $events->listen(
            'J0hnys\TridentWorkflow\Events\TransitionEvent',
            'App\Trident\Base\Processes\Subscribers\CascadeSubscriber@onTransition'
        );

        $events->listen(
            'J0hnys\TridentWorkflow\Events\EnterEvent',
            'App\Trident\Base\Processes\Subscribers\CascadeSubscriber@onEnter'
        );

        $events->listen(
            'J0hnys\TridentWorkflow\Events\EnteredEvent',
            'App\Trident\Base\Processes\Subscribers\CascadeSubscriber@onEntered'
        );
    }

}
