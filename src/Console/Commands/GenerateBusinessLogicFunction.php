<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateBusinessLogicFunction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:business_logic_function {entity_name} {function_name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a business logic function';
    
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
            

            $builders = new Builders\BusinessLogicFunction($entity_name, $function_name);
            

            $this->info($entity_name.' '.$function_name.' business logic function successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}