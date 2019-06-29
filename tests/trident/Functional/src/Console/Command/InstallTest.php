<?php

namespace Tests\Feature;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Builders\Setup\Install;

class InstallTest extends \Orchestra\Testbench\TestCase
{
    private $storage_disk;
    private $install;

    protected function getPackageProviders($app)
    {
        return [
            'j0hnys\Trident\TridentServiceProvider',
        ];
    }

    public function setUp(): void
    {
        parent::setUp();
        
        $base_path = __DIR__.'/../../../../../sandbox';

        $this->storage_disk = new Disk();
        $this->storage_disk->setBasePath($base_path);

        $this->storage_disk->makeDirectory($base_path.'/app');
        $this->storage_disk->makeDirectory($base_path.'/app/Providers');
        $this->storage_disk->makeDirectory($base_path.'/routes');

        $this->install = new Install( $this->storage_disk );
    }

    
    public function testHandle()
    {

        $this->install->run();

        $this->assertTrue(true);
    }
}
