<?php

namespace j0hnys\Trident\Builders\Setup;

use j0hnys\Trident\Base\Storage\Disk;

class Tests
{
    private $storage_disk;

    public function __construct()
    {
        $this->storage_disk = new Disk();
    }
    
    /**
     * @return void
     */
    public function run()
    {
        
        $tests_path = $this->storage_disk->getBasePath().'/tests';
        
        //
        //folder structure creation
        if (!$this->storage_disk->fileExists($tests_path.'/Trident')) {
            
            $source = __DIR__.'/../../../scaffold_structure/Trident';
            $destination = $tests_path.'/Trident';
            
            $this->storage_disk->copyFolderStructure($source, $destination);
        }
        

    }
    
    

}
