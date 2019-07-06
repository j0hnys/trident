<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Build\ModelExports;
use j0hnys\Trident\Console\Commands\BuildModelExports;

class BuildModelExportsTest extends TestCase
{
    private $model_exports;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');
        $this->storage_disk->makeDirectory($this->base_path.'/database/generated_model_exports/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->model_exports = new ModelExports($this->storage_disk);

        //command behavioural test
        $this->mock_model_exports = $this->createMock(ModelExports::class);
        $this->mock_command_model_exports = $this->getMockBuilder(BuildModelExports::class)
            ->setConstructorArgs([$this->mock_model_exports])
            ->setMethods(['option'])
            ->getMock();
    }


    public function testHandle()
    {
        $input_path = '';
        $output_path = '';

        $this->mock_command_model_exports->expects($this->at(0))
            ->method('option')
            ->willReturn($input_path);

        $this->mock_command_model_exports->expects($this->at(1))
            ->method('option')
            ->willReturn($output_path);

        $this->mock_command_model_exports->handle();

        //assert
        $this->assertTrue(true);
    }

    
    public function testGenerate()
    {
        $data = [
            'input_path' => null,
            'output_path' => null,
        ];

        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $method_command = '';
        $method_parameters = [];

        $mock_command->method('call')
            ->with(
                $this->callback(function($parameter) use (&$method_command) {
                    $method_command = $parameter;
                    return true;
                }),
                $this->callback(function($parameter) use (&$method_parameters) {
                    $method_parameters = $parameter;
                    return true;
                })
            )
            ->willReturn(true);

            
        //act
        $this->model_exports->generate($data, $mock_command);

        $this->artisan($method_command, $method_parameters);
        

        //assert
        $this->assertTrue(true);
    }
}
