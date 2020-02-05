<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\Tests\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Tests\WorkflowRestfulFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulFunctionTest;

class GenerateWorkflowRestfulFunctionTestTest extends TestCase
{
    private $td_entity_name;
    private $workflow_restful_function_tests;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //workflow
        $this->workflow = new Workflow($this->storage_disk, $this->storage_trident);
        $this->workflow->generate($this->td_entity_name);

        //workflow resource tests
        $this->workflow_restful_crud_tests = new WorkflowRestfulCrud($this->storage_disk);
        $options = [
            'functionality_schema_path' => '',
            'request_schema_path' => '',
            'response_schema_path' => '',
        ];
        $this->workflow_restful_crud_tests->generate($this->td_entity_name, $options);

        //workflow logic function
        $this->workflow_restful_function_tests = new WorkflowRestfulFunction($this->storage_disk);

        //command behavioural test
        $this->mock_workflow_restful_function_tests = $this->createMock(WorkflowRestfulFunction::class);
        $this->mock_command_workflow_restful_function_tests = $this->getMockBuilder(GenerateWorkflowRestfulFunctionTest::class)
            ->setConstructorArgs([$this->mock_workflow_restful_function_tests])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';
        $functionality_schema_path = '';
        $request_schema_path = '';
        $response_schema_path = '';

        $this->mock_command_workflow_restful_function_tests->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_workflow_restful_function_tests->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_workflow_restful_function_tests->expects($this->at(2))
            ->method('option')
            ->willReturn($functionality_schema_path);

        $this->mock_command_workflow_restful_function_tests->expects($this->at(3))
            ->method('option')
            ->willReturn($request_schema_path);

        $this->mock_command_workflow_restful_function_tests->expects($this->at(4))
            ->method('option')
            ->willReturn($response_schema_path);
            
        $this->mock_command_workflow_restful_function_tests->expects($this->at(5))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_restful_function_tests->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $function_name = 'aaaa';
        $options = [
            'functionality_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Functionality.json',
            'request_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Request.json',
            'response_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Response.json',
        ];

        $this->workflow_restful_function_tests->generate($this->td_entity_name, $function_name, $options);

        $this->assertTrue(true);
    }

}
