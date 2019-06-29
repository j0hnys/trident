<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;
use j0hnys\Trident\Base\Constants\Declarations;


class GenerateWorkflowRestfulCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:workflow_restful_crud {name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a workflow with the accompanied restful crud';
    
    /**
     * @var Builders\WorkflowRestfulCrud
     */
    private $crud_workflow_builder;

    public function __construct()
    {
        parent::__construct();

        $Declarations = new Declarations();
        $Declarations->get();

        $this->crud_workflow_builder = new Builders\WorkflowRestfulCrud();
        
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
                        
            //workflow building
            $crud = $this->crud_workflow_builder->generate($name, $this);

            
            $this->info($name.' workflow restful crud successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
