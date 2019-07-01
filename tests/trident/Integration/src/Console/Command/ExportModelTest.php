<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Builders\Export\Model;

class ExportModelTest extends TestCase
{
    private $export_model;

    public function setUp(): void
    {
        parent::setUp();

        $install = new Install($this->storage_disk);
        $install->run();

        $this->export_model = new Model($this->storage_disk);
    }

    
    public function testGenerate()
    {
        $td_entity_name = 'DemoProcess';
        $output_path = '';

        $this->export_model->generate($td_entity_name, $output_path);

        $this->assertTrue(true);
    }
}
