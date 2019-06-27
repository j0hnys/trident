<?php

namespace j0hnys\Trident\Builders\Tests;

use j0hnys\Trident\Base\Storage\Disk;

class Workflow
{
    private $storage_disk;
    private $mustache;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->mustache = new \Mustache_Engine;
    }

    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function generate($name = 'TEST')
    {
        
        //
        //workflow logic test generation
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