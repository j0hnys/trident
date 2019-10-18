<?php

namespace j0hnys\Trident\Builders\Tests;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Workflow
{
    private $storage_disk;
    private $mustache;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
        $this->folder_structure = new FolderStructure();
    }

    /**
     * @param string $name
     * @return void
     */
    public function generate(string $name): void
    {
        
        //
        //workflow logic test generation
        $this->folder_structure->checkPath('tests/Trident/Workflows/Logic/*');
        $workflow_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Workflows/Logic/'.ucfirst($name).'Test.php';
        
        $class_name = '\\App\\Trident\\Workflows\\Logic\\'.ucfirst($name);
        $class_methods = \get_class_methods( $class_name );
        $class_methods = array_map(function($element){
            return [
                'method' => ($element),
            ];
        },array_values(array_filter($class_methods,function($element){
            return $element != '__construct' ? true : false;
        })));

        if (!$this->storage_disk->fileExists($workflow_logic_test_path)) {
            $this->storage_disk->makeDirectory($workflow_logic_test_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Workflows/Logic/Logic.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            $stub = $this->mustache->render($stub, [
                'methods' => $class_methods,
            ]);
            
            $this->storage_disk->writeFile($workflow_logic_test_path, $stub);
        }

        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //now we build the business logic part of this workflow
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        //
        //workflow logic test generation
        $this->folder_structure->checkPath('tests/Trident/Business/Logic/*');
        $business_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Business/Logic/'.ucfirst($name).'Test.php';
        
        $class_name = '\\App\\Trident\\Business\\Logic\\'.ucfirst($name);
        $class_methods = \get_class_methods( $class_name );
        $class_methods = array_map(function($element){
            return [
                'method' => ($element),
            ];
        },array_values(array_filter($class_methods,function($element){
            return $element != '__construct' ? true : false;
        })));

        if (!$this->storage_disk->fileExists($business_logic_test_path)) {
            $this->storage_disk->makeDirectory($business_logic_test_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Business/Logic/Logic.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            $stub = $this->mustache->render($stub, [
                'methods' => $class_methods,
            ]);
            
            $this->storage_disk->writeFile($business_logic_test_path, $stub);
        }


    }
    


}