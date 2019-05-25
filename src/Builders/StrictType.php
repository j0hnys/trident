<?php

namespace j0hnys\Trident\Builders;

class StrictType
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $strict_type_name, string $function_name, string $td_entity_name, string $domain)
    {
        
        if (strtolower($strict_type_name == 'struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStruct.stub');

        } else if (strtolower($strict_type_name == 'collection_struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'CollectionStruct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicCollectionStruct.stub');

        } else if (strtolower($strict_type_name == 'map_struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'MapStruct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicMapStruct.stub');

        } if (strtolower($strict_type_name == 'struct_optional')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/'.$domain.'/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name). ucfirst($td_entity_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ucfirst($td_entity_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/'.$domain.'/Typed/LogicStructOptional.stub');

        } else {
            throw new \Exception("unknown strict type", 1);
            
        }



        $mustache = new \Mustache_Engine;


        $schema = [];
        $configuration = config('trident');
        if (!empty($configuration)) {
            if (isset($configuration['solution']['schemas']['folder'])) {
                $tmp_schemas = $this->getFolderFiles($configuration['solution']['schemas']['folder']);

                foreach ($tmp_schemas as $tmp_schema) {
                    if ($tmp_schema == $td_entity_name.'.json') {
                        $schema = json_decode(file_get_contents( $configuration['solution']['schemas']['folder'].'/'.$tmp_schema ),true);
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


        $stub = $mustache->render($stub, [
            'td_entity' => lcfirst($td_entity_name),
            'Td_entity' => ucfirst($td_entity_name),
            'function_name' => lcfirst($function_name),
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