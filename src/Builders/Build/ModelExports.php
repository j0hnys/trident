<?php

namespace j0hnys\Trident\Builders\Build;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;

class ModelExports
{
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
    }

    public function generate($data = [], Command $command): void
    {
        $input_path = !empty($data['output_path']) ? $data['output_path'] : $this->storage_disk->getBasePath().'/app/Models/';
        $output_path = !empty($data['output_path']) ? $data['output_path'] : $this->storage_disk->getBasePath().'/database/generated_model_exports/';
            
        $this->storage_disk->makeDirectory($output_path);
        
        $files = $this->storage_disk->getFolderFileNames($input_path);
        
        foreach ($files as $file) {
            $file_name = str_replace('.php', '', $file);
            

            $command->call('trident:export:model', [
                'entity_name' => $file_name,
                '--output-path' => $output_path,
            ]);
        }
    }
    

}