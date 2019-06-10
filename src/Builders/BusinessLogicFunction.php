<?php

namespace j0hnys\Trident\Builders;

class BusinessLogicFunction
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
        //BusinessLogic function generation
        $business_logic_path = base_path().'/app/Trident/Business/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = file($business_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($business_logic_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../Stubs/Trident/Business/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($business_logic_path, $stub, FILE_APPEND);
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