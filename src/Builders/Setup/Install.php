<?php

namespace j0hnys\Trident\Builders\Setup;

class Install
{
    
    /**
     * Install constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        
        $app_path = base_path().'/app';
        $mustache = new \Mustache_Engine;

        //
        //folder structure creation
        if (!file_exists($app_path.'/Trident')) {
            
            $source = __DIR__.'/../../../scaffold_structure/Trident';
            $destination = $app_path.'/Trident';
            
            $this->copyFolderStructure($source, $destination);
        }
        
        //
        //write trident service providers
        $trident_event_service_provider_path = base_path().'/app/Providers/TridentServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflows' => [],
            'register_business' => [],
        ]);

        file_put_contents($trident_event_service_provider_path, $stub);

        //route provider
        $trident_route_service_provider_path = base_path().'/app/Providers/TridentRouteServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/app/Providers/TridentRouteServiceProvider.stub');
        file_put_contents($trident_route_service_provider_path, $stub);

        //event provider
        $trident_event_service_provider_path = base_path().'/app/Providers/TridentEventServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/app/Providers/TridentEventServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflow_triggers_events' => [],
            'register_workflow_subscribers' => [],
        ]);

        file_put_contents($trident_event_service_provider_path, $stub);

        //auth provider
        $trident_auth_provider_path = base_path().'/app/Providers/TridentAuthServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $mustache->render($stub, [
            'register_workflow_policies' => [],
        ]);
        
        file_put_contents($trident_auth_provider_path, $stub);


        //
        //write trident base files
        $trident_base_exception_path = base_path().'/app/Trident/Base/Exceptions/DbRepositoryException.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/Trident/Base/Exceptions/DbRepositoryException.stub');
        file_put_contents($trident_base_exception_path, $stub);

        $trident_base_repository_interface_path = base_path().'/app/Trident/Base/Interfaces/DbRepositoryInterface.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/Trident/Base/Interfaces/DbRepositoryInterface.stub');
        file_put_contents($trident_base_repository_interface_path, $stub);

        $trident_base_repository_path = base_path().'/app/Trident/Base/Repositories/DbRepository.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/Trident/Base/Repositories/DbRepository.stub');
        file_put_contents($trident_base_repository_path, $stub);

        $typed_source_folder = __DIR__.'/../../Stubs/Trident/Base/Typed';
        $typed_destination_folder = $app_path.'/Trident/Base/Typed';
        
        $this->copyFolderStructure($typed_source_folder, $typed_destination_folder);


        //
        //write resource routes file
        $trident_base_repository_path = base_path().'/routes/trident.php';
        $stub = file_get_contents(__DIR__.'/../../Stubs/routes/trident.stub');
        $stub = $mustache->render($stub, [
            'register_resource_routes' => [],
        ]);
        file_put_contents($trident_base_repository_path, $stub);



    }
    
     /**
     * Build directory structure from copying another.
     *
     * @param  string $path
     * @return string
     */
    protected function copyFolderStructure(string $source, string $destination)
    {

        mkdir($destination, 0755);
        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                // copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

    }

     /**
     * copy files.
     *
     * @param  string $path
     * @return string
     */
    protected function copyFiles(string $source, string $destination)
    {

        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                //i don't want to copy any folder
                // mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

    }


     /**
     * copy files.
     *
     * @param  string $path
     * @return string
     */
    protected function copyFoldersAndFiles(string $source, string $destination)
    {

        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                //i don't want to copy any folder
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

    }
    

}
