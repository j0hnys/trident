<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Remove\Entity;
use j0hnys\Trident\Console\Commands\RemoveEntity;

class RemoveEntityTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $remove_entity;

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
        $this->remove_entity = new Entity($this->storage_disk, $this->storage_trident);

        //command behavioural test
        $this->mock_remove_entity = $this->createMock(Entity::class);
        $this->mock_command_remove_entity = $this->getMockBuilder(RemoveEntity::class)
            ->setConstructorArgs([$this->mock_remove_entity])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $td_entity_name = '';

        $this->mock_command_remove_entity->expects($this->at(0))
            ->method('argument')
            ->willReturn($td_entity_name);
            
        $this->mock_command_remove_entity->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_remove_entity->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRun()
    {
                        
        $this->remove_entity->run($this->td_entity_name);

        $this->assertTrue(true);
    }

}
