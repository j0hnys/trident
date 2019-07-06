<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Refresh\ClassInterfaces;

class RefreshClassInterfacesTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $refresh_class_interfaces;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //workflow restful crud
        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);
        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        //refresh class interface
        $this->refresh_class_interfaces = new ClassInterfaces($this->storage_disk, $this->storage_trident);

    }


    public function testRun()
    {
        $type = 'workflow';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
                
        $this->refresh_class_interfaces->run($mock_command, $type);

        $this->assertTrue(true);
    }

}
