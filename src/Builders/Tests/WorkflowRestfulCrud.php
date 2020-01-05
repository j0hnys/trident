<?php

namespace j0hnys\Trident\Builders\Tests;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Constants\Trident\Functionality;
use j0hnys\Trident\Base\Constants\Trident\Request;
use j0hnys\Trident\Base\Constants\Trident\Response;

class WorkflowRestfulCrud
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
        $this->folder_structure = new FolderStructure();
        $this->functionality_definition = new Functionality();
        $this->request_definition = new Request();
        $this->response_definition = new Response();
    }

    /**
     * @param string $name
     * @return void
     */
    public function generate(string $name, array $options = []): void
    {
        $model_db_name = '';
        $functionality_schema = [];
        $request_schema = [];
        $response_schema = [];

        if ($options['functionality_schema_path']) {
            if (!empty($options['functionality_schema_path'])) {
                $functionality_schema = json_decode( $this->storage_disk->readFile( $options['functionality_schema_path'] ),true);
                $this->functionality_definition->check($functionality_schema, 'schema');
            }
            $model_db_name = $functionality_schema['model']['db_name'];
        }
        if ($options['request_schema_path']) {
            if (!empty($options['request_schema_path'])) {
                $request_schema = json_decode( $this->storage_disk->readFile( $options['request_schema_path'] ),true);
                $this->request_definition->check($request_schema, 'schema');
            }
        }
        if ($options['response_schema_path']) {
            if (!empty($options['response_schema_path'])) {
                $response_schema = json_decode( $this->storage_disk->readFile( $options['response_schema_path'] ),true);
                $this->response_definition->check($response_schema, 'schema');
            }
        }


        //
        //
        //
        $request_schema = [
            "type" => 'json',
            "data" => [
                "id" => [
                    'property' => 'auto_id',
                    'type' => 'T::nullable(T::integer())',
                    'validation' => [
                        'rule' => 'required | integer',
                        'message' => 'field 1 is required',
                    ],
                    'fillable' => true,
                ],
                "user_id" => [
                    'property' => 'auto_id',
                    'type' => 'T::nullable(T::integer())',
                    'validation' => [
                        'rule' => 'integer',
                        'message' => 'field 1 is required',
                    ],
                    'fillable' => true,
                ],
                "name" => [
                    'type' => 'T::nullable(T::string())',
                    'validation' => [
                        'rule' => 'required | string',
                        'message' => 'field 1 is required',
                    ],
                    'fillable' => true,
                ],
                "root_folder" => [
                    'type' => 'T::nullable(T::string())',
                    'validation' => [
                        'rule' => 'required | string',
                        'message' => 'field 1 is required',
                    ],
                    'fillable' => true,
                ],
                "relative_schemas_folder" => [
                    'type' => 'T::nullable(T::string())',
                    'validation' => [
                        'rule' => 'required | string',
                        'message' => 'field 1 is required',
                    ],
                    'fillable' => true,
                ],
            ],
        ];
        //
        //
        //

        
        //
        //restful crud test generation
        $this->folder_structure->checkPath('tests/Trident/Functional/Resource/*');
        $workflow_restful_crud_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Functional/Resource/'.$name.'Test.php';

        if (!$this->storage_disk->fileExists($workflow_restful_crud_logic_test_path)) {
            $this->storage_disk->makeDirectory($workflow_restful_crud_logic_test_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Functional/Resources/Logic.stub');

            $stub = str_replace('{{Td_entity}}', $name, $stub);
            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $properties = [];
            if (!empty($request_schema)) {
                foreach ($request_schema['data'] as $key => $data) {
                    if (!isset($data['property']) || (isset($data['property']) && $data['property'] != 'auto_id')) {
                        if (strpos($data['type'], 'string') !== false) {
                            $properties []= [
                                'property' => '\''.$key.'\' => \''.$data['type'].'\','
                            ];
                        } else if (strpos($data['type'], 'bool') !== false) {
                            $properties []= [
                                'property' => '\''.$key.'\' => '.($data['type'] ? 'true' : 'false').','
                            ];
                        } else {
                            $properties []= [
                                'property' => '\''.$key.'\' => '.'2'.','
                            ];
                        }
                    }
                }
            }
            $stub = $this->mustache->render($stub, [
                'properties' => $properties,
            ]);
            
            $this->storage_disk->writeFile($workflow_restful_crud_logic_test_path, $stub);
        }        


    }
    


}