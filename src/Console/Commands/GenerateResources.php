<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateResources extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:resources {entity_name} {--collection} {--workflow} {--schema_path=} {--force}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create resources for restful entity';

    /**
     * @var Builders\Resources
     */
    private $resources;

    public function __construct(Builders\Resources $resources = null)
    {
        parent::__construct();

        $this->resources = new Builders\Resources();
        if (!empty($resources)) {
            $this->resources = $resources;
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
            $is_collection = $this->option('collection');
            $domain = $this->option('workflow') ? 'Workflows' : 'Business';
            $schema_path = $this->option('schema_path');
            $force = $this->option('force');
            
            $crud = $this->resources->generate($entity_name, $is_collection, $domain, $schema_path, $force);
            
            $collection_message = $is_collection ? ' Collection' : '';
            $this->info($entity_name.' Resource'.$collection_message.' successfully created for '.$domain);
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}