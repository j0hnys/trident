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

class WorkflowRestfulFunction
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
    public function refresh(string $name, string $function_name, array $options = []): void
    {
        $this->folder_structure->checkPath('tests/Trident/Functional/Resources/*');
        $workflow_restful_crud_logic_test_path = $this->storage_disk->getBasePath().'/tests/Trident/Functional/Resources/'.$name.'Test.php';

        if (!$this->storage_disk->fileExists($workflow_restful_crud_logic_test_path)) {
            throw new \Exception("workflow_restful_crud_test ".$name." does not exist!", 1);
        }
        if (!$options['functionality_schema_path']) {
            throw new \Exception("functionality_schema_path not set", 1);
        }
        if (!$options['request_schema_path']) {
            throw new \Exception("request_schema_path not set", 1);
        }
        if (!$options['response_schema_path']) {
            throw new \Exception("response_schema_path not set", 1);
        }

        
        $functionality_schema = json_decode( $this->storage_disk->readFile( $options['functionality_schema_path'] ),true);
        $this->functionality_definition->check($functionality_schema, 'schema');
        $this->functionality_definition->check($functionality_schema, 'endpoint');  
        
        $request_schema = json_decode( $this->storage_disk->readFile( $options['request_schema_path'] ),true);
        $this->request_definition->check($request_schema, 'schema');

        $response_schema = json_decode( $this->storage_disk->readFile( $options['response_schema_path'] ),true);
        $this->response_definition->check($response_schema, 'schema');

        
        //
        //restful function test generation
        $stub = '';
        if ($functionality_schema['endpoint']['type'] == 'create') {
            $stub = $this->storage_disk->readFile(__DIR__.'/../../../Stubs/tests/Trident/Functional/Resources/LogicCreate.stub');
        } else if ($functionality_schema['endpoint']['type'] == 'read') {
            $stub = $this->storage_disk->readFile(__DIR__.'/../../../Stubs/tests/Trident/Functional/Resources/LogicRead.stub');
        } else if ($functionality_schema['endpoint']['type'] == 'update') {
            $stub = $this->storage_disk->readFile(__DIR__.'/../../../Stubs/tests/Trident/Functional/Resources/LogicUpdate.stub');
        } else if ($functionality_schema['endpoint']['type'] == 'delete') {
            $stub = $this->storage_disk->readFile(__DIR__.'/../../../Stubs/tests/Trident/Functional/Resources/LogicDelete.stub');
        }

        $stub = str_replace('{{Td_entity}}', $name, $stub);
        $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        $stub = str_replace('{{function_name}}', $function_name, $stub);
        $stub = str_replace('{{endpoint_uri}}', $functionality_schema['endpoint']['uri'], $stub);
        $stub = str_replace('{{model_db_name}}', $functionality_schema['model']['db_name'], $stub);

        $request_properties = [];
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

        $response_properties = [];
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

        //relation ids
        $final_request_properties = [];
        $has_relation_ids = false;
        foreach ($request_properties as $request_property) {
            if (   $functionality_schema['endpoint']['type'] == 'create'
                || $functionality_schema['endpoint']['type'] == 'read'
                ) {
                if ($request_property['property_type'] != 'relation_id') {
                    $final_request_properties []= $request_property;
                } else {
                    $has_relation_ids = true;
                }
            }
        }

        $stub = $this->mustache->render($stub, [
            'request_properties' => $has_relation_ids ? $final_request_properties : $request_properties,
            'response_properties' => $response_properties,
            'has_relation_ids' => $has_relation_ids,
        ]);


        //
        //replace function in code with new
        $code = $this->storage_disk->readFile($workflow_restful_crud_logic_test_path);
        $result = $this->getClassFunction($code, 'test'.$function_name);
        $start_line = $result->objects->function_signatures->line_span->start - 1;
        $end_line = $result->objects->function_signatures->line_span->end - 1;

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
    public function getClassFunction(string $code, string $function_name): Object
    {
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
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result, $function_name){
            if ($node instanceof Node\Stmt\ClassMethod) {
                $tmp_function = (object)[
                    'flags' => $node->flags,  //dld public (1), protected (2), private (4), e.t.c.
                    'name' => $node->name,
                ];

                if ($node->name == $function_name) {
                    $tmp_function->line_span = (object)[
                        'start' => $node->getStartLine(),
                        'end' => $node->getEndLine(),
                    ];

                    $analysis_result->functions_signature = $tmp_function;
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