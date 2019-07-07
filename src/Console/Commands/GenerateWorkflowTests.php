<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Tests;

class GenerateWorkflowTests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow_tests {name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create workflow tests';

    /**
     * @var Tests\Workflow
     */
    private $workflow;

    public function __construct(Tests\Workflow $workflow = null)
    {
        parent::__construct();

        $this->workflow = new Tests\Workflow();
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
            

            $crud = $this->workflow->generate($name);
            

            $this->info($name.' workflow tests successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}