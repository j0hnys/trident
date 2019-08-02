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


        // analyw tn callback function gia na parw t dependencies ths
        $first = $tmp_data['transition_listeners'][ $process_execution_direction[0] ];
        $first_filepath = $tmp_data['transition_listeners_filepaths'][ $process_execution_direction[0] ];


        $callback = explode('@',$first);
        $callback_class_namespace = $callback[0];
        $callback_fuction = $callback[1];

        $class_interface = new ClassInterface($this->storage_disk);

        $code = $this->storage_disk->readFile( $first_filepath );
        $first_process_code_analysis_result = $class_interface->getClassFunctionSignatures($code);

        // foreach ($first_process_code_analysis_result->objects->function_signatures as $function_signature) {
        //     dump([
        //         '$function_signature' => $function_signature->name->name
        //     ]);
        // }

        $td_entity_name = 'DemoProcess';
        $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.$td_entity_name.'.php' );
        $workflow_analysis_result = $class_interface->getClassFunctionSignatures($code);

        $this->updateWorkflowFunction($code,'',[]);

        // dump([
        //     '$process_execution_direction' => $process_execution_direction,
        //     '$first' => $first,
        //     '$first_process_code_analysis_result' => $first_process_code_analysis_result->strings,
        //     '$workflow_analysis_result' => $workflow_analysis_result->strings,
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


    public function updateWorkflowFunction(string $code, string $function_name, array $schema)
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
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result){

            if ($node instanceof Node\Stmt\Namespace_) {
                $analysis_result->class_namespace = $node->name;
            }


            if ($node instanceof Node\Stmt\Use_) {
                // dump([
                //     '$node' => $node->uses[0],
                // ]);
                $analysis_result->used_namespaces []= $node->uses[0];
            }

            if ($node instanceof Node\Stmt\Class_) {

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
                        'type' => implode('\\', $constructor_arguments->type->parts),
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
                //     // '$node' => $constructor_node->getAttributes(),
                //     '$constructor_data' => $constructor_data,
                // ]);
                $analysis_result->class_name = $node->name;
                $analysis_result->implemented_interfaces []= $node->implements;
            }

            if ($node instanceof Node\Stmt\ClassMethod) {
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
        });



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