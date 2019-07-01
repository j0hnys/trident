<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\WorkflowRestfulCrud;

class GenerateWorkflowRestfulCrudTest extends TestCase
{
    private $workflow_restful_crud;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $this->workflow_restful_crud = new WorkflowRestfulCrud($this->storage_disk);
    }

    
    public function testGenerate()
    {
        $td_entity_name = 'DemoProcess';
        $output_path = '';

        $this->workflow_restful_crud->generate($td_entity_name, $output_path);

        $this->assertTrue(true);
    }
}
