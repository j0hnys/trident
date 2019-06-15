<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateWorkflow extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow {name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a workflow';

    private $workflow;

    public function __construct()
    {
        parent::__construct();

        $this->workflow = new Builders\Workflow();

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
            

            $this->info($name.' workflow successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}