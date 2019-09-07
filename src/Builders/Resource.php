<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\Response;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class Resource
{
    private $storage_disk;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
        $this->response_definition = new Response();
        $this->folder_structure = new FolderStructure();
    }
    
    /**
     * @param string $entity_name
     * @param boolean $is_collection
     * @param string $domain
     * @return void
     */
    public function generate(string $entity_name, string $function_name, bool $is_collection, string $domain, string $schema_path = '', bool $force = false): void
    {
        //Resource logic generation
        $resource_type = $is_collection ? 'ResourceCollection' : 'Resource';
        $this->folder_structure->checkPath('app/Trident/'.$domain.'/Schemas/Logic/'.$entity_name.'/Resources/*');
        $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.($entity_name).'/Resources/'.($entity_name).$function_name.$resource_type.'.php';
        
        if ($this->storage_disk->fileExists($struct_path) && $force === false) {
            throw new \Exception(($entity_name) . $function_name . $resource_type . ' already exists!');
        }


        $schema = [];
        if (!empty($schema_path)) {
            $schema = json_decode( $this->storage_disk->readFile( $schema_path ),true);
            $this->response_definition->check($schema);
        }


        $types = [];
        if (!empty($schema)) {
            foreach ($schema['data'] as $key => $data) {
                if (isset($data['resource'])) {
                    if ($data['resource']) {
                        $types []= [
                            'type' => '\''.$key.'\' => $this->'.$key.','
                        ];
                    }
                }
            }
        }


        $this->storage_disk->makeDirectory($struct_path);
        
        
        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Resources/'.$resource_type.'.stub');
        $stub = $this->mustache->render($stub, [
            'Entity_name' => ucfirst($entity_name),
            'render_types' => !empty($types) ? true : false,
            'function_name' => $function_name,
            'types' => $types,
        ]);
        

        $this->storage_disk->writeFile($struct_path, $stub);       

    }
    

}