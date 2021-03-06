<?php

namespace j0hnys\Trident\Console\Commands;

use Illuminate\Console\Command;
use j0hnys\Trident\Builders\Remove;

class RemoveEntity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = "trident:remove:entity {td_entity_name}";

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes trident entity completely or a part.';

    /**
     * @var Remove\Entity
     */
    private $remove_entity;

    public function __construct(Remove\Entity $remove_entity = null)
    {
        parent::__construct();

        $this->remove_entity = new Remove\Entity();
        if (!empty($remove_entity)) {
            $this->remove_entity = $remove_entity;
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
            $td_entity_name = $this->argument('td_entity_name');
            

            $crud = $this->remove_entity->run($td_entity_name);
            

            $this->info($td_entity_name.' removed successfully');
            
        } catch (\Exception $ex) {
            $this->error($ex->getMessage() . ' on line ' . $ex->getLine() . ' in ' . $ex->getFile());
        }
    }

}