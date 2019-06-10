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
    protected $signature = "trident:generate:factory {model} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a factory for a model';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $model = $this->argument('model');
            
            $crud = new Factories\Factory($this->laravel,$model);
            

            $this->info($model.' factory successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}