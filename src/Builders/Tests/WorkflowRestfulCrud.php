<?php

namespace j0hnys\Trident\Builders\Tests;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Constants\Trident\Functionality;
use j0hnys\Trident\Base\Constants\Trident\Tests\Request;
use j0hnys\Trident\Base\Constants\Trident\Tests\Response;

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
        //restful crud test generation
        $this->folder_structure->checkPath('tests/Trident/Functional/Resource/*');
        $workflow_restful_crud_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Functional/Resource/'.$name.'Test.php';

        if (!$this->storage_disk->fileExists($workflow_restful_crud_logic_test_path)) {
            $this->storage_disk->makeDirectory($workflow_restful_crud_logic_test_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/tests/Trident/Functional/Resources/Logic.stub');

            $stub = str_replace('{{Td_entity}}', $name, $stub);
            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{model_db_name}}', $model_db_name, $stub);
            $request_properties = [];
            if (!empty($request_schema)) {
                foreach ($request_schema['data'] as $key => $data) {
                    if ($data['property_type'] != 'auto_id') {
                        if (is_string($data['value'])) {
                            $request_properties []= [
                                'property' => '\''.$key.'\' => \''.$data['value'].'\','
                            ];
                        } else if (is_bool($data['value'])) {
                            $request_properties []= [
                                'property' => '\''.$key.'\' => '.($data['value'] ? 'true' : 'false').','
                            ];
                        } else {
                            $request_properties []= [
                                'property' => '\''.$key.'\' => '.$data['value'].','
                            ];
                        }
                    }
                }
            }

            $response_properties = [];
            if (!empty($request_schema)) {
                foreach ($request_schema['data'] as $key => $data) {
                    if ($data['property_type'] != 'auto_id') {
                        if (is_string($data['value'])) {
                            $response_properties []= [
                                'property' => '\''.$key.'\' => \''.$data['value'].'\','
                            ];
                        } else if (is_bool($data['value'])) {
                            $response_properties []= [
                                'property' => '\''.$key.'\' => '.($data['value'] ? 'true' : 'false').','
                            ];
                        } else {
                            $response_properties []= [
                                'property' => '\''.$key.'\' => '.$data['value'].','
                            ];
                        }
                    }
                }
            }
            $stub = $this->mustache->render($stub, [
                'request_properties' => $request_properties,
                'response_properties' => $response_properties,
            ]);
            
            $this->storage_disk->writeFile($workflow_restful_crud_logic_test_path, $stub);
        }        


    }
    


}