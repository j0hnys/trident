<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;

class Resources
{
    private $storage_disk;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
    }
    
    /**
     * @param string $entity_name
     * @param boolean $is_collection
     * @param string $domain
     * @return void
     */
    public function generate(string $entity_name, bool $is_collection, string $domain): void
    {
        //Resource logic generation
        $resource_type = $is_collection ? 'ResourceCollection' : 'Resource';
        $struct_path = $this->storage_disk->getBasePath().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($entity_name).'/Resources/'.ucfirst($entity_name).$resource_type.'.php';
        
        if ($this->storage_disk->fileExists($struct_path)) {
            throw new \Exception(ucfirst($entity_name) . $resource_type . ' already exists!');
        }


        $schema = [];
        $configuration = config('trident');
        if (!empty($configuration)) {
            if (isset($configuration['solution']['schemas']['folder'])) {
                $tmp_schemas = $this->storage_disk->getFolderFiles($configuration['solution']['schemas']['folder']);

                foreach ($tmp_schemas as $tmp_schema) {
                    if ($tmp_schema == $entity_name.'.json') {
                        $schema = json_decode( $this->storage_disk->readFile( $configuration['solution']['schemas']['folder'].'/'.$tmp_schema ),true);
                    }
                }
            }
        }


        $types = [];
        if (!empty($schema)) {
            foreach ($schema as $key => $data) {
                if (isset($data['output']['resource'])) {
                    if ($data['output']['resource']) {
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
            'types' => $types,
        ]);
        

        $this->storage_disk->writeFile($struct_path, $stub);       

    }
    

}