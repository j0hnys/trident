<?php

namespace j0hnys\Trident\Builders;

use function GuzzleHttp\json_decode;

use j0hnys\Trident\Base\Storage\Disk;

class Validation
{
    private $storage_disk;
    private $mustache;

    public function __construct()
    {
        $this->storage_disk = new Disk();
        $this->mustache = new \Mustache_Engine;
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name): void
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);


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
        $workflow_validation_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Validations/'.$name.'Request.php';
        
        if ($this->storage_disk->fileExists($workflow_validation_path)) {
            throw new \Exception(ucfirst($name) . ' validation already exists!');
        }

        $this->storage_disk->makeDirectory($workflow_validation_path);


        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/Workflows/LogicRequestValidation.stub');
        $stub = $this->mustache->render($stub, [
            'td_entity' => lcfirst($name),
            'Td_entity' => ucfirst($name),
            'rules' => $rules,
            'messages' => $messages,
        ]);
        
        $this->storage_disk->writeFile($workflow_validation_path, $stub);
        

    }


    
}