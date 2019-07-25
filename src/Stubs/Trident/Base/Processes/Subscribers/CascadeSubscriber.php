<?php 

namespace App\Trident\Base\Processes\Subscribers;

use App\Trident\Base\Processes\Models\DefaultMarking;
use Symfony\Component\Finder\Finder;

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
        $workflow_function_name = '';
        if ($subject instanceof DefaultMarking) {
            $subject_name = $subject->td_entity_name;
            $workflow_function_name = $subject->td_entity_workflow_function_name;
        }

        if (empty($workflow_function_name)) {
            throw new Exception('no $workflow_function_name given', 1);
        }

        $td_entity_name = (pathinfo($subject_name))['basename'];

        //
        //
        $configPath = base_path().'/app/Trident/Workflows/Schemas/Processes/'.$td_entity_name;

        $configutations = [];
        foreach (Finder::create()->in($configPath)->name('*.php') as $file) {
            $configutation_name = 'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php');

            $configutations[ basename($file->getRealPath(),'.php') ] = config($configutation_name);
        }

        $transition_configuration = $configutations['cascade_process'];
        //
        //

        $callback_uri = $transition_configuration[ $workflow_function_name ]['transitions'][ $transition_name ];

        $callback = explode('@',$callback_uri);
        $callback_class_namespace = $callback[0];
        $callback_fuction = $callback[1];

        $callback_class = app()->make($callback_class_namespace);
        $result = $callback_class->{$callback_fuction}(['osdmckldsmcsl']);

        dump([
            'message' => 'onTransition',
            '$originalEvent' => $originalEvent,
            '$transition_name' => $transition_name,
            '$subject' => $subject,
            '$subject_name' => $subject_name,
            '$transition_configuration' => $transition_configuration,
            '$callback' => $callback,
            '$result' => $result,
        ]);
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
