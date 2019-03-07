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
    protected $signature = "trident:generate:resource {entity_name} {--collection} {--workflow} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a resource';
    
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
            $domain = $this->option('workflow')?'Workflows':'Business';
            
            $crud = new Builders\Resources($entity_name, $is_collection, $domain);
            // $controllerCrud->save();

            $collection_message = $is_collection?' Collection':'';
            $this->info($entity_name.' Resource'.$collection_message.' successfully created for '.$domain);
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}