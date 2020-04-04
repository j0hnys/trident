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
    protected $signature = "trident:generate:workflow_restful_crud {name} {--functionality_schema_path=} {--resource_schema_path=} {--validation_schema_path=} {--strict_type_schema_path=} ";

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

    public function __construct(Builders\WorkflowRestfulCrud $crud_workflow_builder = null)
    {
        parent::__construct();

        $Declarations = new Declarations();
        $Declarations->get();

        $this->crud_workflow_builder = new Builders\WorkflowRestfulCrud();
        if (!empty($crud_workflow_builder)) {
            $this->crud_workflow_builder = $crud_workflow_builder;
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
            $options = [];
            $name = $this->argument('name');
            $options['functionality_schema_path'] = $this->option('functionality_schema_path') ? $this->option('functionality_schema_path') : '';
            $options['resource_schema_path'] = $this->option('resource_schema_path') ? $this->option('resource_schema_path') : '';
            $options['validation_schema_path'] = $this->option('validation_schema_path') ? $this->option('validation_schema_path') : '';
            $options['strict_type_schema_path'] = $this->option('strict_type_schema_path') ? $this->option('strict_type_schema_path') : '';

            $this->crud_workflow_builder->generate($name, $options, $this);

            $this->info($name.' workflow restful crud successfully created');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
