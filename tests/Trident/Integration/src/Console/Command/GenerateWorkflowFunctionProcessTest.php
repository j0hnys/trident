<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Process;
use j0hnys\Trident\Builders\Refresh\ClassInterface;
use j0hnys\Trident\Builders\WorkflowFunctionProcess;
use j0hnys\Trident\Console\Commands\GenerateWorkflowFunctionProcess;

class GenerateWorkflowFunctionProcessTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $workflow_function_process;

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
        sleep(3);
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

        //for the process
        $process_name = 'Index';
        $schema_path = $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Processes/Index.json';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        $processes = new Process($this->storage_disk);
        $processes->generate($this->td_entity_name, $process_name, $schema_path, $mock_command);
        
        $class_interface = new ClassInterface($this->storage_disk);
        $class_interface->run(
            $process_name,
            'app/Trident/Workflows/Processes/'.$this->td_entity_name.'',  //'app/Trident/Workflows/Logic',
            'app/Trident/Interfaces/Workflows/Processes/'.$this->td_entity_name.''    //'app/Trident/Interfaces/Workflows/Logic',
        );

        sleep(3);
        exec('composer dump-autoload');

        //policy function
        $this->workflow_function_process = new WorkflowFunctionProcess($this->storage_disk);

        //command behavioural test
        $this->mock_workflow_function_process = $this->createMock(WorkflowFunctionProcess::class);
        $this->mock_command_workflow_function_process = $this->getMockBuilder(GenerateWorkflowFunctionProcess::class)
            ->setConstructorArgs([$this->mock_workflow_function_process])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $type = '';
        $function_name = '';
        $schema_path = '';

        $this->mock_command_workflow_function_process->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_workflow_function_process->expects($this->at(1))
            ->method('argument')
            ->willReturn($type);

        $this->mock_command_workflow_function_process->expects($this->at(2))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_workflow_function_process->expects($this->at(3))
            ->method('argument')
            ->willReturn($schema_path);

        $this->mock_command_workflow_function_process->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow_function_process->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $type = '';
        $function_name = '';
        $schema_path = $this->base_path.'/../Stubs/_Solution/Schemas/DemoProcess/Resource/Functionality.json';
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);
        
        $this->workflow_function_process->generate($this->td_entity_name, $type, $function_name, $schema_path, $mock_command);

        $this->assertTrue(true);
    }


    private function schema()
    {
        $schema = [
            'workflow' => [ //<-- this is going to be generated from schema file
                'type' => 'cascade',
                'schema' => [
                    'initial_state' => 'draft',
                    'states'        => ['draft', 'review', 'rejected', 'published'],
                    'transitions'   => [
                        'to_review' => [
                            'from' => 'draft',
                            'to'   => 'review'
                        ],
                        'publish' => [
                            'from' => 'review',
                            'to'   => 'published'
                        ],
                        'reject_published' => [
                            'from' => 'published',
                            'to'   => 'rejected'
                        ]
                    ],
                    'transition_listeners' => [
                        'to_review' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_1',
                        'publish' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_2',
                        'reject_published' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_3'
                    ],
                ]
            ],
        ];

        return $schema;
    }

}
