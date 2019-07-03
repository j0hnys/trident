<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Build\ModelExports;

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
