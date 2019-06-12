<?php

namespace j0hnys\Trident\Builders\Crud;

use j0hnys\Trident\Base\Storage\Disk;

class ControllerFunction
{
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
    }
    
    public function generate($td_entity_name, $function_name)
    {
        
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        //
        //controller function generation
        $controller_path = $this->storage_disk->getBasePath().'/app/Http/Controllers/Trident/'.ucfirst($td_entity_name).'Controller.php';
        
        $lines = $this->storage_disk->readFileArray($controller_path); 
        $last = sizeof($lines) - 1 ; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($controller_path, $lines);
        
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/ControllerFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($controller_path, $stub, [
            'append_file' => true
        ]);
    }
    

}