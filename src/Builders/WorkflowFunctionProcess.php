<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;
use J0hnys\TridentWorkflow\WorkflowRegistry;
use Symfony\Component\Workflow\Definition;
use j0hnys\Trident\Builders\Refresh\ClassInterface;

use PhpParser\{Node, NodeFinder};
use PhpParser\ParserFactory;

class WorkflowFunctionProcess
{
    private $mustache;
    private $storage_disk;
    private $storage_trident;
    private $crud_builder;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->crud_builder = new Builders\Crud\CrudWorkflowBuilder();
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generate(string $td_entity_name, string $type, string $function_name, string $schema_path, Command $command): void
    {

        $this->generateLogicFunction($td_entity_name, $type, $function_name, $schema_path);

        // $this->generateOther($td_entity_name, $function_name, $command);

    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generateLogicFunction(string $td_entity_name, string $type, string $function_name, string $schema_path): void
    {
        $name = ucfirst($td_entity_name).ucfirst($function_name);
        

        // $schema = [];
        // if (!empty($schema_path)) {
        //     $schema = json_decode( $this->storage_disk->readFile( $schema_path ), true);
        // }


        $tmp_data = [
            'type' => 'cascade',

            'workflow' => [ //<-- this is going to be generated from schema file
                'places'        => ['draft', 'review', 'rejected', 'published'],
                'supports' => [DefaultMarking::class],
                'transitions'   => [
                    'to_review' => [
                        'from' => 'draft',
                        'to'   => 'review'
                    ],
                    'publish' => [
                        'from' => 'review',
                        'to'   => 'published'
                    ],
                    'reject_published' => [
                        'from' => 'published',
                        'to'   => 'rejected'
                    ]
                ],
            ],

            'initial_state' => 'draft',
            'transition_listeners' => [
                'to_review' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_1',
                'publish' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_2',
                'reject_published' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_3'
            ],

            'transition_listeners_filepaths' => [
                'to_review' => 'C:\xampp\htdocs\laravel_test\app\Trident\Workflows\Processes\DemoProcessCascadeProcess.php',
                'publish' => 'C:\xampp\htdocs\laravel_test\app\Trident\Workflows\Processes\DemoProcessCascadeProcess.php',
                'reject_published' => 'C:\xampp\htdocs\laravel_test\app\Trident\Workflows\Processes\DemoProcessCascadeProcess.php'
            ],
        ];


        $workflow_configuration = app()->make('J0hnys\TridentWorkflow\PackageProviders\Configuration');
        $workflow_configuration->setWorkflow('$workflow_logic_function_name', $tmp_data['workflow']);

        $workflow_registry = new WorkflowRegistry('$workflow_logic_function_name');

        $default_marking = new DefaultMarking();
        $default_marking->td_entity_name = '$td_entity_namespace';
        $default_marking->td_entity_workflow_function_name = '$workflow_logic_function_name';
        $default_marking->marking = $tmp_data['initial_state'];

        $workflow = $workflow_registry->get($default_marking);

        $edges = $this->findEdges($workflow->getDefinition());

        $process_execution_direction = [];
        foreach ($edges as $edge) {
            if ($edge['direction'] == 'from') {
                $process_execution_direction []= $edge['to'];     
            }
        }


        //
        // get callback function structures for each step of $process_execution_direction and create schema
        $process_function_execution_workflow = [];  //<--this table is directed, index SHOWS the execution workflow of functions!!
        foreach ($process_execution_direction as $process_name) {
            
            // analyw tn callback function gia na parw t dependencies
            $process = $tmp_data['transition_listeners'][ $process_name ];
            $process_filepath = $tmp_data['transition_listeners_filepaths'][ $process_name ];
    
            $callback = explode('@',$process);
            $callback_class_namespace = $callback[0];
            $callback_fuction = $callback[1];
    
            $code = $this->storage_disk->readFile( $process_filepath );
            $process_code_analysis_result = $this->getClassFunctionSignature($code, $callback_fuction);            

            $process_function_execution_workflow []= $process_code_analysis_result;
        }


        //vvv PROSOXH!!!!
        $function_name = 'index';
        //^^^

        //update workflow function

        $td_entity_name = 'DemoProcess';
        // $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.$td_entity_name.'.php' );
        // $code = $this->storage_disk->readFileArray( $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.$td_entity_name.'.php' );
        // $workflow_analysis_result = $class_interface->getClassFunctionSignatures($code);

        $this->updateWorkflowFunction(
            $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.$td_entity_name.'.php', 
            $function_name, 
            $process_function_execution_workflow
        );

        // dump([
        //     '$process_execution_direction' => $process_execution_direction,
        //     '$process_function_execution_workflow' => $process_function_execution_workflow,
        //     // '$workflow_analysis_result' => $workflow_analysis_result->strings,
        // ]);

        // //
        // //workflowLogic function generation
        // $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'.php';
        
        // $lines = $this->storage_disk->readFileArray($workflow_logic_path); 
        // $last = sizeof($lines) - 1; 
        // unset($lines[$last]); 

        // $this->storage_disk->writeFileArray($workflow_logic_path, $lines); 

        // $stub = $this->storage_disk->readFile(__DIR__.'/../Stubs/Trident/Workflows/LogicFunction.stub');

        // $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        // $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        // $stub = str_replace('{{function_name}}', ucfirst($function_name), $stub);
        
        // $this->storage_disk->writeFile($workflow_logic_path, $stub, [
        //     'append_file' => true
        // ]);
        
    }


    protected function findEdges(Definition $definition)
    {
        $workflowMetadata = $definition->getMetadataStore();

        $dotEdges = [];

        foreach ($definition->getTransitions() as $i => $transition) {
            $transitionName = $workflowMetadata->getMetadata('label', $transition) ?? $transition->getName();

            foreach ($transition->getFroms() as $from) {
                $dotEdges[] = [
                    'from' => $from,
                    'to' => $transitionName,
                    'direction' => 'from',
                    'transition_number' => $i,
                ];
            }
            foreach ($transition->getTos() as $to) {
                $dotEdges[] = [
                    'from' => $transitionName,
                    'to' => $to,
                    'direction' => 'to',
                    'transition_number' => $i,
                ];
            }
        }

        return $dotEdges;
    }


    public function updateWorkflowFunction(string $code_filepath, string $function_name, array $process_steps)
    {
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        //
        // PROSOXH!! ta edits t kanw apo katw pros t panw to $code_line_array GIA NA MHN XASW TN ANTISTOIXIA 
        // METAKSY index <-> pragmatikhs grammhs (-1) WSTE NA KANW MIA PERASIA SE OLO TO $code_line_array!!
        //
        //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        $code = $this->storage_disk->readFile( $code_filepath );
        $code_line_array = $this->storage_disk->readFileArray( $code_filepath );       

        $workflow_structure = $this->getClassStructure($code);        
        $workflow_function_structure = $this->getClassStructure($code, $function_name);   
        $workflow_function_arguments = $workflow_function_structure->objects->functions_data[0]->function_signature->arguments;     
        $workflow_function_arguments_string = implode(', ', array_map(function($element){
            return '$'.$element->name;
        }, $workflow_function_arguments));

        dump([
            // '$workflow_structure' => $workflow_structure->strings,
            // '$workflow_function_structure' => $workflow_function_structure->objects->functions_data[0]->function_signature->arguments,
            // '$workflow_function_arguments_string' => $workflow_function_arguments_string,
            '$code_line_array' => $code_line_array,
        ]);


        //prwta vriskw se poies grammes einai t content toy body
        $function_body = (object)[
            'content' => (object)[
                'lines' => (object)[
                    'from' => 0,
                    'to' => 0,
                ]
            ]
        ]; 
        foreach ($workflow_structure->objects->function_signatures as $i => $function_signature) {

            if ($function_signature->name->name == $function_name) {

                $function_data = $workflow_structure->objects->functions_data[$i];

                if ($function_data->function_signature->lines->to == $function_data->body->lines->from) {
                    $function_body->content->lines->from = $function_data->body->lines->from + 1;
                    if ( strpos($code_line_array[$function_data->body->lines->from-1], '{') !== false ) {
                        $function_body->content->lines->from = $function_data->body->lines->from;
                    }
                }

                $function_body->content->lines->to = $function_data->body->lines->to;   //<-- thewrontas oti exw mono ena "}" gia na kleisw tn function!!!
            }
        }

        // dump([
        //     '$function_body' => $function_body,
        // ]);

        $process_name = $this->to_camel_case( $process_steps[0]->strings->class_name );

        //meta kanw t grafw tn kwdika mesa sthn function
        $code_until_function_signature = array_slice($code_line_array, 0, $function_body->content->lines->from, true);
        $code_in_function_body = array_slice($code_line_array, ($function_body->content->lines->from), ($function_body->content->lines->to - $function_body->content->lines->from - 1), true);
        $code_after_function_body = array_slice($code_line_array, $function_body->content->lines->to - 1, count($code_line_array) - 1, true);

        $new_function_body = [];
        foreach ($process_steps as $i => $process_step) {

            $previous_step_function_name = isset($process_steps[$i-1]) ? '$'.$this->to_camel_case($process_steps[$i-1]->objects->function_signatures[0]->name->name).'_result' : $workflow_function_arguments_string;
            $this_step_function_name = $process_step->objects->function_signatures[0]->name->name;
            $next_step_function_name = isset($process_steps[$i+1]) ? $process_steps[$i+1]->objects->function_signatures[0]->name->name : null;

            $stmt = "        ".'$'.$this->to_camel_case($this_step_function_name).'_result = '.
                    '$'.$process_name.'->'.$this_step_function_name.'('.$previous_step_function_name.');'."\n";
            
                    
            $new_function_body []= $stmt;
            
            if ($next_step_function_name === null) {
                $new_function_body []= "        ".'return '.'$'.$this->to_camel_case($this_step_function_name).'_result;'."\n";
            }
        }

        $new_code = array_merge($code_until_function_signature, $new_function_body, $code_after_function_body);

        dump([
            // '$code_until_function_signature' => $code_until_function_signature,
            // '$code_in_function_body' => $code_in_function_body,
            // '$new_function_body' => $new_function_body,
            // '$code_after_function_body' => $code_after_function_body,
            '$new_code' => $new_code,
        ]);

        //meta ftiaxnw tn constructor (enhmerwsh function signature k meta content)


        //telos kanw t namespaces


    }


    public function getClassStructure(string $code, string $function_name = ''): Object
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return (object)[];
        }

