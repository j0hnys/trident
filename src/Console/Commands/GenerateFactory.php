<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Factories;

class GenerateFactory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:factory {model} {--force}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a factory for a model';

    /**
     * @var Factories\Factory
     */
    private $factory;

    public function __construct(Factories\Factory $factory = null)
    {
        parent::__construct();

        $this->factory = new Factories\Factory();
        if (!empty($factory)) {
            $this->factory = $factory;
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
            $model = $this->argument('model');
            $force = $this->option('force');            
            
            $crud = $this->factory->generate($this->laravel, $model, $force);            

            $this->info($model.' factory successfully created');            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}