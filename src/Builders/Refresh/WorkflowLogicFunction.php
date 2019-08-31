<?php

namespace j0hnys\Trident\Builders\Refresh;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;

class WorkflowLogicFunction
{
    private $mustache;
    private $storage_disk;
    private $storage_trident;

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
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name, array $options = [], Command $command): void
    {

        //new validation class
        $command->call('trident:generate:validation', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--schema_path' => $options['validation_schema_path'],
            '--force' => true,
        ]);

        //new strict type
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional',
            'function_name' => $function_name,
            'entity_name' => $td_entity_name,
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path'],
            '--force' => true,
        ]);

        // new resource and it's collection
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => false,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path'],
            '--force' => true,
        ]);
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => true,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path'],
            '--force' => true,
        ]);
    }

    
    

}