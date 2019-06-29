<?php

namespace j0hnys\Trident\Builders\Setup;

use j0hnys\Trident\Base\Storage\Disk;

class Install
{
    private $storage_disk;
    private $mustache;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->mustache = new \Mustache_Engine;
    }
    
    /**
     * @return void
     */
    public function run()
    {
        
        $app_path = $this->storage_disk->getBasePath().'/app';

        //
        //folder structure creation
        if (!$this->storage_disk->fileExists($app_path.'/Trident')) {
            
            $source = __DIR__.'/../../../scaffold_structure/Trident';
            $destination = $app_path.'/Trident';
            
            $this->storage_disk->copyFolderStructure($source, $destination);
        }
        
        //
        //write trident service providers
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => [],
            'register_business' => [],
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);

        //route provider
        $trident_route_service_provider_path = base_path().'/app/Providers/TridentRouteServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentRouteServiceProvider.stub');
        $this->storage_disk->writeFile($trident_route_service_provider_path, $stub);

        //event provider
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentEventServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentEventServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_triggers_events' => [],
            'register_workflow_subscribers' => [],
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);

        //auth provider
        $trident_auth_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => [],
        ]);
        
        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);


        //
        //write trident base files
        $trident_base_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Exceptions/DbRepositoryException.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Exceptions/DbRepositoryException.stub');
        $this->storage_disk->makeDirectory($trident_base_exception_path);
        $this->storage_disk->writeFile($trident_base_exception_path, $stub);

        $trident_base_repository_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Interfaces/DbRepositoryInterface.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Interfaces/DbRepositoryInterface.stub');
        $this->storage_disk->makeDirectory($trident_base_repository_interface_path);
        $this->storage_disk->writeFile($trident_base_repository_interface_path, $stub);

        $trident_base_repository_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Repositories/DbRepository.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Repositories/DbRepository.stub');
        $this->storage_disk->makeDirectory($trident_base_repository_path);
        $this->storage_disk->writeFile($trident_base_repository_path, $stub);

        $typed_source_folder = __DIR__.'/../../Stubs/Trident/Base/Typed';
        $typed_destination_folder = $app_path.'/Trident/Base/Typed';
        $this->storage_disk->makeDirectory($typed_destination_folder.'/a');
        $this->storage_disk->copyFoldersAndFiles($typed_source_folder, $typed_destination_folder);


        //
        //write resource routes file
        $trident_base_repository_path = $this->storage_disk->getBasePath().'/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => [],
        ]);
        $this->storage_disk->writeFile($trident_base_repository_path, $stub);



    }

    
    

}
