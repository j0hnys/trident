<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Crud;

class GenerateCrud extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:restful_crud {name} {--model_db_name=} {--schema_path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a RESTFUL CRUD';
    
    /**
     * @var Crud\CrudBuilder
     */
    private $crud_builder;

    public function __construct(Crud\CrudBuilder $crud_builder = null)
    {
        parent::__construct();

        $this->crud_builder = new Crud\CrudBuilder();
        if (!empty($crud_builder)) {
            $this->crud_builder = $crud_builder;
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
            $model_db_name = $this->option('model_db_name') ? $this->option('model_db_name') : '';
            $schema_path = $this->option('schema_path') ? $this->option('schema_path') : '';
            

            $crud = $this->crud_builder->generate($name, $model_db_name, $schema_path);
            

            $this->info($name.' RESTFUL CRUD successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}