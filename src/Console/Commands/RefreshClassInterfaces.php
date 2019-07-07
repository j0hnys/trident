<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh;

class RefreshClassInterfaces extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:class_interfaces {td_entity_type}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes all the interfaces from the classes of a specific type/folder';
    
    /**
     * @var Refresh\ClassInterfaces
     */
    private $refresh_class_interfaces;

    public function __construct(Refresh\ClassInterfaces $refresh_class_interfaces = null)
    {
        parent::__construct();

        $this->refresh_class_interfaces = new Refresh\ClassInterfaces();
        if (!empty($refresh_class_interfaces)) {
            $this->refresh_class_interfaces = $refresh_class_interfaces;
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
            $td_entity_type = $this->argument('td_entity_type');
            

            $crud = $this->refresh_class_interfaces->run(
                $this,
                $td_entity_type
            );
            

            $this->info($td_entity_type.'Interface updated successfully!');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
