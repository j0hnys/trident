<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateWorkflowLogicFunction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow_logic_function {entity_name} {function_name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a workflow logic function';
    
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
            

            $builders = new Builders\WorkflowLogicFunction($entity_name, $function_name);

            $this->info($entity_name.' '.$function_name.' workflow logic function successfully created');

            //
            //sto workflow tha ftiaxnw taytoxrona k ola ta alla functions/domes
            
            //new validation class
            $this->call('trident:generate:validation', [
                'entity_name' => $entity_name,
                'function_name' => $function_name,
            ]);

            //new controller function
            $this->call('trident:generate:controller_function', [
                'entity_name' => $entity_name,
                'function_name' => $function_name,
            ]);

            //new policy function
            $this->call('trident:generate:policy_function', [
                'entity_name' => $entity_name,
                'function_name' => $function_name,
            ]);

            //new business logic function
            $this->call('trident:generate:business_logic_function', [
                'entity_name' => $entity_name,
                'function_name' => $function_name,
            ]);

            
            $controller_class_name = ucfirst($entity_name).'Controller.php';
            $validation_class_name = ucfirst($entity_name).ucfirst($function_name);
            $this->info("\n".'nice! now add "use App\Trident\Workflows\Validations\\'.$validation_class_name.'Request;" on top of your "'.$controller_class_name.'" and you are ready to go.');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}