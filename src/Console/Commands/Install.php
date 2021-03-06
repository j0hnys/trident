<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Setup;

class Install extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:install";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Trident installer';

    /**
     * @var Setup\Install
     */
    private $install;

    public function __construct(Setup\Install $install = null)
    {
        parent::__construct();

        $this->install = new Setup\Install();
        if (!empty($install)) {
            $this->install = $install;
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
           

            $install = $this->install->run();
            

            $this->info('Trident installed successfully');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}