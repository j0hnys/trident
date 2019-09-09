<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;
use j0hnys\Trident\Base\Constants\Trident\Functionality;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class WorkflowRestfulCrud
{    
    private $mustache;
    private $storage_disk;
    private $storage_trident;
    private $crud_builder;
    
    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->crud_builder = new Builders\Crud\CrudWorkflowBuilder($storage_disk, $storage_trident);
        $this->functionality_definition = new Functionality();
        $this->folder_structure = new FolderStructure();
    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generate(string $name = 'TEST', array $options = [], Command $command): void
    {
        
        $this->generateCrud($name, $options, $command);

        $this->generateWorkflow($name);

        $this->generateOther($name, $options, $command);

    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generateCrud(string $name, array $options = [], Command $command): void
    {
        $model_db_name = '';
        if ($options['functionality_schema_path']) {
            $schema = [];
            if (!empty($options['functionality_schema_path'])) {
                $schema = json_decode( $this->storage_disk->readFile( $options['functionality_schema_path'] ),true);
                $this->functionality_definition->check($schema, 'schema');
            }


            $model_db_name = $schema['model']['db_name'];
        }

        $schema_path = $options['validation_schema_path'];

        $crud = $this->crud_builder->generate($name, $model_db_name, $schema_path, $command);
    }

    /**
     * @param string $name
     * @return void
     */
    public function generateWorkflow(string $name): void
    {
        
        //
        //workflow logic generation
        $this->folder_structure->checkPath('app/Trident/Workflows/Logic/*');
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicCrud.stub';
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
        //workflow repository interface generation
        $this->folder_structure->checkPath('app/Trident/Interfaces/Workflows/Repositories/*');
        $workflow_interface_path = $this->storage_disk->getBasePath().'/app/Trident/Interfaces/Workflows/Repositories/'.ucfirst($name).'RepositoryInterface.php';
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Workflows/LogicRepositoryInterface.stub';
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
        $stub_path = __DIR__.'/../../src/Stubs/Trident/Business/LogicCrud.stub';
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
    
    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $name, array $options = [], Command $command): void
    {
        //new model factory
        $command->call('trident:generate:factory', [
            'model' => 'App\\Models\\'.ucfirst($name),  //<-- PROSOXH!! (prepei na einai array...) //ucfirst($name).'Factory'
        ]);
        
        //new validation class for restful crud store
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'store',
            '--schema_path' => $options['validation_schema_path']
        ]);

        //new validation class for restful crud update
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'update',
            '--schema_path' => $options['validation_schema_path']
        ]);
        
        // Make the basic strict types for crud
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'store',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path']
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'update',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path']
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'index',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path']
        ]);
        
        // Make the basic resource and it's collection
        $command->call('trident:generate:resources', [
            'entity_name' => ucfirst($name),
            '--collection' => false,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
        $command->call('trident:generate:resources', [
            'entity_name' => ucfirst($name),
            '--collection' => true,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
    }

}