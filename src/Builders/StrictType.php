<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Declarations;

class StrictType
{
    private $storage_disk;
    private $mustache;
    private $declarations;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->mustache = new \Mustache_Engine;
        $this->declarations = new Declarations();
    }

    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function generate(string $strict_type_name, string $function_name, string $td_entity_name, string $domain)
    {

        if (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT']['name']) {
            //
            //struct logic generation
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStruct.stub');

        } else if (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['COLLECTION_STRUCT']['name']) {
            //
            //struct logic generation
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'CollectionStruct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicCollectionStruct.stub');

        } else if (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['MAP_STRUCT']['name']) {
            //
            //struct logic generation
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'MapStruct'.ucfirst($function_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicMapStruct.stub');

        } else if (strtolower($strict_type_name) == $this->declarations::STRICT_TYPES['STRUCT_OPTIONAL']['name']) {
            //
            //struct logic generation
            $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if ($this->storage_disk->fileExists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->storage_disk->makeDirectory($struct_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptional.stub');

        } else {
            throw new \Exception("unknown strict type", 1);            
        }


        $schema = [];
        $configuration = config('trident');
        if (!empty($configuration)) {
            if (isset($configuration['solution']['schemas']['folder'])) {
                $tmp_schemas = $this->storage_disk->getFolderFiles($configuration['solution']['schemas']['folder']);

                foreach ($tmp_schemas as $tmp_schema) {
                    if ($tmp_schema == $td_entity_name.'.json') {
                        $schema = json_decode($this->storage_disk->readFile( $configuration['solution']['schemas']['folder'].'/'.$tmp_schema ),true);
                    }
                }
            }
        }


        $types = [];
        if (!empty($schema)) {
            foreach ($schema as $key => $data) {
                if (isset($data['input']['type'])) {
                    $types []= [
                        'type' => '\''.$key.'\' => '.$data['input']['type'].','
                    ];
                }
            }
        }


        $stub = $this->mustache->render($stub, [
            'td_entity' => lcfirst($td_entity_name),
            'Td_entity' => ucfirst($td_entity_name),
            'function_name' => lcfirst($function_name),
            'types' => $types,
        ]);
        


        $this->storage_disk->writeFile($struct_path, $stub);
    }
        

}