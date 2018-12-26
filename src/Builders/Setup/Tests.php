<?php

namespace j0hnys\Trident\Builders\Setup;

class Tests
{
    
    /**
     * Tests constructor.
     * 
     * @return void
     */
    public function __construct()
    {
        
        $tests_path = base_path().'/tests';
        $mustache = new \Mustache_Engine;

        //
        //folder structure creation
        if (!file_exists($tests_path.'/Trident')) {
            
            $source = __DIR__.'/../../../scaffold_structure/Trident';
            $destination = $tests_path.'/Trident';
            
            $this->copyFolderStructure($source, $destination);
        }
        

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
    

}
