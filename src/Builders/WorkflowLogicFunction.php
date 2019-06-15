<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;

class WorkflowLogicFunction
{
    private $mustache;
    private $storage_disk;
    private $storage_trident;
    private $crud_builder;

    public function __construct()
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        $this->storage_trident = new Trident();
        $this->crud_builder = new Builders\Crud\CrudWorkflowBuilder();
    }
    
    /**
     *  constructor.
     * @param string $name
     * @throws \Exception
     */
    public function generate($td_entity_name, $function_name, Command $command)
    {

        $this->generateLogicFunction($td_entity_name, $function_name);

        $this->generateOther($td_entity_name, $function_name, $command);

    }

    public function generateLogicFunction($td_entity_name, $function_name)
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);
        

        //
        //workflowLogic function generation
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = $this->storage_disk->readFileArray($workflow_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($workflow_logic_path, $lines); 

        $stub = $this->storage_disk->readFile(__DIR__.'/../Stubs/Trident/Workflows/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($workflow_logic_path, $stub, [
            'append_file' => true
        ]);
        
    }


    public function generateOther($td_entity_name, $function_name, Command $command)
    {
        //
        //sto workflow tha ftiaxnw taytoxrona k ola ta alla functions/domes
        
        //new validation class
        $command->call('trident:generate:validation', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        //new controller function
        $command->call('trident:generate:controller_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        //new policy function
        $command->call('trident:generate:policy_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        //new business logic function
        $command->call('trident:generate:business_logic_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);
    }

    
    

}