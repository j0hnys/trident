<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Build\Migrations;

class BuildMigrationsTest extends TestCase
{
    private $build_migrations;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $this->storage_disk->makeDirectory($this->base_path.'/database/generated_migrations/.');

        $this->build_migrations = new Migrations($this->storage_disk);
    }

    
    public function testGenerate()
    {
        $output_path = null;

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
        $this->build_migrations->generate($output_path, $mock_command);

        $method_parameters['--no-interaction'] = true;
        $method_parameters['--templatePath'] = realpath($this->base_path.'/../../vendor/xethron/laravel-4-generators/src/Way/Generators/templates/migration.txt');

        $this->artisan($method_command, $method_parameters);
        

        //assert
        $this->assertTrue(true);
    }
}
