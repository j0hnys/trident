<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;

class BusinessLogicFunction
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
        
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        //
        //BusinessLogic function generation
        $business_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = file($business_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $fp = fopen($business_logic_path, 'w'); 
        fwrite($fp, implode('', $lines)); 
        fclose($fp); 


        $stub = $this->storage_disk->readFile(__DIR__.'/../Stubs/Trident/Business/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($business_logic_path, $stub, [
            'append_file' => true
        ]);
    }
    

}