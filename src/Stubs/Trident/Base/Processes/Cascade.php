<?php

namespace App\Trident\Base\Processes;

use Illuminate\Container\Container as App;
use J0hnys\TridentWorkflow\PackageProviders\Configuration;
use J0hnys\TridentWorkflow\WorkflowRegistry;
use App\Trident\Base\Processes\Models\DefaultMarking;
use Symfony\Component\Finder\Finder;
 
class Cascade {

    private $default_marking;
    private $workflow;

    public function initialize(string $td_entity_namespace, string $workflow_logic_function_name, $start_data) 
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

    public function run(): void
    {
        $this->workflow->can($this->default_marking, 'publish'); 

        $this->workflow->apply($this->default_marking, 'to_review');

        $this->workflow->apply($this->default_marking, 'publish');

        $this->workflow->apply($this->default_marking, 'reject_published');
    }




}
