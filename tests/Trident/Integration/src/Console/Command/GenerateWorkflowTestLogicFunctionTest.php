<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\Tests\Workflow as WorkFlowTests;
use j0hnys\Trident\Builders\Tests\WorkflowLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflowTestLogicFunction;

class GenerateWorkflowTestLogicFunctionTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $workflow_tests;
    private $workflow_tests_logic_function;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //workflow
        $this->workflow = new Workflow($this->storage_disk, $this->storage_trident);

        $this->workflow->generate($this->td_entity_name);
        sleep(3);
        exec('composer dump-autoload');

        //workflow tests
        $this->workflow_tests = new WorkFlowTests($this->storage_disk, $this->storage_trident);
        $this->workflow_tests->generate($this->td_entity_name);

        //workflow tests logic function
        $this->workflow_tests_logic_function = new WorkflowLogicFunction($this->storage_disk);

        //command behavioural test
        $this->mock_workflow_tests_logic_function = $this->createMock(WorkflowLogicFunction::class);
        $this->mock_command_workflow_tests_logic_function = $this->getMockBuilder(GenerateWorkflowTestLogicFunction::class)
            ->setConstructorArgs([$this->mock_workflow_tests_logic_function])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';

        $this->mock_command_workflow_tests_logic_function->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);
        
        $this->mock_command_workflow_tests_logic_function->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);
            
        $this->mock_command_workflow_tests_logic_function->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_tests_logic_function->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $function_name = 'otinanai';

        $this->workflow_tests_logic_function->generate($this->td_entity_name, $function_name);

        $this->assertTrue(true);
    }

}
