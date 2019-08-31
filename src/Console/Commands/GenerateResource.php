<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateResource extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:resource {entity_name} {function_name} {--collection} {--workflow} {--schema_path=} {--force}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a resource';

    /**
     * @var Builders\Resource
     */
    private $resource;

    public function __construct(Builders\Resource $resource = null)
    {
        parent::__construct();

        $this->resource = new Builders\Resource();
        if (!empty($resource)) {
            $this->resource = $resource;
        }
    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $entity_name = $this->argument('entity_name');
            $function_name = $this->argument('function_name');
            $is_collection = $this->option('collection');
            $domain = $this->option('workflow') ? 'Workflows' : 'Business';
            $schema_path = $this->option('schema_path');
            $force = $this->option('force');
            
            $crud = $this->resource->generate($entity_name, $function_name, $is_collection, $domain, $schema_path, $force);
            
            $collection_message = $is_collection ? ' Collection' : '';
            $this->info($entity_name.' Resource'.$collection_message.' successfully created for '.$domain);
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}