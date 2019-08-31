<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulCrud;

class GenerateWorkflowRestfulCrudTest extends TestCase
{
    private $workflow_restful_crud;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);

        //command behavioural test
        $this->mock_workflow_restful_crud = $this->createMock(WorkflowRestfulCrud::class);
        $this->mock_command_workflow_restful_crud = $this->getMockBuilder(GenerateWorkflowRestfulCrud::class)
            ->setConstructorArgs([$this->mock_workflow_restful_crud])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $name = '';

        $this->mock_command_workflow_restful_crud->expects($this->at(0))
            ->method('argument')
            ->willReturn($name);
            
        $this->mock_command_workflow_restful_crud->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_restful_crud->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerateCrud()
    {
        //arrange
        $td_entity_name = 'DemoProcess';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];

        $mock_command->method('call')
            ->with(
                $this->callback(function($parameter) use (&$method_command) {
                    $method_command []= $parameter;
                    return true;
                }),
                $this->callback(function($parameter) use (&$method_parameters) {
                    $method_parameters []= $parameter;
                    return true;
                })
            )
            ->willReturn(true);

        //act
        $this->workflow_restful_crud->generateCrud($td_entity_name, $schema, $mock_command);    

        //assert
        $this->assertTrue(true);
    }


    public function testGenerateOther()
    {
        $td_entity_name = 'DemoProcess';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];

        $method_command = [];
        $method_parameters = [];

        $mock_command->method('call')
            ->with(
                $this->callback(function($parameter) use (&$method_command) {
                    $method_command []= $parameter;
                    return true;
                }),
                $this->callback(function($parameter) use (&$method_parameters) {
                    $method_parameters []= $parameter;
                    return true;
                })
            )
            ->willReturn(true);

            
        $this->workflow_restful_crud->generateOther($td_entity_name, $schema, $mock_command);    
        
        
        $this->assertTrue(true);
    }
    

    public function testGenerateWorkflow()
    {
        $td_entity_name = 'DemoProcess';

        $this->workflow_restful_crud->generateWorkflow($td_entity_name);

        $this->assertTrue(true);
    }


    public function testGenerate_()
    {
        $td_entity_name = 'DemoProcess';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];

        $this->workflow_restful_crud->generate($td_entity_name, $schema, $mock_command);

        $this->assertTrue(true);
    }

}
