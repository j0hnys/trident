<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Workflow;
use j0hnys\Trident\Builders\Tests\Workflow as WorkFlowTests;

class GenerateWorkflowTestsTest extends TestCase
{
    private $workflow_restful_crud;
    private $td_entity_name;
    private $workflow_tests;

    public function setUp(): void
    {
        parent::setUp();

        $this->td_entity_name = 'DemoProcess';

        $install = new Install($this->storage_disk);
        $install->run();

        //workflow
        $this->workflow = new Workflow($this->storage_disk);

        $this->workflow->generate($this->td_entity_name);
        exec('composer dump-autoload');

        //workflow tests
        $this->workflow_tests = new WorkFlowTests($this->storage_disk);

    }


    public function testGenerate()
    {

        $this->workflow_tests->generate($this->td_entity_name);

        $this->assertTrue(true);
    }

}