        $analysis_result = (object)[
            'class_namespace' => null,
            'class_name' => '',
            'used_namespaces' => [],
            'implemented_interfaces' => [],
            'functions_signature' => [],
            'constructor_data' => null,
            'functions_data' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result, $function_name){
            if ($node instanceof Node\Stmt\Namespace_) {
                $analysis_result->class_namespace = $node->name;
            }


            if ($node instanceof Node\Stmt\Use_) {
                $analysis_result->used_namespaces []= $node->uses[0];
            }

            if ($node instanceof Node\Stmt\Class_) {
                $analysis_result->class_name = $node->name;
                $analysis_result->implemented_interfaces []= $node->implements;

                //constructor_data
                $constructor_node = $node->stmts[3];

                $constructor_data = (object)[
                    'function_signature' => (object)[
                        'lines' => (object)[
                            'from' => 0,
                            'to' => 0
                        ],
                        'arguments' => [],
                    ],
                    'body' => (object)[
                        'lines' => (object)[
                            'from' => 0,
                            'to' => 0,
                        ]
                    ]
                ];

                foreach ($constructor_node->params as $constructor_arguments) {
                    $constructor_data->function_signature->arguments []= (object)[
                        'type' => isset($constructor_arguments->type) ? implode('\\', $constructor_arguments->type->parts) : null,
                        'name' => $constructor_arguments->var->name
                    ];

                    if (empty($constructor_data->function_signature->lines->from)) {
                        $constructor_data->function_signature->lines->from = $constructor_arguments->var->getAttributes()['startLine'];
                    } else if ($constructor_data->function_signature->lines->from > $constructor_arguments->var->getAttributes()['startLine']) {
                        $constructor_data->function_signature->lines->from = $constructor_arguments->var->getAttributes()['startLine'];
                    }

                    if (empty($constructor_data->function_signature->lines->to)) {
                        $constructor_data->function_signature->lines->to = $constructor_arguments->var->getAttributes()['endLine'];
                    } else if ($constructor_data->function_signature->lines->to < $constructor_arguments->var->getAttributes()['endLine']) {
                        $constructor_data->function_signature->lines->to = $constructor_arguments->var->getAttributes()['endLine'];
                    }
                }

                $constructor_data->body->lines->from = $constructor_node->getAttributes()['startLine'];
                $constructor_data->body->lines->to = $constructor_node->getAttributes()['endLine'];

                // dump([
                //     '$constructor_data' => $constructor_data,
                // ]);
                $analysis_result->constructor_data = $constructor_data;
            }

            if ($node instanceof Node\Stmt\ClassMethod) {
                if ($function_name == $node->name->name || $function_name === '') {
                    $tmp_function = (object)[
                        'flags' => $node->flags,  //dld public (1), protected (2), private (4), e.t.c.
                        'name' => $node->name,
                        'attributes' => $node->getAttributes(),
                    ];
    
                    if (!empty($node->params)) {
                        $tmp = [];
                        foreach ($node->params as $parameter) {
                            $tmp []= (object)[
                                'type' => $parameter->type,
                                'var' => $parameter->var,
                            ];
                        }
                        $tmp_function->parameters []= $tmp;
                    }
    
                    if (isset($node->returnType)) {
                        if (isset($node->returnType->name)) {
                            $tmp_function->return_type = $node->returnType->name;
                        } else if (isset($node->returnType->parts)) {
                            $tmp_function->return_type = $node->returnType->parts[ count($node->returnType->parts)-1 ];
                        }
                    }

                    //function_data
                    $function_node = $node;
                
                    $function_data = (object)[
                        'name' => $node->name->name,
                        'function_signature' => (object)[
                            'lines' => (object)[
                                'from' => 0,
                                'to' => 0
                            ],
                            'arguments' => [],
                        ],
                        'body' => (object)[
                            'lines' => (object)[
                                'from' => 0,
                                'to' => 0,
                            ]
                        ]
                    ];

                    foreach ($function_node->params as $function_arguments) {
                        $function_data->function_signature->arguments []= (object)[
                            'type' => isset($function_arguments->type) ? implode('\\', $function_arguments->type->parts) : null,
                            'name' => $function_arguments->var->name
                        ];

                        if (empty($function_data->function_signature->lines->from)) {
                            $function_data->function_signature->lines->from = $function_arguments->var->getAttributes()['startLine'];
                        } else if ($function_data->function_signature->lines->from > $function_arguments->var->getAttributes()['startLine']) {
                            $function_data->function_signature->lines->from = $function_arguments->var->getAttributes()['startLine'];
                        }

                        if (empty($function_data->function_signature->lines->to)) {
                            $function_data->function_signature->lines->to = $function_arguments->var->getAttributes()['endLine'];
                        } else if ($function_data->function_signature->lines->to < $function_arguments->var->getAttributes()['endLine']) {
                            $function_data->function_signature->lines->to = $function_arguments->var->getAttributes()['endLine'];
                        }
                    }

                    $function_data->body->lines->from = $function_node->getAttributes()['startLine'];
                    $function_data->body->lines->to = $function_node->getAttributes()['endLine'];

                    // dump([
                    //     '$function_data' => $function_data,
                    // ]);
                    $analysis_result->functions_data []= $function_data;
                    $analysis_result->functions_signature []= $tmp_function;
                }
            }

        });

        $class_namespace_string = '';
        if (isset($analysis_result->class_namespace)) {
            $class_namespace_string = implode('\\',$analysis_result->class_namespace->parts);
        }

        $class_implemented_interfaces_namespaces_strings = [];
        foreach ($analysis_result->implemented_interfaces[0] as $implemented_interface) {
            $interface_name = $implemented_interface->parts[ count($implemented_interface->parts)-1 ];
            $tmp_used_namespace = null;

            //gia na valw t swsta `use` sthn arxh toy arxeioy
            foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                if (isset($used_namespace->alias)) {
                    if ($interface_name == $used_namespace->alias) {
                        $tmp_used_namespace = $used_namespace;
                    }
                } else {
                    if ($interface_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                        $tmp_used_namespace = $used_namespace;
                    }
                }
            }

            if (is_array($tmp_used_namespace)) {
                array_pop($tmp_used_namespace->name->parts);
                $class_implemented_interfaces_namespaces_strings []= implode('\\', $tmp_used_namespace->name->parts );
            } else {
                $class_implemented_interfaces_namespaces_strings []= $class_namespace_string;
            }
        }


        //gia t interface
        $used_namespaces_indexes = [];
        $function_signature_strings = [];
        foreach ($analysis_result->functions_signature as $function_signature) {
            $function_signature_string = '';

            //gia tn modifier/access
            if ($function_signature->flags == 1) {
                $function_signature_string .= 'public ';
            } else if ($function_signature->flags == 2) {
                $function_signature_string .= 'protected ';
            } else if ($function_signature->flags == 4) {
                $function_signature_string .= 'private ';
            }

            //t onoma
            $function_signature_string .= 'function '.$function_signature->name.'(';
            
            //gia tis parametroys
            if (isset($function_signature->parameters)) {
                $function_signature_parameters = [];
                foreach ($function_signature->parameters[0] as $parameter) {
                    $type = null;
                    if ($parameter->type) {
                        $type = $parameter->type;
                        $type_name = '';
                        if (isset($parameter->type->parts)) {
                            $type_name = $parameter->type->parts[ count($parameter->type->parts)-1 ];
                        } else {
                            $type_name = $type->name;
                        }

                        //gia na valw t swsta `use` sthn arxh toy arxeioy
                        foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                            if (isset($used_namespace->alias)) {
                                if ($type_name == $used_namespace->alias) {
                                    if (!in_array($index, $used_namespaces_indexes)) {
                                        $used_namespaces_indexes []= $index;
                                    }
                                }
                            } else {
                                if ($type_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                                    if (!in_array($index, $used_namespaces_indexes)) {
                                        $used_namespaces_indexes []= $index;
                                    }
                                }
                            }
                        }
                    }


                    if (isset($type) && isset($parameter->var)) {
                        $tmp_type = '';
                        if (isset($type->parts)) {
                            $tmp_type = implode('\\',$type->parts);
                        } else {
                            $tmp_type = $type->name;
                        }
                        $function_signature_parameters []= $tmp_type.' $'.$parameter->var->name;
                    } else if (isset($parameter->var)) {
                        $function_signature_parameters []= '$'.$parameter->var->name;
                    }
                }

                $function_signature_string .= implode(', ',$function_signature_parameters).')';
            } else {
                $function_signature_string .= ')';
            }            

            //gia to return type
            if (isset($function_signature->return_type)) {

                $type_name = $function_signature->return_type;

                //gia na valw t swsta `use` sthn arxh toy arxeioy
                foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                    if (isset($used_namespace->alias)) {
                        if ($type_name == $used_namespace->alias) {
                            if (!in_array($index, $used_namespaces_indexes)) {
                                $used_namespaces_indexes []= $index;
                            }
                        }
                    } else {
                        if ($type_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                            if (!in_array($index, $used_namespaces_indexes)) {
                                $used_namespaces_indexes []= $index;
                            }
                        }
                    }
                }

                $function_signature_string .= ': '.$function_signature->return_type.';';
            }

            $function_signature_strings []= $function_signature_string;
        }

        //sort $used_namespaces_indexes ascending order
        sort($used_namespaces_indexes);

        $used_namespaces_strings = [];
        foreach ($used_namespaces_indexes as $used_namespaces_index) {
            $used_namespaces_string = 'use ';
            $tmp_used_namespace = $analysis_result->used_namespaces[$used_namespaces_index];

            $used_namespaces_string .= implode('\\',$tmp_used_namespace->name->parts);
            if (isset($tmp_used_namespace->alias)) {
                $used_namespaces_string .= ' as '.$tmp_used_namespace->alias.';';
            } else {
                $used_namespaces_string .= ';';
            }

            $used_namespaces_strings []= $used_namespaces_string;
        }
        

        return (object)[
            'strings' => (object)[
                'class_implemented_interfaces_namespaces' => $class_implemented_interfaces_namespaces_strings,
                'class_namespace' => $class_namespace_string,
                'class_name' => $analysis_result->class_name->name,
                'used_namespaces' => $used_namespaces_strings,
                'function_signatures' => $function_signature_strings,
            ],
            'objects' => (object)[
                'used_namespaces' => $analysis_result->used_namespaces,
                'functions_data' => $analysis_result->functions_data,           //<-- these indexes are correlated to each other!!
                'function_signatures' => $analysis_result->functions_signature, //<-- these indexes are correlated to each other!!
            ]
        ];
    }

    public function getClassFunctionSignature(string $code, string $function_name): Object
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return (object)[];
        }

        $analysis_result = (object)[
            'class_namespace' => null,
            'class_name' => '',
            'used_namespaces' => [],
            'implemented_interfaces' => [],
            'functions_signature' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result, $function_name){
            if ($node instanceof Node\Stmt\Namespace_) {
                $analysis_result->class_namespace = $node->name;
            }


            if ($node instanceof Node\Stmt\Use_) {
                $analysis_result->used_namespaces []= $node->uses[0];
            }

            if ($node instanceof Node\Stmt\Class_) {
                $analysis_result->class_name = $node->name;
                $analysis_result->implemented_interfaces []= $node->implements;
            }

            if ($node instanceof Node\Stmt\ClassMethod) {
                if ($function_name == $node->name->name) {
                    $tmp_function = (object)[
                        'flags' => $node->flags,  //dld public (1), protected (2), private (4), e.t.c.
                        'name' => $node->name,
                    ];
    
                    if (!empty($node->params)) {
                        $tmp = [];
                        foreach ($node->params as $parameter) {
                            $tmp []= (object)[
                                'type' => $parameter->type,
                                'var' => $parameter->var,
                            ];
                        }
                        $tmp_function->parameters []= $tmp;
                    }
    
                    if (isset($node->returnType)) {
                        if (isset($node->returnType->name)) {
                            $tmp_function->return_type = $node->returnType->name;
                        } else if (isset($node->returnType->parts)) {
                            $tmp_function->return_type = $node->returnType->parts[ count($node->returnType->parts)-1 ];
                        }
                    }
                    
                    $analysis_result->functions_signature []= $tmp_function;
                }
            }

        });

        $class_namespace_string = '';
        if (isset($analysis_result->class_namespace)) {
            $class_namespace_string = implode('\\',$analysis_result->class_namespace->parts);
        }

        $class_implemented_interfaces_namespaces_strings = [];
        foreach ($analysis_result->implemented_interfaces[0] as $implemented_interface) {
            $interface_name = $implemented_interface->parts[ count($implemented_interface->parts)-1 ];
            $tmp_used_namespace = null;

            //gia na valw t swsta `use` sthn arxh toy arxeioy
            foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                if (isset($used_namespace->alias)) {
                    if ($interface_name == $used_namespace->alias) {
                        $tmp_used_namespace = $used_namespace;
                    }
                } else {
                    if ($interface_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                        $tmp_used_namespace = $used_namespace;
                    }
                }
            }

            if (is_array($tmp_used_namespace)) {
                array_pop($tmp_used_namespace->name->parts);
                $class_implemented_interfaces_namespaces_strings []= implode('\\', $tmp_used_namespace->name->parts );
            } else {
                $class_implemented_interfaces_namespaces_strings []= $class_namespace_string;
            }
        }


        //gia t interface
        $used_namespaces_indexes = [];
        $function_signature_strings = [];
        foreach ($analysis_result->functions_signature as $function_signature) {
            $function_signature_string = '';

            //gia tn modifier/access
            if ($function_signature->flags == 1) {
                $function_signature_string .= 'public ';
            } else if ($function_signature->flags == 2) {
                $function_signature_string .= 'protected ';
            } else if ($function_signature->flags == 4) {
                $function_signature_string .= 'private ';
            }

            //t onoma
            $function_signature_string .= 'function '.$function_signature->name.'(';
            
            //gia tis parametroys
            if (isset($function_signature->parameters)) {
                $function_signature_parameters = [];
                foreach ($function_signature->parameters[0] as $parameter) {
                    $type = null;
                    if ($parameter->type) {
                        $type = $parameter->type;
                        $type_name = '';
                        if (isset($parameter->type->parts)) {
                            $type_name = $parameter->type->parts[ count($parameter->type->parts)-1 ];
                        } else {
                            $type_name = $type->name;
                        }

                        //gia na valw t swsta `use` sthn arxh toy arxeioy
                        foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                            if (isset($used_namespace->alias)) {
                                if ($type_name == $used_namespace->alias) {
                                    if (!in_array($index, $used_namespaces_indexes)) {
                                        $used_namespaces_indexes []= $index;
                                    }
                                }
                            } else {
                                if ($type_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                                    if (!in_array($index, $used_namespaces_indexes)) {
                                        $used_namespaces_indexes []= $index;
                                    }
                                }
                            }
                        }
                    }


                    if (isset($type) && isset($parameter->var)) {
                        $tmp_type = '';
                        if (isset($type->parts)) {
                            $tmp_type = implode('\\',$type->parts);
                        } else {
                            $tmp_type = $type->name;
                        }
                        $function_signature_parameters []= $tmp_type.' $'.$parameter->var->name;
                    } else if (isset($parameter->var)) {
                        $function_signature_parameters []= '$'.$parameter->var->name;
                    }
                }

                $function_signature_string .= implode(', ',$function_signature_parameters).')';
            } else {
                $function_signature_string .= ')';
            }            

            //gia to return type
            if (isset($function_signature->return_type)) {

                $type_name = $function_signature->return_type;

                //gia na valw t swsta `use` sthn arxh toy arxeioy
                foreach ($analysis_result->used_namespaces as $index => $used_namespace) {
                    if (isset($used_namespace->alias)) {
                        if ($type_name == $used_namespace->alias) {
                            if (!in_array($index, $used_namespaces_indexes)) {
                                $used_namespaces_indexes []= $index;
                            }
                        }
                    } else {
                        if ($type_name == $used_namespace->name->parts[ count($used_namespace->name->parts)-1 ]) {
                            if (!in_array($index, $used_namespaces_indexes)) {
                                $used_namespaces_indexes []= $index;
                            }
                        }
                    }
                }

                $function_signature_string .= ': '.$function_signature->return_type.';';
            }

            $function_signature_strings []= $function_signature_string;
        }

        //sort $used_namespaces_indexes ascending order
        sort($used_namespaces_indexes);

        $used_namespaces_strings = [];
        foreach ($used_namespaces_indexes as $used_namespaces_index) {
            $used_namespaces_string = 'use ';
            $tmp_used_namespace = $analysis_result->used_namespaces[$used_namespaces_index];

            $used_namespaces_string .= implode('\\',$tmp_used_namespace->name->parts);
            if (isset($tmp_used_namespace->alias)) {
                $used_namespaces_string .= ' as '.$tmp_used_namespace->alias.';';
            } else {
                $used_namespaces_string .= ';';
            }

            $used_namespaces_strings []= $used_namespaces_string;
        }
        

        return (object)[
            'strings' => (object)[
                'class_implemented_interfaces_namespaces' => $class_implemented_interfaces_namespaces_strings,
                'class_namespace' => $class_namespace_string,
                'class_name' => $analysis_result->class_name->name,
                'used_namespaces' => $used_namespaces_strings,
                'function_signatures' => $function_signature_strings,
            ],
            'objects' => (object)[
                'used_namespaces' => $analysis_result->used_namespaces,
                'function_signatures' => $analysis_result->functions_signature,
            ]
        ];
    }


    function to_camel_case($input) {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
        // preg_match_all('!([A-Z][A-Z0-9]*(?=$|[A-Z][a-z0-9])|[A-Za-z][a-z0-9]+)!', $input, $matches);
        // $ret = $matches[0];
        // foreach ($ret as &$match) {
        //     $match = $match == strtoupper($match) ? strtolower($match) : lcfirst($match);
        // }
        // return implode('_', $ret);
    }


    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $td_entity_name, string $function_name, Command $command): void
    {
        //
        //sto workflow tha ftiaxnw taytoxrona k ola ta alla functions/domes
        
        //new validation class
        $command->call('trident:generate:validation', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

    }

    
    

}

// needed for workflow
class DefaultMarking
{
    public $td_entity_name;
    public $td_entity_workflow_function_name;
    public $marking;
}