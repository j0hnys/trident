<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Refresh;

class RefreshClassInterface extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:refresh:class_interface {name} {relative_input_path} {relative_output_path}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refreshes the interface that a class implements according to class functions';
    
    /**
     * @var Refresh\ClassInterface
     */
    private $refresh_class_interface;

    public function __construct()
    {
        parent::__construct();

        $this->refresh_class_interface = new Refresh\ClassInterface();
        
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $name = $this->argument('name');
            $relative_input_path = $this->argument('relative_input_path');
            $relative_output_path = $this->argument('relative_output_path');
            

            $crud = $this->refresh_class_interface->run(
                $name,
                $relative_input_path,
                $relative_output_path
            );
            

            $this->info($name.'Interface updated successfully!');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}
