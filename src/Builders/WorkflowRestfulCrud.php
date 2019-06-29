<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;

class WorkflowRestfulCrud
{    
    private $mustache;
    private $storage_disk;
    private $storage_trident;
    private $crud_builder;
    
    public function __construct()
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        $this->storage_trident = new Trident();
        $this->crud_builder = new Builders\Crud\CrudWorkflowBuilder();
    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generate(string $name = 'TEST', Command $command): void
    {
        $this->generateCrud($name, $command);

        $this->generateWorkflow($name);

        $this->generateOther($name, $command);

    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generateCrud(string $name, Command $command): void
    {
        $crud = $this->crud_builder->generate($name, $command);
    }

    /**
     * @param string $name
     * @return void
     */
    public function generateWorkflow(string $name): void
    {
        
        //
        //workflow logic generation
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicCrud.stub';
        $this->makeFile(
            $name,
            $workflow_logic_path,
            $stub_path
        );


        //
        //workflow exception generation
        $workflow_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Exceptions/'.ucfirst($name).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicException.stub';
        $this->makeFile(
            $name,
            $workflow_exception_path,
            $stub_path
        );


        //
        //workflow interface generation
        $workflow_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Logic/'.ucfirst($name).'Interface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicInterface.stub';
        $this->makeFile(
            $name,
            $workflow_interface_path,
            $stub_path
        );

        //
        //workflow repository interface generation
        $workflow_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Repositories/'.ucfirst($name).'RepositoryInterface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicRepositoryInterface.stub';
        $this->makeFile(
            $name,
            $workflow_interface_path,
            $stub_path
        );

        //
        //workflow repository generation
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
        $business_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.ucfirst($name).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicCrud.stub';
        $this->makeFile(
            $name,
            $business_logic_path,
            $stub_path
        );

        //
        //business logic exception generation
        $business_logic_exception_path = $this->storage_disk->getBasePath().'/app/Trident/Business/Exceptions/'.ucfirst($name).'Exception.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicException.stub';
        $this->makeFile(
            $name,
            $business_logic_exception_path,
            $stub_path
        );

        //
        //business logic interface generation
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
    
    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $name, Command $command): void
    {
        //new model factory
        $command->call('trident:generate:factory', [
            'model' => 'App\\Models\\'.ucfirst($name),  //<-- PROSOXH!! (prepei na einai array...) //ucfirst($name).'Factory'
        ]);
        
        //new validation class for restful crud store
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'store',
        ]);

        //new validation class for restful crud update
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'update',
        ]);
        
        // Make the basic strict types for crud
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'store',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'update',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'index',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
        ]);
        
        // Make the basic resource and it's collection
        $command->call('trident:generate:resource', [
            'entity_name' => ucfirst($name),
            '--collection' => false,
            '--workflow' => true,
        ]);
        $command->call('trident:generate:resource', [
            'entity_name' => ucfirst($name),
            '--collection' => true,
            '--workflow' => true,
        ]);
    }

}