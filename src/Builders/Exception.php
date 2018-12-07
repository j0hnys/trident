<?php

namespace j0hnys\Trident\Builders;

class Exception
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_type, $td_entity_name)
    {
        
        $td_entity_name = ucfirst(strtolower($td_entity_name));
        $td_entity_type = strtolower($td_entity_type);

        $type = '';
        if ($td_entity_type == 'workflow') {
            $type = 'Workflows';
        } else if ($td_entity_type == 'business') {
            $type = 'Business';
        } else {
            throw new \Exception('entity type '.$type.' does not exists!');
        }

        //
        //workflow logic generation
        $workflow_exception_path = base_path().'/app/Trident/'.$type.'/Exceptions/'.$td_entity_name.'Exception.php';
        
        if (file_exists($workflow_exception_path)) {
            throw new \Exception(ucfirst(strtolower($td_entity_name)) . ' exception already exists!');
        }

        $this->makeDirectory($workflow_exception_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$type.'/LogicException.stub');

        $stub = str_replace('{{td_entity}}', strtolower($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        
        file_put_contents($workflow_exception_path, $stub);
        

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