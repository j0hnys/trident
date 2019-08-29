<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\WorkflowLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflowLogicFunction;

class GenerateWorkflowLogicFunctionTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $workflow_logic_function;

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
        $this->workflow_logic_function = new WorkflowLogicFunction($this->storage_disk);

        //command behavioural test
        $this->mock_workflow_logic_function = $this->createMock(WorkflowLogicFunction::class);
        $this->mock_command_workflow_logic_function = $this->getMockBuilder(GenerateWorkflowLogicFunction::class)
            ->setConstructorArgs([$this->mock_workflow_logic_function])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';

        $this->mock_command_workflow_logic_function->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_workflow_logic_function->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);
            
        $this->mock_command_workflow_logic_function->expects($this->at(2))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_logic_function->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerateOther()
    {
        $td_entity_name = 'DemoProcess';
        $function_name = 'otinanai';
        $options = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

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

            
        $this->workflow_logic_function->generateOther($td_entity_name, $function_name, $options, $mock_command);    
        
        
        $this->assertTrue(true);
    }


    public function testGenerateLogicFunction()
    {
        $function_name = 'otinanai';

        $this->workflow_logic_function->generateLogicFunction($this->td_entity_name, $function_name);

        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $function_name = 'otinanai';
        $options = [
            'functionality_schema_path' => '',
            'validation_schema_path' => '',
            'strict_type_schema_path' => '',
            'resource_schema_path' => '',
        ];
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_logic_function->generate($this->td_entity_name, $function_name, $options, $mock_command);

        $this->assertTrue(true);
    }

}
