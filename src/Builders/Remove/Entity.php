<?php

namespace j0hnys\Trident\Builders\Remove;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

class Entity
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($name = '')
    {
        
        $mustache = new \Mustache_Engine;

        $controller_path = base_path().'/app/Http/Controllers/Trident/'.($name).'Controller.php';
        $model_path = base_path().'/app/Models/'.($name).'.php';
        $policies_path = base_path().'/app/Policies/Trident/'.($name).'Policy.php';
        $business_exception_path = base_path().'/app/Trident/Business/Exceptions/'.($name).'Exception.php';
        $business_logic_path = base_path().'/app/Trident/Business/Logic/'.($name).'.php';
        $interfaces_business_logic_path = base_path().'/app/Trident/Interfaces/Business/Logic/'.($name).'Interface.php';
        $interfaces_workflow_logic_path = base_path().'/app/Trident/Interfaces/Workflows/Logic/'.($name).'Interface.php';
        $interfaces_repositories_path = base_path().'/app/Trident/Interfaces/Workflows/Repositories/'.($name).'RepositoryInterface.php';
        $workflow_exception_path = base_path().'/app/Trident/Workflows/Exceptions/'.($name).'Exception.php';
        $workflow_logic_path = base_path().'/app/Trident/Workflows/Logic/'.($name).'.php';
        $workflow_repository_path = base_path().'/app/Trident/Workflows/Repositories/'.($name).'Repository.php';
        $this->deleteDirectory(base_path().'/app/Trident/Workflows/Schemas/Logic/');
        $workflow_validations_path = glob(base_path().'/app/Trident/Workflows/Validations/'.($name).'*.php');
        $database_factory_logic_path = base_path().'/database/factories/Models/'.($name).'.php';
               
        (is_file($controller_path)) ? unlink($controller_path) : null;
        (is_file($model_path)) ? unlink($model_path) : null;
        (is_file($policies_path)) ? unlink($policies_path) : null;
        (is_file($business_exception_path)) ? unlink($business_exception_path) : null;
        (is_file($business_logic_path)) ? unlink($business_logic_path) : null;
        (is_file($interfaces_business_logic_path)) ? unlink($interfaces_business_logic_path) : null;
        (is_file($interfaces_workflow_logic_path)) ? unlink($interfaces_workflow_logic_path) : null;
        (is_file($interfaces_repositories_path)) ? unlink($interfaces_repositories_path) : null;
        (is_file($workflow_exception_path)) ? unlink($workflow_exception_path) : null;
        (is_file($workflow_logic_path)) ? unlink($workflow_logic_path) : null;
        (is_file($workflow_repository_path)) ? unlink($workflow_repository_path) : null;
        array_map(function($element){
            (is_file($element)) ? unlink($element) : null;
        },$workflow_validations_path);
        (is_file($database_factory_logic_path)) ? unlink($database_factory_logic_path) : null;



        //
        //update resource routes
        $Td_entities_workflows = $this->getCurrentControllers();

        $workflows = array_map(function ($element) {
            return [
                'Td_entity' => ucfirst($element),
                'td_entity' => lcfirst($element),
            ];
        }, $Td_entities_workflows);

        $trident_resource_routes_path = base_path() . '/routes/trident.php';
        $stub = file_get_contents(__DIR__ . '/../../Stubs/routes/trident.stub');
        $stub = $mustache->render($stub, [
            'register_resource_routes' => $workflows,
        ]);

        file_put_contents($trident_resource_routes_path, $stub);

        //
        //update trident auth provider
        $trident_auth_provider_path = base_path() . '/app/Providers/TridentAuthServiceProvider.php';
        $stub = file_get_contents(__DIR__ . '/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflow_policies' => $workflows,
        ]);

        file_put_contents($trident_auth_provider_path, $stub);


        //
        //update TridentServiceProvider
        $Td_entities_workflows = $this->getCurrentWorkflows();
        $Td_entities_businesses = $this->getCurrentBusinesses();

        $workflows = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
            ];
        },$Td_entities_workflows);

        $businesses = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
            ];
        },$Td_entities_businesses);


        $trident_event_service_provider_path = base_path().'/app/Providers/TridentServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
        ]);

        file_put_contents($trident_event_service_provider_path, $stub);


    }


    /**
     * removes directory deleting child folders and files
     *
     * @param [type] $dir
     * @return void
     */
    public function deleteDirectory($dir) {
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), array('.','..'));
            foreach ($files as $file) {
                (is_dir("$dir/$file")) ? $this->deleteDirectory("$dir/$file") : unlink("$dir/$file");
            }
            return rmdir($dir);
        }
    } 



    /**
     * return the names of all events from trigger folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentControllers()
    {
        $files = scandir(base_path() . '/app/Http/Controllers/Trident/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames[] = str_replace('Controller.php', '', $file);
            }
        }

        return $filenames;
    }


    /**
     * return the names of all events from trigger folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentWorkflows()
    {
        $files = scandir(base_path().'/app/Trident/Workflows/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * return the names of all events from subscriber folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentBusinesses()
    {
        $files = scandir(base_path().'/app/Trident/Business/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }



}