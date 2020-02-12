<?php

namespace j0hnys\Trident\Builders\Refresh\Tests;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};
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
    public function refresh(string $name, array $options = []): void
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
            throw new \Exception("workflow_restful_crud_test ".$name." does not exist!", 1);
        }

        $stub = $this->storage_disk->readFile(__DIR__.'/../../../Stubs/tests/Trident/Functional/Resources/LogicResource.stub');

        $stub = str_replace('{{Td_entity}}', $name, $stub);
        $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        $stub = str_replace('{{model_db_name}}', $model_db_name, $stub);
        $request_properties = [];
        if (!empty($request_schema)) {
            foreach ($request_schema['data'] as $key => $data) {
                if ($data['property_type'] != 'auto_id') {
                    if (is_string($data['value'])) {
                        $request_properties []= [
                            'property' => '\''.$key.'\' => \''.$data['value'].'\',',
                            'property_type' => $data['property_type'],
                        ];
                    } else if (is_bool($data['value'])) {
                        $request_properties []= [
                            'property' => '\''.$key.'\' => '.($data['value'] ? 'true' : 'false').',',
                            'property_type' => $data['property_type'],
                        ];
                    } else {
                        $request_properties []= [
                            'property' => '\''.$key.'\' => '.$data['value'].',',
                            'property_type' => $data['property_type'],
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
                            'property' => '\''.$key.'\' => \''.$data['value'].'\',',
                            'property_type' => $data['property_type'],
                        ];
                    } else if (is_bool($data['value'])) {
                        $response_properties []= [
                            'property' => '\''.$key.'\' => '.($data['value'] ? 'true' : 'false').',',
                            'property_type' => $data['property_type'],
                        ];
                    } else {
                        $response_properties []= [
                            'property' => '\''.$key.'\' => '.$data['value'].',',
                            'property_type' => $data['property_type'],
                        ];
                    }
                }
            }
        }

        //relation ids
        $index_request_properties = [];
        $show_request_properties = [];
        $has_relation_ids = false;
        foreach ($request_properties as $request_property) {
            if ($request_property['property_type'] != 'relation_id') {
                $index_request_properties []= $request_property;
                $show_request_properties []= $request_property;
            } else {
                $has_relation_ids = true;
            }
        }

        $stub = $this->mustache->render($stub, [
            'index_request_properties' => $index_request_properties,
            'show_request_properties' => $show_request_properties,
            'store_request_properties' => $request_properties,
            'update_request_properties' => $request_properties,
            'destroy_request_properties' => $request_properties,
            
            'index_response_properties' => $response_properties,
            'show_response_properties' => $response_properties,

            'has_relation_ids' => $has_relation_ids,
        ]);
        

        //
        //replace functions in code with new
        $code = $this->storage_disk->readFile($workflow_restful_crud_logic_test_path);
        $result = $this->getClassResourceFunctions($code);
        $start_line = 0;
        $end_line = 0;
        foreach ($result->objects->function_signatures as $function_signature) {
            if ($start_line == 0 || $start_line > $function_signature->line_span->start) {
                $start_line = $function_signature->line_span->start - 1;
            }
            if ($end_line < $function_signature->line_span->end) {
                $end_line = $function_signature->line_span->end - 1;
            }
        }

        $lines = $this->storage_disk->readFileArray($workflow_restful_crud_logic_test_path); 
        
        for ($i=$start_line; $i<=$end_line; $i++) { 
            unset($lines[$i]);
        }
        $lines = array_values($lines);

        array_splice($lines, $start_line, 0, $stub);


        //
        //write new test function
        $this->storage_disk->writeFileArray($workflow_restful_crud_logic_test_path, $lines);
    }

    /**
     * @param string $code
     * @return Object
     */
    public function getClassResourceFunctions(string $code): Object
    {
        $function_names = [
            'testIndex', 'testCreate', 'testStore', 'testShow', 'testEdit', 'testUpdate', 'testDestroy'
        ];

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return (object)[];
        }

        $analysis_result = (object)[
            'functions_signature' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result, $function_names){
            if ($node instanceof Node\Stmt\ClassMethod) {
                $tmp_function = (object)[
                    'flags' => $node->flags,  //dld public (1), protected (2), private (4), e.t.c.
                    'name' => $node->name,
                ];

                if (in_array($node->name, $function_names)) {
                    $tmp_function->line_span = (object)[
                        'start' => $node->getStartLine(),
                        'end' => $node->getEndLine(),
                    ];

                    $analysis_result->functions_signature []= $tmp_function;
                }                
            }

        });        

        return (object)[
            'objects' => (object)[
                'function_signatures' => $analysis_result->functions_signature,
            ]
        ];
    }
    


}