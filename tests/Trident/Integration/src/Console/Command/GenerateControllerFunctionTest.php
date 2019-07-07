<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Crud\ControllerFunction;
use j0hnys\Trident\Console\Commands\GenerateControllerFunction;

class GenerateControllerFunctionTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $controller_function;

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

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);

        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        $this->controller_function = new ControllerFunction($this->storage_disk);

        //command behavioural test
        $this->mock_controller_function = $this->createMock(ControllerFunction::class);
        $this->mock_command_controller_function = $this->getMockBuilder(GenerateControllerFunction::class)
            ->setConstructorArgs([$this->mock_controller_function])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';

        $this->mock_command_controller_function->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_controller_function->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_controller_function->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_controller_function->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $function_name = 'a_new_function';
        
        $this->controller_function->generate($this->td_entity_name, $function_name);

        $this->assertTrue(true);
    }

}
