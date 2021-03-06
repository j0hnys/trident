<?php

namespace j0hnys\Trident\Builders\Build;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Migrations
{
    private $storage_disk;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->folder_structure = new FolderStructure();
    }

    /**
     * @param string $output_path
     * @param Command $command
     * @return void
     */
    public function generate(?string $output_path, Command $command): void
    {
        $this->folder_structure->checkPath('database/generated_migrations/');
        $output_path = !empty($output_path) ? $output_path : $this->storage_disk->getBasePath().'/database/generated_migrations';
        
        $this->storage_disk->makeDirectory($output_path.'/*');
        
        //new validation class
        $command->call('migrate:generate', [
            '-p' => $output_path,
        ]);
    }
    

}