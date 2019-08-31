<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Process;
use j0hnys\Trident\Console\Commands\GenerateProcess;

class GenerateProcessTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $processes;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //for workflow restful crud
        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $this->workflow_restful_crud->generate($this->td_entity_name, $schema, $mock_command);

        //policy function
        $this->processes = new Process($this->storage_disk);

        //command behavioural test
        $this->mock_processes = $this->createMock(Process::class);
        $this->mock_command_processes = $this->getMockBuilder(GenerateProcess::class)
            ->setConstructorArgs([$this->mock_processes])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $td_entity_name = '';
        $process_name = '';
        $schema_path = '';

        $this->mock_command_processes->expects($this->at(0))
            ->method('argument')
            ->willReturn($td_entity_name);

        $this->mock_command_processes->expects($this->at(1))
            ->method('argument')
            ->willReturn($process_name);

        $this->mock_command_processes->expects($this->at(2))
            ->method('argument')
            ->willReturn($schema_path);

        $this->mock_command_processes->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_processes->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $process_name = 'Index';
        $schema_path = $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Processes/Index.json';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        
        $this->processes->generate($this->td_entity_name, $process_name, $schema_path, $mock_command);

        $this->assertTrue(true);
    }

}
