<?php

namespace j0hnys\Trident\Builders\Crud;

class PolicyFunction
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
        //policy function generation
        $policy_path = base_path().'/app/Policies/Trident/'.ucfirst($td_entity_name).'Policy.php';
        
        $lines = file($policy_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($policy_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../../Stubs/Crud/PolicyFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($policy_path, $stub, FILE_APPEND);
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