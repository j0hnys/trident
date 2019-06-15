<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateStrictType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:strict_type {strict_type_name} {function_name} {entity_name} {--workflow} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a strict type';

    private $strict_type;

    public function __construct()
    {
        parent::__construct();

        $this->strict_type = new Builders\StrictType();

    }
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $strict_type_name = $this->argument('strict_type_name');
            $function_name = $this->argument('function_name');
            $entity_name = $this->argument('entity_name');
            $domain = $this->option('workflow')?'Workflows':'Business';
            
            $this->strict_type->generate($strict_type_name, $function_name, $entity_name, $domain);

            $this->info($strict_type_name.' '.$entity_name.' '.$function_name.' strict type successfully created for '.$domain);
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}