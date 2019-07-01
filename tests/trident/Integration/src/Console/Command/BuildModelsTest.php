<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Build\Models;

class BuildModelsTest extends TestCase
{
    private $build_models;

    public function setUp(): void
    {
        parent::setUp();

        $this->artisan('list');

        $install = new Install($this->storage_disk);
        $install->run();

        $this->build_models = new Models($this->storage_disk);
    }

    
    public function testGenerate()
    {
        $data = [
            'output_path' => ''
        ];

        // $symfony_application = new \Symfony\Component\Console\Application('otinanai');
        // $command = new \Illuminate\Console\Command();
        // $command->setApplication($symfony_application);
        // $command->setLaravel(app());

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

        $this->build_models->generate($data, $mock_command);

        $this->artisan('list');
        
        dump([
            '$method_command' => $method_command,
            '$method_parameters' => $method_parameters,
        ]);

        // $this->storage_disk->makeDirectory($method_parameters['--output-path'].'');

        $this->artisan($method_command, $method_parameters);



        $this->assertTrue(true);
    }
}
