<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Factories;

class GenerateFactories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:factories {--force}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create database factories for all models';

    /**
     * @var Factories\Factories
     */
    private $factories;

    public function __construct(Factories\Factories $factories = null)
    {
        parent::__construct();

        $this->factories = new Factories\Factories();
        if (!empty($factories)) {
            $this->factories = $factories;
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
            $force = $this->option('force');
            
            $crud = $this->factories->generate($this->laravel, $this, $force);            

            $this->info('factories successfully created!');            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}