<?php

namespace j0hnys\Trident\Builders\Build;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Models
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

    public function generate($data = [], Command $command): void
    {
        $this->folder_structure->checkPath('database/generated_models/');
        $output_path = !empty($data['output_path']) ? $data['output_path'] : $this->storage_disk->getBasePath().'/database/generated_models/';

        $this->storage_disk->makeDirectory($output_path);
        
        $table_names = \DB::connection()->getDoctrineSchemaManager()->listTableNames();
        
        foreach ($table_names as $table_name) {
            $camel_case_table_name = str_replace(' ', '', ucwords(str_replace('_', ' ', $table_name)));
            $camel_case_table_name[0] = strtoupper($camel_case_table_name[0]);

            $command->call('krlove:generate:model', [
                'class-name' => $camel_case_table_name,
                '--output-path' => $output_path,
                '--table-name' => $table_name,
                '--namespace' => 'App\\Models'
            ]);
        }
    

    }
    

}