<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;

class InstallTest extends TestCase
{
    private $install;

    public function setUp(): void
    {
        parent::setUp();

        $this->install = new Install( $this->storage_disk );
    }

    
    public function testRun()
    {

        $this->install->run();

        $this->assertTrue(true);
    }
}
