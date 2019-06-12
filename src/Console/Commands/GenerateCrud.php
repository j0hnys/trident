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
    protected $signature = "trident:generate:restful_crud {name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a RESTFUL CRUD';
    
    private $crud_builder;

    public function __construct()
    {
        parent::__construct();

        $this->crud_builder = new Crud\CrudBuilder();

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
            

            $crud = $this->crud_builder->generate($name);
            

            $this->info($name.' RESTFUL CRUD successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}