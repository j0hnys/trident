<?php

namespace App\Trident\Base\Processes;

use Illuminate\Container\Container as App;
use J0hnys\TridentWorkflow\PackageProviders\Configuration;
use J0hnys\TridentWorkflow\WorkflowRegistry;
use App\Trident\Base\Processes\Models\DefaultMarking;
use Symfony\Component\Finder\Finder;
 
class Cascade {

    public function __construct(string $td_entity_namespace, string $workflow_logic_function_name) 
    {

        $workflow_configuration = app()->make('J0hnys\TridentWorkflow\PackageProviders\Configuration');

        $td_entity_name = (pathinfo($td_entity_namespace))['basename'];

        $configPath = base_path().'/app/Trident/Workflows/Schemas/Processes/'.$td_entity_name;



        // dd([
        //     '$td_entity_namespace' => $td_entity_namespace,
        //     '$td_entity_name' => $td_entity_name,
        //     '$workflow_logic_function_name' => $workflow_logic_function_name,
        //     'pathinfo($td_entity_namespace)' => pathinfo($td_entity_namespace),
        // ]);

        $configutations = [];
        foreach (Finder::create()->in($configPath)->name('*.php') as $file) {
            $configutation_name = 'trident.workflows.schemas.processes.'.$td_entity_name.'.'.basename($file->getRealPath(), '.php');

            $configutations[ basename($file->getRealPath(),'.php') ] = config($configutation_name);
        }

        $configutation = $configutations['workflow'];
        $transition_configuration = $configutations['cascade_process'];

        $workflow_configuration->setWorkflow($workflow_logic_function_name, $configutation[$workflow_logic_function_name]);

        $workflow_registry = new WorkflowRegistry($workflow_logic_function_name);

        $default_marking = new DefaultMarking();
        $default_marking->td_entity_name = $td_entity_namespace;
        $default_marking->td_entity_workflow_function_name = $workflow_logic_function_name;
        $default_marking->marking = 'draft';

        $cascade_machine = CascadeMachine::getInstance();
        $cascade_machine->setProcessStartStepData(['aspdoiaspodiaspodaidpoadsi']);

        $workflow = $workflow_registry->get($default_marking);

        $workflow->can($default_marking, 'publish'); 

        $workflow->apply($default_marking, 'to_review');

        $workflow->apply($default_marking, 'publish');


        // dump([
        //     // '$configutation' => $configutation,
        //     // '$workflow_configuration' => $workflow_configuration,
        //     // '$workflow_registry' => $workflow_registry,
        //     '$workflow_logic_function_name' => $workflow_logic_function_name,
        //     '$default_marking' => $default_marking,
        //     '$workflow' => $workflow,
        //     '$workflow->can($default_marking, publish)' => $workflow->can($default_marking, 'publish'),
        //     '$workflow->can($default_marking, to_review)' => $workflow->can($default_marking, 'to_review'),
        // ]);


    }




}
