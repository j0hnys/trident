<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Console\Commands\GenerateWorkflow;

class GenerateWorkflowTest extends TestCase
{
    private $td_entity_name;
    private $workflow;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        $this->workflow = new Workflow($this->storage_disk, $this->storage_trident);

        //command behavioural test
        $this->mock_workflow = $this->createMock(Workflow::class);
        $this->mock_command_workflow = $this->getMockBuilder(GenerateWorkflow::class)
            ->setConstructorArgs([$this->mock_workflow])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $name = '';

        $this->mock_command_workflow->expects($this->at(0))
            ->method('argument')
            ->willReturn($name);
            
        $this->mock_command_workflow->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_workflow->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
                
        $this->workflow->generate($this->td_entity_name);

        $this->assertTrue(true);
    }

}
