<?php

namespace j0hnys\Trident\Tests\Base;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected $base_path;
    protected $storage_disk;
    protected $storage_trident;
    
    public function setUp(): void
    {
        parent::setUp();

        $this->base_path = __DIR__.'/../sandbox';

        $this->storage_trident = new Trident();
        $this->storage_trident->setBasePath($this->base_path);

        $this->storage_disk = new Disk();
        $this->storage_disk->setBasePath($this->base_path);

        $this->storage_disk->deleteDirectoryAndFiles($this->base_path.'/');

        $this->storage_disk->makeDirectory($this->base_path.'/app/.');
        $this->storage_disk->makeDirectory($this->base_path.'/app/Providers/.');
        $this->storage_disk->makeDirectory($this->base_path.'/routes/.');

    }

    protected function getPackageProviders($app)
    {
        return [
            'j0hnys\Trident\TridentServiceProvider',
            'Krlove\EloquentModelGenerator\Provider\GeneratorServiceProvider'
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // // Setup default database to use sqlite :memory:
        // $app['config']->set('database.default', 'testbench');
        // $app['config']->set('database.connections.testbench', [
        //     'driver'   => 'sqlite',
        //     'database' => ':memory:',
        //     'prefix'   => '',
        // ]);

        
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => 'laravel_test',
            'username' => 'root',
            'password' => '',
        ]);

        $app['config']->set('filesystems.default', 'testbench');
        $app['config']->set('filesystems.disks.testbench', [
            'driver' => 'local',
            'root' => '127.0.0.1',
        ]);

        // $app->bind('path.public', function() {
        //     return __DIR__;
        // });
        
        // dd($app['config']);
    }


}
