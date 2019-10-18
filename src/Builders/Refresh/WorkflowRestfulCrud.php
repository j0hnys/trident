<?php

namespace j0hnys\Trident\Builders\Refresh;

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
    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function refresh(string $name, array $options = [], Command $command): void
    {
        //new model factory
        $command->call('trident:generate:factory', [
            'model' => 'App\\Models\\'.ucfirst($name),  //<-- PROSOXH!! (prepei na einai array...) //ucfirst($name).'Factory'
            '--force' => true,
        ]);
        
        //new validation class for restful crud store
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'store',
            '--schema_path' => $options['validation_schema_path'],
            '--force' => true,
        ]);

        //new validation class for restful crud update
        $command->call('trident:generate:validation', [
            'entity_name' => $name,
            'function_name' => 'update',
            '--schema_path' => $options['validation_schema_path'],
            '--force' => true,
        ]);
        
        // Make the basic strict types for crud
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'store',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path'],
            '--force' => true,
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'update',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path'],
            '--force' => true,
        ]);
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => 'index',
            'entity_name' => ucfirst($name),
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path'],
            '--force' => true,
        ]);
        
        // Make the basic resource and it's collection
        $command->call('trident:generate:resources', [
            'entity_name' => ucfirst($name),
            '--collection' => false,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path'],
            '--force' => true,
        ]);
        $command->call('trident:generate:resources', [
            'entity_name' => ucfirst($name),
            '--collection' => true,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path'],
            '--force' => true,
        ]);
    }

}