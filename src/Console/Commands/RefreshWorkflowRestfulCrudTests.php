<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh\Tests;

class RefreshWorkflowRestfulCrudTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:workflow_restful_crud_tests {name} {--functionality_schema_path=} {--request_schema_path=} {--response_schema_path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh workflow restful crud tests';

    /**
     * @var Tests\Workflow
     */
    private $workflow;

    public function __construct(Tests\WorkflowRestfulCrud $workflow = null)
    {
        parent::__construct();

        $this->workflow = new Tests\WorkflowRestfulCrud();
        if (!empty($workflow)) {
            $this->workflow = $workflow;
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
            $name = $this->argument('name');
            $options = [];
            $options['functionality_schema_path'] = $this->option('functionality_schema_path') ? $this->option('functionality_schema_path') : '';
            $options['request_schema_path'] = $this->option('request_schema_path') ? $this->option('request_schema_path') : '';
            $options['response_schema_path'] = $this->option('response_schema_path') ? $this->option('response_schema_path') : '';
            
            $this->workflow->refresh($name, $options);            

            $this->info($name.' workflow restful crud tests successfully updated');            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}