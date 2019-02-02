<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateWorkflowRestfulCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow_restful_crud {name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a workflow with the accompanied restful crud';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');
            
            //crud building
            $crud = new Builders\Crud\CrudWorkflowBuilder($name);
            
            //workflow building
            $crud = new Builders\WorkflowRestfulCrud($name);

            //new model factory
            $this->call('trident:generate:factory', [
                'model' => 'App\\Models\\'.ucfirst($name),  //<-- PROSOXH!! (prepei na einai array...) //ucfirst($name).'Factory'
            ]);
            
            //new validation class for restful crud store
            $this->call('trident:generate:validation', [
                'entity_name' => $name,
                'function_name' => 'store',
            ]);

            //new validation class for restful crud update
            $this->call('trident:generate:validation', [
                'entity_name' => $name,
                'function_name' => 'update',
            ]);

            $this->info($name.' workflow restful crud successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}