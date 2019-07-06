<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\StrictType;
use j0hnys\Trident\Console\Commands\GenerateStrictType;

class GenerateStrictTypeTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $strict_type;

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
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        //policy function
        $this->strict_type = new StrictType($this->storage_disk);

        //command behavioural test
        $this->mock_strict_type = $this->createMock(StrictType::class);
        $this->mock_command_strict_type = $this->getMockBuilder(GenerateStrictType::class)
            ->setConstructorArgs([$this->mock_strict_type])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $strict_type_name = '';
        $function_name = '';
        $entity_name = '';
        $domain = '';

        $this->mock_command_strict_type->expects($this->at(0))
            ->method('argument')
            ->willReturn($strict_type_name);

        $this->mock_command_strict_type->expects($this->at(1))
            ->method('argument')
            ->willReturn($function_name);

        $this->mock_command_strict_type->expects($this->at(2))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_strict_type->expects($this->at(0))
            ->method('option')
            ->willReturn($domain);

        $this->mock_command_strict_type->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_strict_type->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $strict_type_name = 'struct';
        $function_name = 'otinanai';
        $domain = 'Workflows';
        
        $this->strict_type->generate($strict_type_name, $function_name, $this->td_entity_name, $domain);

        $this->assertTrue(true);
    }

}
