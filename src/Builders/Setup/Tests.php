<?php

namespace j0hnys\Trident\Builders\Setup;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Tests
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
     * @return void
     */
    public function run()
    {
        $this->folder_structure->checkPath('tests/*');
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
