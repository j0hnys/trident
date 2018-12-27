<?php

namespace j0hnys\Trident\Builders\Crud;

class ControllerFunction
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
        //controller function generation
        $controller_path = base_path().'/app/Http/Controllers/Trident/'.ucfirst($td_entity_name).'Controller.php';
        
        $lines = file($controller_path); 
        $last = sizeof($lines) - 1 ; 
        unset($lines[$last]); 

        $fp = fopen($controller_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = file_get_contents(__DIR__.'/../../Stubs/Crud/ControllerFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        file_put_contents($controller_path, $stub, FILE_APPEND);
        

        // $this->call('email:send', [
        //     'user' => 1, '--queue' => 'default'
        // ]);


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