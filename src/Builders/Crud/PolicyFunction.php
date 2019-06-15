<?php

namespace j0hnys\Trident\Builders\Crud;

use j0hnys\Trident\Base\Storage\Disk;

class PolicyFunction
{
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
    }
    
    /**
     *  constructor.
     * @param string $name
     * @throws \Exception
     */
    public function generate($td_entity_name, $function_name)
    {
        
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        //
        //policy function generation
        $policy_path = $this->storage_disk->getBasePath().'/app/Policies/Trident/'.ucfirst($td_entity_name).'Policy.php';
        
        $lines = $this->storage_disk->readFileArray($policy_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($policy_path, $lines);

        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/PolicyFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($policy_path, $stub, [
            'append_file' => true
        ]);
    }
    

}