<?php

namespace j0hnys\Trident\Builders\Crud;

class CrudBuilder
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($name = 'TEST')
    {
        
        //
        //controller generation
        $controller_path = base_path().'/app/Http/Controllers/'.ucfirst(strtolower($name)).'Controller.php';
        
        if (file_exists($controller_path)) {
            $this->makeDirectory($controller_path);

            $stub = file_get_contents(__DIR__.'/../../../src/Stubs/Crud/Controller.stub');

            $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
            
            file_put_contents($controller_path, $stub);
        }


        //
        //model generation
        $model_path = base_path().'/app/Models/'.ucfirst(strtolower($name)).'.php';
        
        if (file_exists($model_path)) {
            $this->makeDirectory($model_path);

            $stub = file_get_contents(__DIR__.'/../../../src/Stubs/Crud/Model.stub');

            $stub = str_replace('{{td_entity}}', strtolower($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst(strtolower($name)), $stub);
            
            file_put_contents($model_path, $stub);
        }
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