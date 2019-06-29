<?php

namespace j0hnys\Trident\Builders\Crud;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;

class CrudBuilder
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->storage_trident = new Trident();
        $this->mustache = new \Mustache_Engine;
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function generate(string $name = 'TEST'): void
    {
        

        //
        //controller generation
        $controller_path = $this->storage_disk->getBasePath().'/app/Http/Controllers/Trident/'.ucfirst(strtolower($name)).'Controller.php';
        
        if (!$this->storage_disk->fileExists($controller_path)) {
            $this->storage_disk->makeDirectory($controller_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/Controller.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($controller_path, $stub);
        }

        //
        // repository generation is in workflow generation
        //

        //
        //model generation
        $model_path = $this->storage_disk->getBasePath().'/app/Models/'.ucfirst($name).'.php';
        
        if (!$this->storage_disk->fileExists($model_path)) {
            $this->storage_disk->makeDirectory($model_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/Model.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($model_path, $stub);
        }

        //
        //update resource routes
        $Td_entities_workflows = $this->storage_trident->getCurrentControllers();
        
        $workflows = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
                'td_entity' => lcfirst($element),
            ];
        },$Td_entities_workflows);

        $trident_resource_routes_path = $this->storage_disk->getBasePath().'/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_resource_routes_path, $stub);

        //
        //update trident auth provider
        $trident_auth_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => $workflows,
        ]);
        
        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);

        //
        //policy generation
        $trident_policy_path = $this->storage_disk->getBasePath().'/app/Policies/Trident/'.ucfirst($name).'Policy.php';
        if (!file_exists($trident_policy_path)) {
            $this->storage_disk->makeDirectory($trident_policy_path);
            
            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Policies/Trident/LogicPolicy.stub');
            
            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($trident_policy_path, $stub);
        }
            
    }

    
}
