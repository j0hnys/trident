<?php

namespace j0hnys\Trident\Tests\Integration;

use j0hnys\Trident\Tests\Base\TestCase;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;
use j0hnys\Trident\Console\Commands\Install as InstallCommand;

class InstallTest extends TestCase
{
    private $install;

    public function setUp(): void
    {
        parent::setUp();

        $this->install = new Install( $this->storage_disk );

        //command behavioural test
        $this->mock_install = $this->createMock(Install::class);
        $this->mock_command_install = $this->getMockBuilder(InstallCommand::class)
            ->setConstructorArgs([$this->mock_install])
            ->setMethods(['argument','option','info'])
            ->getMock();
    }


    public function testHandle()
    {

        $this->mock_command_install->handle();

        //assert
        $this->assertTrue(true);
    }

    
    public function testRun()
    {

        $this->install->run();

        $this->assertTrue(true);
    }
}
