<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\Tests\WorkflowRestfulCrud;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulCrudTests;

class GenerateWorkflowRestfulCrudTestsTest extends TestCase
{
    private $td_entity_name;
    private $workflow_restful_crud_tests;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //workflow
        $this->workflow = new Workflow($this->storage_disk, $this->storage_trident);

        $this->workflow->generate($this->td_entity_name);


        //workflow logic function
        $this->workflow_restful_crud_tests = new WorkflowRestfulCrud($this->storage_disk);

        //command behavioural test
        $this->mock_workflow_restful_crud_tests = $this->createMock(WorkflowRestfulCrud::class);
        $this->mock_command_workflow_restful_crud_tests = $this->getMockBuilder(GenerateWorkflowRestfulCrudTests::class)
            ->setConstructorArgs([$this->mock_workflow_restful_crud_tests])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';

        $this->mock_command_workflow_restful_crud_tests->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);
            
        $this->mock_command_workflow_restful_crud_tests->expects($this->at(1))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_restful_crud_tests->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $options = [
            'functionality_schema_path' => '',
            'request_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Request.json',
            'response_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Response.json',
        ];

        $this->workflow_restful_crud_tests->generate($this->td_entity_name, $options);

        $this->assertTrue(true);
    }

}
