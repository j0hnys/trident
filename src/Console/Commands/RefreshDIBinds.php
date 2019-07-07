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
     * @var Refresh\DIBinds
     */
    private $refresh_di_binds;

    public function __construct(Refresh\DIBinds $refresh_di_binds = null)
    {
        parent::__construct();

        $this->refresh_di_binds = new Refresh\DIBinds();
        if (!empty($refresh_di_binds)) {
            $this->refresh_di_binds = $refresh_di_binds;
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

            $crud = $this->refresh_di_binds->run();            
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
