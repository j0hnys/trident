<?php

namespace j0hnys\Trident\Builders\Setup;

class Install
{
    
    /**
     * Install constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        
        $app_path = base_path().'/app';
        
        //
        //folder structure creation
        if (!file_exists($app_path.'/Trident')) {
            
            $source = __DIR__.'/../../../demo_structure/Trident';
            $destination = $app_path.'/Trident';
            
            $this->copyFolderStructure($source, $destination);
        }
        
        //
        //write trident service providers
        $source = __DIR__.'/../../../demo_structure/app/Providers';
        $destination = $app_path.'/Providers';
        
        $this->copyFiles($source, $destination);


        // $this->info('Controller created successfully.');

    }
    
     /**
     * Build directory structure from copying another.
     *
     * @param  string $path
     * @return string
     */
    protected function copyFolderStructure(string $source, string $destination)
    {

        mkdir($destination, 0755);
        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                // copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

    }

     /**
     * copy files.
     *
     * @param  string $path
     * @return string
     */
    protected function copyFiles(string $source, string $destination)
    {

        foreach (
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST) as $item
        ) {
            if ($item->isDir()) {
                //i don't want to copy any folder
                // mkdir($destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                //i don't want to copy any file now
                copy($item, $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }

    }
    
    /**
     * Get code and save to disk
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        //
    }

}