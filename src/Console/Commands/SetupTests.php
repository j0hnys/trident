<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Setup;

class SetupTests extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:setup:tests";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trident test setup ';

    /**
     * @var Setup\Tests
     */
    private $setup_tests;

    public function __construct()
    {
        parent::__construct();

        $this->setup_tests = new Setup\Tests();
        
    }


    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
           

            $install = $this->setup_tests->run();
            

            $this->info('Trident tests setup successful');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}