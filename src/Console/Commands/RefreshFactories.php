<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh\Factories;

class RefreshFactories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:factories";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh all database factories';

    /**
     * @var Factories
     */
    private $factories;

    public function __construct(Factories $factories = null)
    {
        parent::__construct();

        $this->factories = new Factories();
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
            
            $builders = $this->factories->refresh($this->laravel);

            $this->info('database factories refreshed!');
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}