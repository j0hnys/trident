<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh;
use j0hnys\Trident\Base\Constants\Declarations;


class RefreshWorkflowRestfulCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:workflow_restful_crud {name} {--functionality_schema_path=} {--resource_schema_path=} {--validation_schema_path=} {--strict_type_schema_path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh a workflow with the accompanied restful crud';
    
    /**
     * @var Refresh\WorkflowRestfulCrud
     */
    private $crud_workflow_refresh;

    public function __construct(Refresh\WorkflowRestfulCrud $crud_workflow_refresh = null)
    {
        parent::__construct();

        $Declarations = new Declarations();
        $Declarations->get();

        $this->crud_workflow_refresh = new Refresh\WorkflowRestfulCrud();
        if (!empty($crud_workflow_refresh)) {
            $this->crud_workflow_refresh = $crud_workflow_refresh;
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
                        
            
            //workflow building
            $crud = $this->crud_workflow_refresh->refresh($name, $options, $this);

            
            $this->info($name.' workflow restful crud successfully refreshed');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
