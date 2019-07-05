<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;

class GenerateWorkflowTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $workflow;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        $this->workflow = new Workflow($this->storage_disk);

    }


    public function testGenerate()
    {
                
        $this->workflow->generate($this->td_entity_name);

        $this->assertTrue(true);
    }

}
