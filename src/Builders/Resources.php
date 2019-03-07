<?php

namespace j0hnys\Trident\Builders;

class StrictType
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $entity_name, boolean $is_collection, string $domain)
    {
        //Resource logic generation
        $resource_type = $is_collection?'ResourceCollection':'Resource';
        $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($entity_name).'/Resources/'.ucfirst($entity_name).$resource_type.'.php';
        
        if (file_exists($struct_path)) {
            throw new \Exception(ucfirst($function_name) . $resource_type . ' already exists!');
        }

        $this->makeDirectory($struct_path);

        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Resources/'.$resource_type.'.stub');

        $stub = str_replace('{{Entity_name}}', ucfirst($entity_name), $stub);
        
        file_put_contents($struct_path, $stub);
        

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