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
            
            // Make the basic strict types for crud
            $this->call('trident:generate:strict_type', [
                'strict_type_name' => 'struct_optional',
                'function_name' => 'store'.ucfirst($name),
                'entity_name' => ucfirst($name),
            ]);
            $this->call('trident:generate:strict_type', [
                'strict_type_name' => 'struct_optional',
                'function_name' => 'update'.ucfirst($name),
                'entity_name' => ucfirst($name),
            ]);
            $this->call('trident:generate:strict_type', [
                'strict_type_name' => 'struct_optional',
                'function_name' => 'index'.ucfirst($name),
                'entity_name' => ucfirst($name),
            ]);
            
            // Make the basic resource and it's collection
            $this->call('make:resource', [
                'name' => ucfirst($name).'/'.ucfirst($name).'Resource', 
            ]);
            $this->call('make:resource', [
                'name' => ucfirst($name).'/'.ucfirst($name).'ResourceCollection', 
            ]);

            $this->info($name.' workflow restful crud successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}