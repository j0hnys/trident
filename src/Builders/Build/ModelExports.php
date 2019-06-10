<?php

namespace j0hnys\Trident\Builders\Build;

use Illuminate\Console\Command;

class ModelExports
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($data = [], Command $command)
    {
        $input_path = $data['input_path'];
        $output_path = $data['output_path'];
        
        $files = $this->getFolderFileNames($input_path);

        foreach ($files as $file) {
            $file_name = str_replace('.php', '', $file);
            
            $command->call('trident:export:model', [
                'entity_name' => $file_name,
                '--output-path' => $output_path,
            ]);
        }
    

    }


    /**
     * return the names of all events from trigger folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getFolderFileNames(string $path)
    {
        $files = scandir($path);

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..' && !is_dir($path.$file)) {
                $filenames []= $file;
            }
        }

        return $filenames;
    }
    

}