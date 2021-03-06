<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Build\Migrations;

class BuildMigrations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:build:migrations {--output-path=} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create all migrations from current database connection';
    
    /**
     * @var Migrations
     */
    private $migrations;

    public function __construct(Migrations $migrations = null)
    {
        parent::__construct();

        $this->migrations = new Migrations();
        if (!empty($migrations)) {
            $this->migrations = $migrations;
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
            $output_path = $this->option('output-path');
            
            $this->migrations->generate($output_path, $this);
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }
    

}