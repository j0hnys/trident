<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Refresh\WorkflowRestfulCrud as RefreshWorkflowRestfulCrudBuilder;
use j0hnys\Trident\Console\Commands\RefreshWorkflowRestfulCrud;

class RefreshWorkflowRestfulCrudTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $refresh_workflow_restful_crud;

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
        $schema = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $this->workflow_restful_crud->generate($this->td_entity_name, $schema, $mock_command);

        //refresh class interface
        $this->refresh_workflow_restful_crud = new RefreshWorkflowRestfulCrudBuilder($this->storage_disk);

        //command behavioural test
        $this->mock_refresh_workflow_restful_crud = $this->createMock(RefreshWorkflowRestfulCrudBuilder::class);
        $this->mock_command_refresh_workflow_restful_crud = $this->getMockBuilder(RefreshWorkflowRestfulCrud::class)
            ->setConstructorArgs([$this->mock_refresh_workflow_restful_crud])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';

        $this->mock_command_refresh_workflow_restful_crud->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_refresh_workflow_restful_crud->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_refresh_workflow_restful_crud->expects($this->at(2))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_refresh_workflow_restful_crud->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRefresh()
    {
        $options = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
                
        $this->refresh_workflow_restful_crud->refresh($this->td_entity_name, $options, $mock_command);

        $this->assertTrue(true);
    }

}
