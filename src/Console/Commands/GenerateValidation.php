<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:validation {entity_name} {function_name} {model?} {--only=} {--api} {--parent=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a validation';
    
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
            $model = $this->argument('model');
            $only = $this->option('only');
            $api = $this->option('api');
            $withArr = !empty($with) ? explode(",", $with) : [];
            $onlyArr = !empty($only) ? explode(",", $only) : '';
            $parent = $this->option('parent');
           

            $crud = new Builders\Validation($entity_name, $function_name);
            // $controllerCrud->save();


            $this->info($entity_name.' '.$function_name.' validation successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}