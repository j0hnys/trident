<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Remove;

class RemoveEntityFunction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:remove:entity_function {entity_name} {function_name}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes trident entity\'s function with the structures connected to it.';
    
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
            

            $crud = new Remove\EntityFunction($entity_name, $function_name);
            

            $this->info($entity_name.': '.$function_name.' removed successfully');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}