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
    protected $signature = "trident:generate:workflow_restful_crud {name} {model?} {--only=} {--api} {--parent=} ";

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
            $model = $this->argument('model');
            $name = $this->argument('name');
            $only = $this->option('only');
            $api = $this->option('api');
            $withArr = !empty($with) ? explode(",", $with) : [];
            $onlyArr = !empty($only) ? explode(",", $only) : '';
            $parent = $this->option('parent');
           
            //crud building
            $crud = new Builders\Crud\CrudWorkflowBuilder($name);
            
            //workflow building
            $crud = new Builders\WorkflowRestfulCrud($name);


            $this->info($name.' workflow restful crud successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}