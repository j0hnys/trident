<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\Crud\CrudBuilder;

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

    }


    public function testGenerate()
    {
        
        $this->crud_builder->generate($this->td_entity_name);

        $this->assertTrue(true);
    }

}
