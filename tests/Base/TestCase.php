<?php

namespace j0hnys\Trident\Tests\Base;

use j0hnys\Trident\Base\Storage\Disk;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $base_path;
    protected $storage_disk;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->base_path = __DIR__.'/../sandbox';

        $this->storage_disk = new Disk();
        $this->storage_disk->setBasePath($this->base_path);

        $this->storage_disk->deleteDirectoryAndFiles($this->base_path.'/app');
        $this->storage_disk->deleteDirectoryAndFiles($this->base_path.'/routes');

        $this->storage_disk->makeDirectory($this->base_path.'/app/.');
        $this->storage_disk->makeDirectory($this->base_path.'/app/Providers/.');
        $this->storage_disk->makeDirectory($this->base_path.'/routes/.');

    }

    protected function getPackageProviders($app)
    {
        return [
            'j0hnys\Trident\TridentServiceProvider',
        ];
    }


}
