<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\Tests\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Tests\WorkflowRestfulFunction;
use j0hnys\Trident\Builders\Refresh\Tests\WorkflowRestfulFunction as RefreshWorkflowRestfulFunction;
use j0hnys\Trident\Console\Commands\RefreshWorkflowRestfulFunctionTest;

class RefreshWorkflowRestfulFunctionTestTest extends TestCase
{
    private $td_entity_name;
    private $refresh_workflow_restful_function_test;

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

        //workflow resource function tests
        $this->workflow_restful_function_tests = new WorkflowRestfulFunction($this->storage_disk);
        $function_name = 'aaaa';
        $options = [
            'functionality_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Functionality.json',
            'request_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Request.json',
            'response_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Response.json',
        ];
        $this->workflow_restful_function_tests->generate($this->td_entity_name, $function_name, $options);

        //workflow logic function
        $this->refresh_workflow_restful_function_test = new RefreshWorkflowRestfulFunction($this->storage_disk);

        //command behavioural test
        $this->mock_refresh_workflow_restful_function_test = $this->createMock(RefreshWorkflowRestfulFunction::class);
        $this->mock_command_refresh_workflow_restful_function_test = $this->getMockBuilder(RefreshWorkflowRestfulFunctionTest::class)
            ->setConstructorArgs([$this->mock_refresh_workflow_restful_function_test])
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

        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(2))
            ->method('option')
            ->willReturn($functionality_schema_path);

        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(3))
            ->method('option')
            ->willReturn($request_schema_path);

        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(4))
            ->method('option')
            ->willReturn($response_schema_path);
            
        $this->mock_command_refresh_workflow_restful_function_test->expects($this->at(5))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_refresh_workflow_restful_function_test->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRefresh()
    {
        $function_name = 'aaaa';
        $options = [
            'functionality_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Functionality.json',
            'request_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Request.json',
            'response_schema_path' => $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Tests/Resource/Response.json',
        ];

        $this->refresh_workflow_restful_function_test->refresh($this->td_entity_name, $function_name, $options);

        $this->assertTrue(true);
    }

}
