<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Crud;

class GenerateControllerFunction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:controller_function {entity_name} {function_name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a controller function';
    
    /**
     * @var Crud\ControllerFunction
     */
    private $controller_function;

    public function __construct(Crud\ControllerFunction $controller_function = null)
    {
        parent::__construct();

        $this->controller_function = new Crud\ControllerFunction();
        if (!empty($controller_function)) {
            $this->controller_function = $controller_function;
        }
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
            

            $crud = $this->controller_function->generate($entity_name, $function_name);
            

            $this->info($entity_name.' '.$function_name.' controller function successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}