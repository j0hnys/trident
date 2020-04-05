<?php

namespace j0hnys\Trident\Builders;

use Illuminate\Console\Command;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Builders;
use j0hnys\Trident\Base\Constants\Trident\Functionality;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Utilities\WordCaseConverter;

class WorkflowLogicFunction
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
        $this->functionality_definition = new Functionality();
        $this->folder_structure = new FolderStructure();
        $this->word_case_converter = new WordCaseConverter();
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name, array $options = [], Command $command): void
    {

        $this->generateLogicFunction($td_entity_name, $function_name, $options);

        $this->generateOther($td_entity_name, $function_name, $options, $command);

    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generateLogicFunction(string $td_entity_name, string $function_name, array $options): void
    {
        $functionality_schema = [];
        if ($options['functionality_schema_path']) {
            if (!empty($options['functionality_schema_path'])) {
                $functionality_schema = json_decode( $this->storage_disk->readFile( $options['functionality_schema_path'] ),true);
                $this->functionality_definition->check($functionality_schema, 'schema');

                if (isset($functionality_schema['endpoint'])) {
                    $this->functionality_definition->check($functionality_schema, 'endpoint');
                }
            }
        }

        //
        //workflowLogic function generation
        $this->folder_structure->checkPath('tests/Trident/Workflows/Logic/*');
        $workflow_logic_path = $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($td_entity_name).'.php';
        
        $lines = $this->storage_disk->readFileArray($workflow_logic_path); 
        $last = sizeof($lines) - 1; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($workflow_logic_path, $lines);

        $stub = $this->storage_disk->readFile(__DIR__.'/../Stubs/Trident/Workflows/LogicFunction.stub');

        $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ($function_name), $stub);
        $stub = str_replace('{{Function_name}}', ucfirst($function_name), $stub);
        $stub = str_replace('{{function_parameter_name}}', $this->word_case_converter->camelCaseToSnakeCase($function_name), $stub);
        
        $this->storage_disk->writeFile($workflow_logic_path, $stub, [
            'append_file' => true
        ]);

        //
        //update dependencies in use
        $this->addDependenciesInUses($workflow_logic_path, $td_entity_name, $function_name);

        //
        //update routes
        if (isset($functionality_schema['endpoint'])) {
            $this->updateRoutes($td_entity_name, $function_name, $functionality_schema);
        }
               
    }

    /**
     * @param string $workflow_logic_path
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function addDependenciesInUses(string $workflow_logic_path, string $td_entity_name, string $function_name): void
    {
        $code = $this->storage_disk->readFile($workflow_logic_path); 
        $lines = $this->storage_disk->readFileArray($workflow_logic_path); 

        //code analysis
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        $analysis_result = (object)[
            'used_namespaces' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result){
            if ($node instanceof Node\Stmt\Use_) {
                $analysis_result->used_namespaces []= $node->uses[0];
            }
        });

        //use addition in code
        $new_use_string = 'use App\Trident\Workflows\Schemas\Logic\\'.$td_entity_name.'\Resources\\'.$td_entity_name.$function_name.'Resource;'."\r\n";
        $use_struct_string = 'use App\Trident\Workflows\Schemas\Logic\\'.$td_entity_name.'\Typed\Struct'.ucfirst($function_name).$td_entity_name.';'."\r\n";

        $start_line = ($analysis_result->used_namespaces[count($analysis_result->used_namespaces)-1])->getStartLine();
        $end_line = ($analysis_result->used_namespaces[count($analysis_result->used_namespaces)-1])->getEndLine();

        array_splice($lines, $start_line, 0, $new_use_string);
        array_splice($lines, ($start_line+1), 0, $use_struct_string);

        //update file
        $this->storage_disk->writeFileArray($workflow_logic_path, $lines); 
    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param array $functionality_schema
     * @return void
     */
    public function updateRoutes(string $td_entity_name, string $function_name, array $functionality_schema): void
    {
        $this->folder_structure->checkPath('routes/trident.php');
        $trident_resource_routes_path = $this->storage_disk->getBasePath() . '/routes/trident.php';
        
        $lines = $this->storage_disk->readFileArray($trident_resource_routes_path); 

        $auth_group_start_line = 0;
        $auth_group_end_line = 0;
        $endpoint_exist = false;
        foreach ($lines as $i => $line) {
            if (strpos($line, "Route::middleware(['auth'])") === 0) {
                $auth_group_start_line = $i;
            }
            if (strpos($line, "});") === 0) {
                $auth_group_end_line = $i;
            }

            if ($auth_group_start_line > 0) {
                if (strpos($line, $functionality_schema['endpoint']['uri']) !== false) {
                    $endpoint_exist = true;
                }
            }
        }

        if (!$endpoint_exist) {
            $http_method = '';
            if ($functionality_schema['endpoint']['type'] === 'create') {
                $http_method = 'post';
            } else if ($functionality_schema['endpoint']['type'] === 'read') {
                $http_method = 'get';
            } else if ($functionality_schema['endpoint']['type'] === 'update') {
                $http_method = 'put';
            } else if ($functionality_schema['endpoint']['type'] === 'delete') {
                $http_method = 'delete';
            }
            $line = "Route::".$http_method."('".$functionality_schema['endpoint']['uri']."', '".$td_entity_name."Controller@".$function_name."');";
            if ($functionality_schema['endpoint']['group'] === 'auth') {
                array_splice($lines, $auth_group_end_line, 0, ['    '.$line, "\r\n"]);
            } else {
                array_splice($lines, count($lines), 0, ["\r\n", $line]);
            }
        }

        $this->storage_disk->writeFileArray($trident_resource_routes_path, $lines); 
    }

    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $td_entity_name, string $function_name, array $options = [], Command $command): void
    {
        //
        //sto workflow tha ftiaxnw taytoxrona k ola ta alla functions/domes
        //new controller function
        $command->call('trident:generate:controller_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        //new policy function
        $command->call('trident:generate:policy_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        //new business logic function
        $command->call('trident:generate:business_logic_function', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
        ]);

        
        //new validation class
        $command->call('trident:generate:validation', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--schema_path' => $options['validation_schema_path']
        ]);

        //new strict type
        $command->call('trident:generate:strict_type', [
            'strict_type_name' => 'struct_optional_workflow_function',
            'function_name' => $function_name,
            'entity_name' => $td_entity_name,
            '--workflow' => true,
            '--schema_path' => $options['strict_type_schema_path']
        ]);

        // new resource and it's collection
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => false,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
        $command->call('trident:generate:resource', [
            'entity_name' => $td_entity_name,
            'function_name' => $function_name,
            '--collection' => true,
            '--workflow' => true,
            '--schema_path' => $options['resource_schema_path']
        ]);
    }

    
    

}