<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Declarations;

class Exception
{
    private $storage_disk;
    private $declarations;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->declarations = new Declarations();
    }

    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function generate($td_entity_type, $td_entity_name)
    {
        
        $td_entity_name = ucfirst($td_entity_name);
        $td_entity_type = strtolower($td_entity_type);

        $type = '';
        if ($td_entity_type == $this->declarations::ENTITIES['WORKFLOW']['name']) {
            $type = 'Workflows';
        } else if ($td_entity_type == $this->declarations::ENTITIES['BUSINESS']['name']) {
            $type = 'Business';
        } else {
            throw new \Exception('entity type '.$type.' does not exists!');
        }

        //
        //workflow logic generation
        $workflow_exception_path = $this->storage_disk->getBasePath().'/app/Trident/'.$type.'/Exceptions/'.$td_entity_name.'Exception.php';
        
        if (file_exists($workflow_exception_path)) {
            throw new \Exception(ucfirst($td_entity_name) . ' exception already exists!');
        }

        $this->storage_disk->makeDirectory($workflow_exception_path);

        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$type.'/LogicException.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        
        $this->storage_disk->writeFile($workflow_exception_path, $stub);
    }
    

}