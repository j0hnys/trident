<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Build\Models;
use j0hnys\Trident\Console\Commands\BuildModels;

class BuildModelsTest extends TestCase
{
    private $build_models;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $base_path = $this->storage_disk->getBasePath();    //<--
        $base_path = str_replace('C:\\','/',$base_path);    //<-- krlove needs full starting with `/` (linux!!) 
        
        $this->storage_disk->setBasePath($base_path);

        $this->build_models = new Models($this->storage_disk);

        //command behavioural test
        $this->mock_build_models = $this->createMock(Models::class);
        $this->mock_command_build_models = $this->getMockBuilder(BuildModels::class)
            ->setConstructorArgs([$this->mock_build_models])
            ->setMethods(['option'])
            ->getMock();
    }


    public function testHandle()
    {
        $output_path = '';

        $this->mock_command_build_models->expects($this->at(0))
            ->method('option')
            ->willReturn($output_path);

        $this->mock_command_build_models->handle();

        //assert
        $this->assertTrue(true);
    }

    
    public function testGenerate()
    {
        $data = [
            'output_path' => ''
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

        //act
        $this->build_models->generate($data, $mock_command);

        foreach ($method_command as $i => $method_command_) {
            $this->artisan($method_command[$i], $method_parameters[$i]);
        }


        //assert
        $this->assertTrue(true);
    }
}
