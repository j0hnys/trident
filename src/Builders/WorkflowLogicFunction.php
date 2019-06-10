<?php

namespace j0hnys\Trident\Builders;

class WorkflowLogicFunction
{
    
    /**
     *  constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_name, $function_name)
    {
        
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        //
        //workflowLogic function generation
        $workflow_logic_path = base_path().'/app/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = file($workflow_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($workflow_logic_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../Stubs/Trident/Workflows/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($workflow_logic_path, $stub, FILE_APPEND);
        
    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
    

}