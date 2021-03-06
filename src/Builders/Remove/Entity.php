<?php

namespace j0hnys\Trident\Builders\Remove;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Entity
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->storage_disk = new Disk();    
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }    
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->mustache = new \Mustache_Engine;
        $this->folder_structure = new FolderStructure();
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function run(string $name = ''): void
    {

        $this->folder_structure->checkPath('app/Http/Controllers/Trident/*');
        $this->folder_structure->checkPath('app/Models/*');
        $this->folder_structure->checkPath('app/Policies/Trident/*');
        $this->folder_structure->checkPath('app/Trident/Business/Exceptions/*');
        $this->folder_structure->checkPath('app/Trident/Business/Logic/*');
        $this->folder_structure->checkPath('app/Trident/Interfaces/Business/Logic/*');
        $this->folder_structure->checkPath('app/Trident/Interfaces/Workflows/Logic/*');
        $this->folder_structure->checkPath('app/Trident/Interfaces/Workflows/Repositories/*');
        $this->folder_structure->checkPath('app/Trident/Workflows/Exceptions/*');
        $this->folder_structure->checkPath('app/Trident/Workflows/Logic/*');
        $this->folder_structure->checkPath('app/Trident/Workflows/Repositories/*');
        $this->folder_structure->checkPath('app/Trident/Workflows/Schemas/Logic/*');
        $this->folder_structure->checkPath('app/Trident/Workflows/Validations/*');
        $this->folder_structure->checkPath('database/factories/Models/*');

        $controller_path = $this->storage_disk->getBasePath().'/app/Http/Controllers/Trident/'.($name).'Controller.php';
        $model_path = $this->storage_disk->getBasePath().'/app/Models/'.($name).'.php';
        $policies_path = $this->storage_disk->getBasePath().'/app/Policies/Trident/'.($name).'Policy.php';
        $business_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Exceptions/'.($name).'Exception.php';
        $business_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.($name).'.php';
        $interfaces_business_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Business/Logic/'.($name).'Interface.php';
        $interfaces_workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Logic/'.($name).'Interface.php';
        $interfaces_repositories_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Repositories/'.($name).'RepositoryInterface.php';
        $workflow_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Exceptions/'.($name).'Exception.php';
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.($name).'.php';
        $workflow_repository_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Repositories/'.($name).'Repository.php';
        $this->storage_disk->deleteDirectoryAndFiles($this->storage_disk->getBasePath().'/app/Trident/Workflows/Schemas/Logic/');
        $workflow_validations_path = glob($this->storage_disk->getBasePath().'/app/Trident/Workflows/Validations/'.($name).'*.php');
        $database_factory_logic_path = $this->storage_disk->getBasePath().'/database/factories/Models/'.($name).'.php';
               
        ($this->storage_disk->isFile($controller_path)) ? $this->storage_disk->deleteFile($controller_path) : null;
        ($this->storage_disk->isFile($model_path)) ? $this->storage_disk->deleteFile($model_path) : null;
        ($this->storage_disk->isFile($policies_path)) ? $this->storage_disk->deleteFile($policies_path) : null;
        ($this->storage_disk->isFile($business_exception_path)) ? $this->storage_disk->deleteFile($business_exception_path) : null;
        ($this->storage_disk->isFile($business_logic_path)) ? $this->storage_disk->deleteFile($business_logic_path) : null;
        ($this->storage_disk->isFile($interfaces_business_logic_path)) ? $this->storage_disk->deleteFile($interfaces_business_logic_path) : null;
        ($this->storage_disk->isFile($interfaces_workflow_logic_path)) ? $this->storage_disk->deleteFile($interfaces_workflow_logic_path) : null;
        ($this->storage_disk->isFile($interfaces_repositories_path)) ? $this->storage_disk->deleteFile($interfaces_repositories_path) : null;
        ($this->storage_disk->isFile($workflow_exception_path)) ? $this->storage_disk->deleteFile($workflow_exception_path) : null;
        ($this->storage_disk->isFile($workflow_logic_path)) ? $this->storage_disk->deleteFile($workflow_logic_path) : null;
        ($this->storage_disk->isFile($workflow_repository_path)) ? $this->storage_disk->deleteFile($workflow_repository_path) : null;
        array_map(function($element){
            ($this->storage_disk->isFile($element)) ? $this->storage_disk->deleteFile($element) : null;
        },$workflow_validations_path);
        ($this->storage_disk->isFile($database_factory_logic_path)) ? $this->storage_disk->deleteFile($database_factory_logic_path) : null;



        //
        //update resource routes
        $Td_entities_workflows = $this->storage_trident->getCurrentControllers();

        $workflows = array_map(function ($element) {
            return [
                'Td_entity' => ucfirst($element),
                'td_entity' => lcfirst($element),
            ];
        }, $Td_entities_workflows);

        $this->folder_structure->checkPath('routes/trident.php');
        $trident_resource_routes_path = $this->storage_disk->getBasePath() . '/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_resource_routes_path, $stub);

        //
        //update trident auth provider
        $this->folder_structure->checkPath('app/Providers/TridentAuthServiceProvider.php');
        $trident_auth_provider_path = $this->storage_disk->getBasePath() . '/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);


        //
        //update TridentServiceProvider
        $Td_entities_workflows = $this->storage_trident->getCurrentWorkflows();
        $Td_entities_businesses = $this->storage_trident->getCurrentBusinesses();

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


        $this->folder_structure->checkPath('app/Providers/TridentServiceProvider.php');
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);


    }


}