<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Workflow
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
    public function generate(string $name = 'TEST'): void
    {        
        //
        //workflow logic generation
        $this->folder_structure->checkPath('app/Trident/Workflows/Logic/*');
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/Logic.stub';
        $this->makeFile(
            $name,
            $workflow_logic_path,
            $stub_path
        );


        //
        //workflow exception generation
        $this->folder_structure->checkPath('app/Trident/Workflows/Exceptions/*');
        $workflow_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Exceptions/'.ucfirst($name).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicException.stub';
        $this->makeFile(
            $name,
            $workflow_exception_path,
            $stub_path
        );


        //
        //workflow interface generation
        $this->folder_structure->checkPath('app/Trident/Interfaces/Workflows/Logic/*');
        $workflow_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Logic/'.ucfirst($name).'Interface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicInterface.stub';
        $this->makeFile(
            $name,
            $workflow_interface_path,
            $stub_path
        );

        //
        //workflow repository generation
        $this->folder_structure->checkPath('app/Trident/Workflows/Repositories/*');
        $workflow_repository_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Repositories/'.ucfirst($name).'Repository.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicRepository.stub';
        $this->makeFile(
            $name,
            $workflow_repository_path,
            $stub_path
        );

        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //business logic generation
        $this->folder_structure->checkPath('app/Trident/Business/Logic/*');
        $business_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.ucfirst($name).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/Logic.stub';
        $this->makeFile(
            $name,
            $business_logic_path,
            $stub_path
        );

        //
        //business logic exception generation
        $this->folder_structure->checkPath('app/Trident/Business/Exceptions/*');
        $business_logic_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Exceptions/'.ucfirst($name).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicException.stub';
        $this->makeFile(
            $name,
            $business_logic_exception_path,
            $stub_path
        );

        //
        //business logic interface generation
        $this->folder_structure->checkPath('app/Trident/Interfaces/Business/Logic/*');
        $business_logic_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Business/Logic/'.ucfirst($name).'Interface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicInterface.stub';
        $this->makeFile(
            $name,
            $business_logic_interface_path,
            $stub_path
        );


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
        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/app/Providers/TridentServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
        ]);


        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);

    }
    
     /**
     * make the appropriate file for the class if necessary.
     *
     * @param string $name
     * @param string $fullpath_to_create
     * @param string $stub_fullpath
     * @return void
     */
     protected function makeFile(string $name, string $fullpath_to_create, string $stub_fullpath): void
    {
        
        if ($this->storage_disk->fileExists($fullpath_to_create)) {
            // throw new \Exception($fullpath_to_create . ' already exists!');
            return;
        }

        $this->storage_disk->makeDirectory($fullpath_to_create);

        $stub = $this->storage_disk->readFile($stub_fullpath);

        $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        
        $this->storage_disk->writeFile($fullpath_to_create, $stub);
    }
        

}