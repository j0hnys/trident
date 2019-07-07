<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Refresh\DIBinds;
use j0hnys\Trident\Console\Commands\RefreshDIBinds;

class RefreshDIBindsTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $refresh_di_binds;

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
        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        //refresh class interface
        $this->refresh_di_binds = new DIBinds($this->storage_disk, $this->storage_trident);

        //command behavioural test
        $this->mock_refresh_di_binds = $this->createMock(DIBinds::class);
        $this->mock_command_refresh_di_binds = $this->getMockBuilder(RefreshDIBinds::class)
            ->setConstructorArgs([$this->mock_refresh_di_binds])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        
        $this->mock_command_refresh_di_binds->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testRun()
    {
                        
        $this->refresh_di_binds->run();

        $this->assertTrue(true);
    }

}
