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

    private $workflow_logic_function;

    public function __construct()
    {
        parent::__construct();

        $this->workflow_logic_function = new Builders\WorkflowLogicFunction();

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
            $function_name = $this->argument('function_name');
            

            $builders = $this->workflow_logic_function->generate($entity_name, $function_name, $this);

            $this->info($entity_name.' '.$function_name.' workflow logic function successfully created');

            
            $controller_class_name = ucfirst($entity_name).'Controller.php';
            $validation_class_name = ucfirst($entity_name).ucfirst($function_name);
            $this->info("\n".'nice! now add "use App\Trident\Workflows\Validations\\'.$validation_class_name.'Request;" on top of your "'.$controller_class_name.'" and you are ready to go.');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}