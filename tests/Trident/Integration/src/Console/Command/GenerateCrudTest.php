<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Crud\CrudBuilder;
use j0hnys\Trident\Console\Commands\GenerateCrud;

class GenerateCrudTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $crud_builder;

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

        $this->crud_builder = new CrudBuilder($this->storage_disk, $this->storage_trident);

        //command behavioural test
        $this->mock_crud_builder = $this->createMock(CrudBuilder::class);
        $this->mock_command_crud_builder = $this->getMockBuilder(GenerateCrud::class)
            ->setConstructorArgs([$this->mock_crud_builder])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $entity_name = '';
        $model_db_name = '';
        $schema_path = '';

        $this->mock_command_crud_builder->expects($this->at(0))
            ->method('argument')
            ->willReturn($entity_name);

        $this->mock_command_crud_builder->expects($this->at(1))
            ->method('option')
            ->willReturn($model_db_name);

        $this->mock_command_crud_builder->expects($this->at(2))
            ->method('option')
            ->willReturn($schema_path);

        $this->mock_command_crud_builder->expects($this->at(0))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_crud_builder->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        
        $this->crud_builder->generate($this->td_entity_name);

        $this->assertTrue(true);
    }

}
