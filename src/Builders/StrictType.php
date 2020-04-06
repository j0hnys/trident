<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Declarations;
use j0hnys\Trident\Base\Constants\Trident\Request;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class StrictType
{
    private $storage_disk;
    private $mustache;
    private $declarations;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
        $this->declarations = new Declarations();
        $this->request_definition = new Request();
        $this->folder_structure = new FolderStructure();
    }

    /**
     * @param string $strict_type_name
     * @param string $function_name
     * @param string $td_entity_name
     * @param string $domain
     * @return void
     */    
    public function generate(string $strict_type_name, string $function_name, string $td_entity_name, string $domain, string $schema_path = '', bool $force = false): void
    {

        if (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStruct.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['COLLECTION_STRUCT']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'CollectionStruct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicCollectionStruct.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['MAP_STRUCT']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'MapStruct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicMapStruct.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT_OPTIONAL']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptional.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT_OPTIONAL_WORKFLOW']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptionalWorkflow.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT_OPTIONAL_SHOW']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptionalShow.stub');

        } elseif (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT_OPTIONAL_WORKFLOW_FUNCTION']['name']) {
            //
            //struct logic generation
            $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/{{td_entity_name}}/Typed/*');
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path) && $force === false) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptionalWorkflowFunction.stub');

        } else {
            throw new \Exception("unknown strict type", 1);            
        }


        $schema = [];
        if (!empty($schema_path)) {
            $schema = \json_decode($this->storage_disk->readFile( $schema_path ),true);
            $this->request_definition->check($schema);
        }


        $types = [];
        if (!empty($schema)) {
            foreach ($schema['data'] as $key => $data) {
                if (isset($data['type'])) {
                    $types []= [
                        'type' => '\''.$key.'\' => '.$data['type'].','
                    ];
                }
            }
        }


        $stub = $this->mustache->render($stub, [
            'td_entity' => lcfirst($td_entity_name),
            'Td_entity' => ucfirst($td_entity_name),
            'function_name' => lcfirst($function_name),
            'Function_name' => ucfirst($function_name),
            'types' => $types,
        ]);
        


        $this->storage_disk->writeFile($struct_path, $stub);
    }
        

}