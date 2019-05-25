<?php

namespace j0hnys\Trident\Builders;

class Resources
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $entity_name, bool $is_collection, string $domain)
    {
        //Resource logic generation
        $resource_type = $is_collection?'ResourceCollection':'Resource';
        $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($entity_name).'/Resources/'.ucfirst($entity_name).$resource_type.'.php';
        
        if (file_exists($struct_path)) {
            throw new \Exception(ucfirst($entity_name) . $resource_type . ' already exists!');
        }


        $mustache = new \Mustache_Engine;


        $schema = [];
        $configuration = config('trident');
        if (!empty($configuration)) {
            if (isset($configuration['solution']['schemas']['folder'])) {
                $tmp_schemas = $this->getFolderFiles($configuration['solution']['schemas']['folder']);

                foreach ($tmp_schemas as $tmp_schema) {
                    if ($tmp_schema == $entity_name.'.json') {
                        $schema = json_decode(file_get_contents( $configuration['solution']['schemas']['folder'].'/'.$tmp_schema ),true);
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


        // dd([
        //     '$types' => $types,
        // ]);


        $this->makeDirectory($struct_path);
        
        
        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Resources/'.$resource_type.'.stub');
        $stub = $mustache->render($stub, [
            'Entity_name' => ucfirst($entity_name),
            'types' => $types,
        ]);
        
        file_put_contents($struct_path, $stub);
        

    }
    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory($path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }
    
    /**
     * return the names of all events from subscriber folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getFolderFiles($absolute_folder_path)
    {
        $files = scandir($absolute_folder_path);

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

}