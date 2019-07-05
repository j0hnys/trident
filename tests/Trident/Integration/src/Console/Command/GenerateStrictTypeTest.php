<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;
use j0hnys\Trident\Builders\StrictType;

class GenerateStrictTypeTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $strict_type;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //for workflow restful crud
        $this->storage_disk->makeDirectory($this->base_path.'/app/Models/.');

        $stub = $this->storage_disk->readFile($this->base_path.'/../Stubs/App/Models/DemoProcess.stub');
        $this->storage_disk->writeFile($this->base_path.'/app/Models/DemoProcess.php', $stub);
        exec('composer dump-autoload');

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk, $this->storage_trident);
        $mock_command = $this->createMock(\Illuminate\Console\Command::class);

        $this->workflow_restful_crud->generate($this->td_entity_name, $mock_command);

        //policy function
        $this->strict_type = new StrictType($this->storage_disk);

    }


    public function testGenerate()
    {
        $strict_type_name = 'struct';
        $function_name = 'otinanai';
        $domain = 'Workflows';
        
        $this->strict_type->generate($strict_type_name, $function_name, $this->td_entity_name, $domain);

        $this->assertTrue(true);
    }

}