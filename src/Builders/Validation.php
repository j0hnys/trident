<?php

namespace j0hnys\Trident\Builders;

use function GuzzleHttp\json_decode;

class Validation
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($td_entity_name, $function_name)
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);

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


        $rules = [];
        $messages = [];
        if (!empty($schema)) {
            foreach ($schema as $key => $data) {
                if (isset($data['input']['validation']['rule'])) {
                    $rules []= [
                        'rule' => '\''.$key.'\' => \''.$data['input']['validation']['rule'].'\','
                    ];
                }
                if (isset($data['input']['validation']['message'])) {
                    $messages []= [
                        'message' => '\''.$key.'\' => \''.$data['input']['validation']['message'].'\','
                    ];
                }
            }
        }


        //
        //workflow logic generation
        $workflow_validation_path = base_path().'/app/Trident/Workflows/Validations/'.$name.'Request.php';
        
        if (file_exists($workflow_validation_path)) {
            throw new \Exception(ucfirst($name) . ' validation already exists!');
        }

        $this->makeDirectory($workflow_validation_path);


        $stub = file_get_contents(__DIR__.'/../../src/Stubs/Trident/Workflows/LogicRequestValidation.stub');
        $stub = $mustache->render($stub, [
            'td_entity' => lcfirst($name),
            'Td_entity' => ucfirst($name),
            'rules' => $rules,
            'messages' => $messages,
        ]);
        
        file_put_contents($workflow_validation_path, $stub);
        

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