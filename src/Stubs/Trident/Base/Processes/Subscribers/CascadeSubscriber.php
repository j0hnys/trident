<?php 

namespace App\Trident\Base\Processes\Subscribers;

use App\Trident\Base\Processes\Models\DefaultMarking;

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
        $originalEvent = $event->getOriginalEvent();
        
        $transition = $originalEvent->getTransition();
        $transition_name = $transition->getName();

        $subject = $originalEvent->getSubject();
        $subject_name = get_class($subject);
        if ($subject instanceof DefaultMarking) {
            $subject_name = $subject->td_entity_namespace;
        }

        // dump([
        //     'message' => 'onTransition',
        //     '$originalEvent' => $originalEvent,
        //     '$transition_name' => $transition_name,
        //     '$subject' => $subject,
        //     '$subject_name' => $subject_name,
        // ]);
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

        // dump([
        //     'message' => 'onEntered',
        //     // '$originalEvent' => $originalEvent,
        //     '$subject' => $subject,
        // ]);
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
