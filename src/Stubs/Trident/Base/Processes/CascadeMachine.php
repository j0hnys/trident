<?php

namespace App\Trident\Base\Processes;

use App\Trident\Base\Processes\Models\DefaultMarking;
use J0hnys\TridentWorkflow\WorkflowRegistry;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Workflow\Definition;
 
class CascadeMachine {

    private static $instance;
    private $process_start_step_data = [];
    private $process_step_data = [];
    private $default_marking;
    private $workflow;

    public function boot()  //<-- must be executed once in the beginning of the request
    {
        $configPath = __DIR__.'/../../Workflows/Schemas/Processes';

        foreach (Finder::create()->in($configPath)->name('*.php') as $file) {
            $td_entity_name = (pathinfo( $file->getRelativePath() ))['basename'];

            // $this->mergeConfigFrom( $file->getRealPath() , 'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php') );
            
            $config = app()['config']->get(
                'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php'), 
                []
            );

            app()['config']->set( 
                'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php'), 
                array_merge( require $file->getRealPath(), $config ) 
            );
        }
    }

    public function initializeWorkflow(string $td_entity_namespace, string $workflow_logic_function_name, $start_data) 
    {
        $workflow_configuration = app()->make('J0hnys\TridentWorkflow\PackageProviders\Configuration');

        $td_entity_name = (pathinfo($td_entity_namespace))['basename'];

        $configPath = base_path().'/app/Trident/Workflows/Schemas/Processes/'.$td_entity_name;


        $configutations = [];
        foreach (Finder::create()->in($configPath)->name('*.php') as $file) {
            $configutation_name = 'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php');

            $configutations[ basename($file->getRealPath(),'.php') ] = config($configutation_name);
        }

        $configutation = $configutations['workflow'];
        $transition_configuration = $configutations['cascade_process'];

        $workflow_configuration->setWorkflow($workflow_logic_function_name, $configutation[$workflow_logic_function_name]);

        $workflow_registry = new WorkflowRegistry($workflow_logic_function_name);

        $this->default_marking = new DefaultMarking();
        $this->default_marking->td_entity_name = $td_entity_namespace;
        $this->default_marking->td_entity_workflow_function_name = $workflow_logic_function_name;
        $this->default_marking->marking = 'draft';

        $cascade_machine = CascadeMachine::getInstance();
        $cascade_machine->setProcessStartStepData($start_data);

        $this->workflow = $workflow_registry->get($this->default_marking);
    }

    public function runWorkflow(): void
    {

        $edges = $this->findEdges($this->workflow->getDefinition());

        foreach ($edges as $edge) {
            if ($edge['direction'] == 'from') {
                $this->workflow->apply($this->default_marking, $edge['to']);     
            }
        }
    }

    public static function getInstance(): CascadeMachine
    {
        if (static::$instance === null) {
            static::$instance = new CascadeMachine();
        }
        return static::$instance;
    }

    public function setProcessStartStepData($data): void
    {
        $this->process_start_step_data = $data;
    }

    public function getProcessStartStepData()
    {
        return $this->process_start_step_data;
    }


    public function setProcessStepData(string $callback_uri, $data): void
    {
        $this->process_step_data[$callback_uri] = $data;
    }

    public function getProcessStepData()
    {
        return $this->process_step_data;
    }

    protected function findEdges(Definition $definition)
    {
        $workflowMetadata = $definition->getMetadataStore();

        $dotEdges = [];

        foreach ($definition->getTransitions() as $i => $transition) {
            $transitionName = $workflowMetadata->getMetadata('label', $transition) ?? $transition->getName();

            foreach ($transition->getFroms() as $from) {
                $dotEdges[] = [
                    'from' => $from,
                    'to' => $transitionName,
                    'direction' => 'from',
                    'transition_number' => $i,
                ];
            }
            foreach ($transition->getTos() as $to) {
                $dotEdges[] = [
                    'from' => $transitionName,
                    'to' => $to,
                    'direction' => 'to',
                    'transition_number' => $i,
                ];
            }
        }

        return $dotEdges;
    }

    public function getPreviousStepDataTransition($original_event)
    {
        $transition = $original_event->getTransition();

        $edges = $this->findEdges($original_event->getWorkflow()->getDefinition());

        $step_data_transition = null;

        foreach ($edges as $i => $edge) {
            if ($transition->getFroms()[0] == $edge['from'] && $edge['transition_number'] == 0) {   //<-- tote eimai sthn arxh toy workflow
                break;
            }
            if ($transition->getFroms()[0] == $edge['from'] && $edge['transition_number'] != 0) {
                for ($j=($i-1); $j>=0; $j--) { 
                    if ($edges[$j]['to'] == $transition->getFroms()[0]) {
                        $step_data_transition = $edges[$j]['from'];
                    }
                }

                break;
            }
        }

        return $step_data_transition;
    }

    public function handleTransition($event)
    {
        
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

        $previous_step_data_transition = $this->getPreviousStepDataTransition($originalEvent);
        $step_data = null;

        if ($previous_step_data_transition === null) {
            $step_data = $this->getProcessStartStepData();
        } else {
            $previous_callback_uri = $transition_configuration[ $workflow_function_name ]['transitions'][ $previous_step_data_transition ];

            $steps_datas = $this->getProcessStepData();

            $step_data = $steps_datas[$previous_callback_uri];
        }


        $callback_class = app()->make($callback_class_namespace);
        $result = $callback_class->{$callback_fuction}($step_data);


        $this->setProcessStepData($callback_uri, $result);

    }




}
