<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Factories\Factory;
use j0hnys\Trident\Console\Commands\GenerateFactory;

class GenerateFactoryTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $factory;

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

        $this->factory = new Factory($this->storage_disk);

        //command behavioural test
        $this->mock_factory = $this->createMock(Factory::class);
        $this->mock_command_factory = $this->getMockBuilder(GenerateFactory::class)
            ->setConstructorArgs([$this->mock_factory])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {
        $model = '';
        $force = false;

        $this->mock_command_factory->expects($this->at(0))
            ->method('argument')
            ->willReturn($model);

        $this->mock_command_factory->expects($this->at(1))
            ->method('option')
            ->willReturn($force);

        $this->mock_command_factory->expects($this->at(2))
            ->method('info')
            ->willReturn(null);

        $this->mock_command_factory->handle();

        //assert
        $this->assertTrue(true);
    }


    public function testGenerate()
    {
        $laravel = app();
        $model_full_class = 'App\\Models\\DemoProcess';
        
        $this->factory->generate($laravel, $model_full_class);

        $this->assertTrue(true);
    }

}
