<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateWorkflowFunctionProcess extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow_function_process {entity_name} {type} {function_name} {schema_path} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a workflow function process from existing workflow function';

    /**
     * @var Builders\WorkflowFunctionProcess
     */
    private $workflow_function_process;

    public function __construct(Builders\WorkflowFunctionProcess $workflow_function_process = null)
    {
        parent::__construct();

        $this->workflow_function_process = new Builders\WorkflowFunctionProcess();
        if (!empty($workflow_function_process)) {
            $this->workflow_function_process = $workflow_function_process;
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
            $type = $this->argument('type');
            $function_name = $this->argument('function_name');
            $schema_path = $this->argument('schema_path');


            $this->workflow_function_process->generate($entity_name, $type, $function_name, $schema_path, $this);


            $this->info($entity_name.' '.$function_name.' workflow function successfully updated');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}