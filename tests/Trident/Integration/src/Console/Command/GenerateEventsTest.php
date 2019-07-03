<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Events;

class GenerateEventsTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $events;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->events = new Events($this->storage_disk, $this->storage_trident);

    }


    public function testGenerate()
    {
        $td_entity_type = 'workflow';
        $event_type = 'trigger_listener';
        $td_entity_name = $this->td_entity_name;
        
        $this->events->generate($td_entity_type, $event_type, $td_entity_name);

        $this->assertTrue(true);
    }

}
