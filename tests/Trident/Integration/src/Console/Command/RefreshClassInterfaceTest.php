<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Refresh\ClassInterface;
use j0hnys\Trident\Console\Commands\RefreshClassInterface;

class RefreshClassInterfaceTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $refresh_class_interface;

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
        $this->refresh_class_interface = new ClassInterface($this->storage_disk);

        //command behavioural test
        $this->mock_refresh_class_interface = $this->createMock(ClassInterface::class);
        $this->mock_command_refresh_class_interface = $this->getMockBuilder(RefreshClassInterface::class)
            ->setConstructorArgs([$this->mock_refresh_class_interface])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $name = '';
        $relative_input_path = '';
        $relative_output_path = '';

        $this->mock_command_refresh_class_interface->expects($this->at(0))
            ->method('argument')
            ->willReturn($name);

        $this->mock_command_refresh_class_interface->expects($this->at(1))
            ->method('argument')
            ->willReturn($relative_input_path);

        $this->mock_command_refresh_class_interface->expects($this->at(2))
            ->method('argument')
            ->willReturn($relative_output_path);
            
        $this->mock_command_refresh_class_interface->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_refresh_class_interface->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRun()
    {
        $name = 'DemoProcess';
        $relative_input_path = 'app/Trident/Workflows/Logic';
        $relative_output_path = 'app/Trident/Interfaces/Workflows/Logic';
                
        $this->refresh_class_interface->run($this->td_entity_name, $relative_input_path, $relative_output_path);

        $this->assertTrue(true);
    }

}
