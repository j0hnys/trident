<?php

namespace j0hnys\Trident\Builders\Tests;

use j0hnys\Trident\Base\Storage\Disk;

class WorkflowLogicFunction
{
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name): void
    {
        if (empty($td_entity_name)) {
            throw new \Exception("entity name cannot be empty!!", 1);
        }
        if (empty($function_name)) {
            throw new \Exception("function name cannot be empty!!", 1);
        }


        //
        //workflow logic test function generation
        $workflow_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'Test.php';
        
        $lines = $this->storage_disk->readFileArray($workflow_logic_test_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($workflow_logic_test_path, $lines); 

        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Workflows/Logic/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($workflow_logic_test_path, $stub, [
            'append_file' => true
        ]);
        
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //business logic test function generation
        $business_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Business/Logic/'.ucfirst($td_entity_name).'Test.php';
        
        $lines = $this->storage_disk->readFileArray($business_logic_test_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($business_logic_test_path, $lines); 

        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Business/Logic/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($business_logic_test_path, $stub, [
            'append_file' => true
        ]);

    }
    
    

}