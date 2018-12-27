<?php

namespace j0hnys\Trident\Builders\Tests;

class WorkflowLogicFunction
{
    
    /**
     *  constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $td_entity_name, string $function_name)
    {
        if (empty($td_entity_name)) {
            throw new \Exception("entity name cannot be empty!!", 1);
        }
        if (empty($function_name)) {
            throw new \Exception("function name cannot be empty!!", 1);
        }


        //
        //workflow logic test function generation
        $workflow_logic_test_path = base_path().'/tests/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'Test.php';
        
        $lines = file($workflow_logic_test_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($workflow_logic_test_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../../Stubs/tests/Trident/Workflows/Logic/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($workflow_logic_test_path, $stub, FILE_APPEND);
        
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //business logic test function generation
        $business_logic_test_path = base_path().'/tests/Trident/Business/Logic/'.ucfirst($td_entity_name).'Test.php';
        
        $lines = file($business_logic_test_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($business_logic_test_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../../Stubs/tests/Trident/Business/Logic/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($business_logic_test_path, $stub, FILE_APPEND);


    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory(string $path): void
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
    

}