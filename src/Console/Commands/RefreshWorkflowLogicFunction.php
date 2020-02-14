<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh;

class RefreshWorkflowLogicFunction extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:workflow_logic_function {entity_name} {function_name} {--functionality_schema_path=} {--resource_schema_path=} {--validation_schema_path=} {--strict_type_schema_path=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh a workflow logic function';

    /**
     * @var Refresh\WorkflowLogicFunction
     */
    private $workflow_logic_function;

    public function __construct(Refresh\WorkflowLogicFunction $workflow_logic_function = null)
    {
        parent::__construct();

        $this->workflow_logic_function = new Refresh\WorkflowLogicFunction();
        if (!empty($workflow_logic_function)) {
            $this->workflow_logic_function = $workflow_logic_function;
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
            $options['functionality_schema_path'] = $this->option('functionality_schema_path') ? $this->option('functionality_schema_path') : '';
            $options['resource_schema_path'] = $this->option('resource_schema_path') ? $this->option('resource_schema_path') : '';
            $options['validation_schema_path'] = $this->option('validation_schema_path') ? $this->option('validation_schema_path') : '';
            $options['strict_type_schema_path'] = $this->option('strict_type_schema_path') ? $this->option('strict_type_schema_path') : '';            

            $builders = $this->workflow_logic_function->generate($entity_name, $function_name, $options, $this);            

            $this->info($entity_name.' '.$function_name.' workflow logic function successfully refreshed');            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}