<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Setup\Tests;
use j0hnys\Trident\Console\Commands\SetupTests;

class SetupTestsTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $setup_tests;

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
        $this->storage_disk->makeDirectory($this->base_path.'/tests/.');
        $this->setup_tests = new Tests($this->storage_disk);

        //command behavioural test
        $this->mock_setup_tests = $this->createMock(Tests::class);
        $this->mock_command_setup_tests = $this->getMockBuilder(SetupTests::class)
            ->setConstructorArgs([$this->mock_setup_tests])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
            
        $this->mock_command_setup_tests->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_setup_tests->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRun()
    {
                        
        $this->setup_tests->run();

        $this->assertTrue(true);
    }

}
