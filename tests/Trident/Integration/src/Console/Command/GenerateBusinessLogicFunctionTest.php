<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\BusinessLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateBusinessLogicFunction;

class GenerateBusinessLogicFunctionTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $business_logic_function;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);

        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        $this->business_logic_function = new BusinessLogicFunction($this->storage_disk);

        //command behavioural test
        $this->mock_business_logic_function = $this->createMock(BusinessLogicFunction::class);
        $this->mock_command_business_logic_function = $this->getMockBuilder(GenerateBusinessLogicFunction::class)
            ->setConstructorArgs([$this->mock_business_logic_function])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }

    public function testHandle()
    {
        $entity_name = '';
        $function_name = '';

        $this->mock_command_business_logic_function->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_business_logic_function->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_business_logic_function->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_business_logic_function->handle();

        //assert
        $this->assertTrue(true);
    }

    public function testGenerate()
    {
        $function_name = 'a_new_function';
        
        $this->business_logic_function->generate($this->td_entity_name, $function_name);

        $this->assertTrue(true);
    }

}
