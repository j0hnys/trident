<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders;

class GenerateEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:generate:events {td_entity_type} {event_type} {td_entity_name} ";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create an event';
    
    /**
     * @var Builders\Events
     */
    private $events;

    public function __construct()
    {
        parent::__construct();

        $this->events = new Builders\Events();

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
            $event_type = $this->argument('event_type');
            $td_entity_name = $this->argument('td_entity_name');
            

            $crud = $this->events->generate($td_entity_type, $event_type, $td_entity_name);
            

            $this->info($td_entity_type.' '.$td_entity_name.' event successfully created');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}