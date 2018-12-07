<?php

namespace j0hnys\Trident\Builders;

class Validation
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_name, $function_name)
    {
        
        $name = ucfirst(strtolower($td_entity_name)).ucfirst(strtolower($function_name));

        //
        //workflow logic generation
        $workflow_validation_path = base_path().'/app/Trident/Workflows/Validations/'.$name.'Request.php';
        
        if (file_exists($workflow_validation_path)) {
            throw new \Exception(ucfirst(strtolower($name)) . ' validation already exists!');
        }

        $this->makeDirectory($workflow_validation_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Workflows/LogicRequestValidation.stub');

        $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        
        file_put_contents($workflow_validation_path, $stub);
        

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
    
    /**
     * Get code and save to disk
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        //
    }

}