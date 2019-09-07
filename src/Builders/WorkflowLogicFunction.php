<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class WorkflowLogicFunction
{
    private $mustache;
    private $storage_disk;
    private $storage_trident;
    private $crud_builder;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->crud_builder = new Builders\Crud\CrudWorkflowBuilder();
        $this->folder_structure = new FolderStructure();
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name, array $options = [], Command $command): void
    {

        $this->generateLogicFunction($td_entity_name, $function_name);

        $this->generateOther($td_entity_name, $function_name, $options, $command);

    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generateLogicFunction(string $td_entity_name, string $function_name): void
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);
        

        //
        //workflowLogic function generation
        $this->folder_structure->checkPath('tests/Trident/Workflows/Logic/*');
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = $this->storage_disk->readFileArray($workflow_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($workflow_logic_path, $lines); 

        $stub = $this->storage_disk->readFile(__DIR__.'/../Stubs/Trident/Workflows/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ($function_name), $stub);
        $stub = str_replace('{{Function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($workflow_logic_path, $stub, [
            'append_file' => true
        ]);
        
    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $td_entity_name, string $function_name, array $options = [], Command $command): void
    {
        //
        //sto workflow tha ftiaxnw taytoxrona k ola ta alla functions/domes
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

        
        //new validation class
        $command->call('trident:generate:validation', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--schema_path' => $options['validation_schema_path']
        ]);

        //new strict type
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => $function_name,
            'entity_name' => $td_entity_name,
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path']
        ]);

        // new resource and it's collection
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => false,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => true,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
    }

    
    

}