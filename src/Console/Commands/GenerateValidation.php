<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateValidation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:validation {entity_name} {function_name} {--schema_path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a validation';

    /**
     * @var Builders\Validation
     */
    private $validation;

    public function __construct(Builders\Validation $validation = null)
    {
        parent::__construct();

        $this->validation = new Builders\Validation();
        if (!empty($validation)) {
            $this->validation = $validation;
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
            $schema_path = $this->option('schema_path');
            

            $crud = $this->validation->generate($entity_name, $function_name, $schema_path);
            

            $this->info($entity_name.' '.$function_name.' validation successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}