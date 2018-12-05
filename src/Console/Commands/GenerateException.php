<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateException extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:exception {td_entity_type} {td_entity_name} {model?} {--only=} {--api} {--parent=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an exception';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $td_entity_type = $this->argument('td_entity_type');
            $td_entity_name = $this->argument('td_entity_name');
            $model = $this->argument('model');
            $only = $this->option('only');
            $api = $this->option('api');
            $withArr = !empty($with) ? explode(",", $with) : [];
            $onlyArr = !empty($only) ? explode(",", $only) : '';
            $parent = $this->option('parent');
           

            $crud = new Builders\Exception($td_entity_type, $td_entity_name);
            // $controllerCrud->save();


            $this->info($td_entity_type.' '.$td_entity_name.' exception successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}