<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh;

class RefreshDIBinds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:di_binds";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes DI containers binds';
    
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            // $name = $this->argument('name');
            

            $crud = new Refresh\DIBinds();
            

            // $this->info($name.' workflow tests successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
