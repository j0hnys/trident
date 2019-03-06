<?php

namespace j0hnys\Trident\Builders;

class StrictType
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct(string $strict_type_name, string $function_name, string $td_entity_name)
    {
        
        if (strtolower($strict_type_name == 'struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/Business/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Business/Typed/LogicStruct.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
            
            file_put_contents($struct_path, $stub);

        } else if (strtolower($strict_type_name == 'collection_struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/Business/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'CollectionStruct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Business/Typed/LogicCollectionStruct.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
            
            file_put_contents($struct_path, $stub);

        } else if (strtolower($strict_type_name == 'map_struct')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/Business/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'MapStruct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Business/Typed/LogicMapStruct.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
            
            file_put_contents($struct_path, $stub);

        } if (strtolower($strict_type_name == 'struct_optional')) {
            //
            //struct logic generation
            $struct_path = base_path().'/app/Trident/Business/Schemas/Logic/'.ucfirst($td_entity_name).'/Typed/'.'Struct'.ucfirst($function_name).'.php';
            
            if (file_exists($struct_path)) {
                throw new \Exception('Struct'.ucfirst($function_name) . ' struct already exists!');
            }

            $this->makeDirectory($struct_path);

            $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Business/Typed/LogicStructOptional.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
            $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
            
            file_put_contents($struct_path, $stub);

        } else {
            throw new \Exception("unknown strict type", 1);
            
        }
        

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
     * Get code and save to disk
     * @return mixed
     * @throws \Exception
     */
    public function save()
    {
        //
    }

}