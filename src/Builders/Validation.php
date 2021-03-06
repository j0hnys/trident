<?php

namespace j0hnys\Trident\Builders;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\Request;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Utilities\WordCaseConverter;

class Validation
{
    private $storage_disk;
    private $mustache;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->mustache = new \Mustache_Engine;
        $this->request_definition = new Request();
        $this->folder_structure = new FolderStructure();
        $this->word_case_converter = new WordCaseConverter();
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name, string $schema_path = '', bool $force = false): void
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        $schema = [];
        if (!empty($schema_path)) {
            $schema = \json_decode($this->storage_disk->readFile( $schema_path ),true);
            $this->request_definition->check($schema);
        }


        $rules = [];
        $messages = [];
        if (!empty($schema)) {
            foreach ($schema['data'] as $key => $data) {
                if (isset($data['validation']['rule'])) {
                    $rules []= [
                        'rule' => '\''.$key.'\' => \''.$data['validation']['rule'].'\','
                    ];
                }
                if (isset($data['validation']['message'])) {
                    $messages []= [
                        'message' => '\''.$key.'\' => \''.$data['validation']['message'].'\','
                    ];
                }
            }
        }


        //
        //workflow logic generation
        $this->folder_structure->checkPath('tests/Trident/Workflows/Validations/*');
        $workflow_validation_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Validations/'.$name.'Request.php';
        
        if ($this->storage_disk->fileExists($workflow_validation_path) && $force === false) {
            throw new \Exception(ucfirst($name) . ' validation already exists!');
        }

        $this->storage_disk->makeDirectory($workflow_validation_path);


        $stub = $this->storage_disk->readFile(__DIR__.'/../../src/Stubs/Trident/Workflows/LogicRequestValidation.stub');
        $stub = $this->mustache->render($stub, [
            'td_entity' => $this->word_case_converter->camelCaseToSnakeCase($name),
            'Td_entity' => ucfirst($name),
            'id_request_parameter' => $this->word_case_converter->camelCaseToSnakeCase($td_entity_name),
            'rules' => $rules,
            'messages' => $messages,
        ]);
        
        $this->storage_disk->writeFile($workflow_validation_path, $stub);
        

    }


    
}