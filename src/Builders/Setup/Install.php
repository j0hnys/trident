<?php

namespace j0hnys\Trident\Builders\Setup;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Install
{
    private $storage_disk;
    private $mustache;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
        $this->folder_structure = new FolderStructure();
    }
    
    /**
     * @return void
     */
    public function run()
    {
        $this->folder_structure->checkPath('app/*');
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
        $this->folder_structure->checkPath('app/Providers/TridentServiceProvider.php');
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => [],
            'register_business' => [],
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);

        //route provider
        $this->folder_structure->checkPath('app/Providers/TridentRouteServiceProvider.php');
        $trident_route_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentRouteServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentRouteServiceProvider.stub');
        $this->storage_disk->writeFile($trident_route_service_provider_path, $stub);

        //event provider
        $this->folder_structure->checkPath('app/Providers/TridentEventServiceProvider.php');
        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentEventServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentEventServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_triggers_events' => [],
            'register_workflow_subscribers' => [],
        ]);

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);

        //auth provider
        $this->folder_structure->checkPath('app/Providers/TridentAuthServiceProvider.php');
        $trident_auth_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => [],
        ]);
        
        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);


        //
        //write trident base files
        $this->folder_structure->checkPath('app/Trident/Base/Exceptions/DbRepositoryException.php');
        $trident_base_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Exceptions/DbRepositoryException.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Exceptions/DbRepositoryException.stub');
        $this->storage_disk->makeDirectory($trident_base_exception_path);
        $this->storage_disk->writeFile($trident_base_exception_path, $stub);

        $this->folder_structure->checkPath('app/Trident/Base/Interfaces/DbRepositoryInterface.php');
        $trident_base_repository_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Interfaces/DbRepositoryInterface.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Interfaces/DbRepositoryInterface.stub');
        $this->storage_disk->makeDirectory($trident_base_repository_interface_path);
        $this->storage_disk->writeFile($trident_base_repository_interface_path, $stub);

        $this->folder_structure->checkPath('app/Trident/Base/Repositories/DbRepository.php');
        $trident_base_repository_path = $this->storage_disk->getBasePath().'/app/Trident/Base/Repositories/DbRepository.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Trident/Base/Repositories/DbRepository.stub');
        $this->storage_disk->makeDirectory($trident_base_repository_path);
        $this->storage_disk->writeFile($trident_base_repository_path, $stub);

        $typed_source_folder = __DIR__.'/../../Stubs/Trident/Base/Typed';
        $typed_destination_folder = $app_path.'/Trident/Base/Typed';
        $this->storage_disk->makeDirectory($typed_destination_folder.'/.');
        $this->storage_disk->copyFoldersAndFiles($typed_source_folder, $typed_destination_folder);

        $processes_source_folder = __DIR__.'/../../Stubs/Trident/Base/Processes';
        $processes_destination_folder = $app_path.'/Trident/Base/Processes';
        $this->storage_disk->makeDirectory($processes_destination_folder.'/.');
        $this->storage_disk->copyFoldersAndFiles($processes_source_folder, $processes_destination_folder);


        //
        //write resource routes file
        $this->folder_structure->checkPath('routes/trident.php');
        $trident_base_repository_path = $this->storage_disk->getBasePath().'/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => [],
        ]);
        $this->storage_disk->writeFile($trident_base_repository_path, $stub);



    }

    
    

}
