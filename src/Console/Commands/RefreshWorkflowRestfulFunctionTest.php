<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh\Tests;

class RefreshWorkflowRestfulFunctionTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:workflow_restful_function_test {entity_name} {function_name} {--functionality_schema_path=} {--request_schema_path=} {--response_schema_path=}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh a restful function test';

    /**
     * @var Tests\WorkflowRestfulFunction
     */
    private $workflow_restful_function;

    public function __construct(Tests\WorkflowRestfulFunction $workflow_restful_function = null)
    {
        parent::__construct();

        $this->workflow_restful_function = new Tests\WorkflowRestfulFunction();
        if (!empty($workflow_restful_function)) {
            $this->workflow_restful_function = $workflow_restful_function;
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
            $options['request_schema_path'] = $this->option('request_schema_path') ? $this->option('request_schema_path') : '';
            $options['response_schema_path'] = $this->option('response_schema_path') ? $this->option('response_schema_path') : '';
            
            $builders = $this->workflow_restful_function->refresh($entity_name, $function_name, $options, $this);

            $this->info($entity_name.' '.$function_name.' workflow restful function successfully created');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}