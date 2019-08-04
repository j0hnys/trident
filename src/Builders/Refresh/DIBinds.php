<?php

namespace j0hnys\Trident\Builders\Refresh;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Declarations;

use j0hnys\Trident\Builders\WorkflowFunctionProcess;

class DIBinds
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;
    private $declarations;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->storage_disk = new Disk();      
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }  
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
        $this->mustache = new \Mustache_Engine;
        $this->declarations = new Declarations();
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function run(string $name = 'TEST'): void
    {

        $Td_entities_workflows = $this->storage_trident->getCurrentWorkflows();
        $Td_entities_businesses = $this->storage_trident->getCurrentBusinesses();
        $Td_entities_processes = $this->storage_trident->getCurrentProcesses();

        // dd([
        //     '$Td_entities_workflows' => $Td_entities_workflows,
        //     '$Td_entities_businesses' => $Td_entities_businesses,
        //     '$Td_entities_processes' => $Td_entities_processes,
        // ]);

        $workflow_logic_di_interfaces = [];
        $business_logic_di_interfaces = [];
        $process_di_interfaces = [];

        //workflow
        foreach ($Td_entities_workflows as $Td_entities_workflow) {

            $name = $Td_entities_workflow;
    
            $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php' );
            $workflow_logic_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

        //business
        foreach ($Td_entities_businesses as $Td_entities_business) {
            
            $name = $Td_entities_business;

            $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.ucfirst($name).'.php' );
            $business_logic_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

        //process
        foreach ($Td_entities_processes as $Td_entities_process) {
            
            $name = $Td_entities_process;

            $code = $this->storage_disk->readFile( $Td_entities_process );
            $process_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

        //

        //workflow
        $workflow_logic_interface_class_instantiations = [];
        foreach ($workflow_logic_di_interfaces as $workflow_logic => $di_interfaces) {
            foreach ($di_interfaces as $di_interface) {
                $class_name = str_replace('Interfaces','',$di_interface);
                $class_name = str_replace('Interface','',$class_name);
                $class_name = str_replace('\\\\','\\',$class_name);

                if (!isset($workflow_logic_interface_class_instantiations[$workflow_logic])) {
                    $workflow_logic_interface_class_instantiations[$workflow_logic] = [];
                }
    
                if (strpos($class_name,'\\Repositories')) {
                    $workflow_logic_interface_class_instantiations[$workflow_logic] []= 'new \\'.$class_name.'($app)';
                } else {
                    $workflow_logic_interface_class_instantiations[$workflow_logic] []= 'new \\'.$class_name;
                }
            }
        }

        //business
        $business_logic_interface_class_instantiations = [];
        foreach ($business_logic_di_interfaces as $business_logic => $di_interfaces) {
            foreach ($di_interfaces as $di_interface) {
                $class_name = str_replace('Interfaces','',$di_interface);
                $class_name = str_replace('Interface','',$class_name);
                $class_name = str_replace('\\\\','\\',$class_name);

                if (!isset($business_logic_interface_class_instantiations[$business_logic])) {
                    $business_logic_interface_class_instantiations[$business_logic] = [];
                }
    
                if (strpos($class_name,'\\Repositories')) {
                    $business_logic_interface_class_instantiations[$business_logic] []= 'new \\'.$class_name.'($app)';
                } else {
                    $business_logic_interface_class_instantiations[$business_logic] []= 'new \\'.$class_name;
                }
            }
        }

        //process
        $process_interface_class_instantiations = [];
        foreach ($process_di_interfaces as $process => $di_interfaces) {
            foreach ($di_interfaces as $di_interface) {
                $class_name = str_replace('Interfaces','',$di_interface);
                $class_name = str_replace('Interface','',$class_name);
                $class_name = str_replace('\\\\','\\',$class_name);

                if (!isset($process_interface_class_instantiations[$process])) {
                    $process_interface_class_instantiations[$process] = [];
                }
    
                if (strpos($class_name,'\\Repositories')) {
                    $process_interface_class_instantiations[$process] []= 'new \\'.$class_name.'($app)';
                } else {
                    $process_interface_class_instantiations[$process] []= 'new \\'.$class_name;
                }
            }
        }

        
        //
        //update TridentServiceProvider
        
        //workflows
        $workflows = [];
        if (!empty($workflow_logic_interface_class_instantiations)) {
            $workflows = array_map(function($element) use ($workflow_logic_interface_class_instantiations){
                return [
                    'Td_entity' => ucfirst($element),
                    'interface_class_instantiations' => implode(",\n".'                ', $workflow_logic_interface_class_instantiations[$element]),
                ];
            },$Td_entities_workflows);
        }

        //businesses
        $businesses = [];
        if (!empty($business_logic_interface_class_instantiations)) {
            $businesses = array_map(function($element) use ($business_logic_interface_class_instantiations) {
                return [
                    'Td_entity' => ucfirst($element),
                    'interface_class_instantiations' => implode(",\n".'                ', $business_logic_interface_class_instantiations[$element]),
                ];
            },$Td_entities_businesses);
        }

        //processes
        $processes = [];
        if (!empty($process_interface_class_instantiations)) {
            $processes = array_values(array_filter(array_map(function($element) use ($process_interface_class_instantiations) {
                if (!isset($process_interface_class_instantiations[$element])) {
                    return false;
                }

                $workflow_function_process = new WorkflowFunctionProcess();
                $code = $this->storage_disk->readFile( $element );
                $code_analysis_result = $workflow_function_process->getClassStructure($code);

                $class_name = $code_analysis_result->strings->class_name;
                $class_path = $code_analysis_result->strings->class_namespace.$class_name;

                $implemented_interface = array_values(array_filter(array_map(function($element) use ($class_name) {
                    return $element->name->parts[ count($element->name->parts)-1 ] == $class_name.'Interface' ? implode('\\',$element->name->parts) : false;
                }, $code_analysis_result->objects->used_namespaces)))[0];

                return [
                    'Td_entity_interface' => $implemented_interface,
                    'Td_entity_class_path' => $class_path,
                    'interface_class_instantiations' => implode(",\n".'                ', $process_interface_class_instantiations[$element]),
                ];
            },$Td_entities_processes)));
        }


        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../../src/Stubs/app/Providers/TridentServiceProvider_dynamic.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
            'register_process' => $processes,
        ]);
        

        $this->storage_disk->writeFile($trident_event_service_provider_path, $stub);


    }

    /**
     * @param string $code
     * @return array
     */
    public function getDIInterfaces(string $code): array
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return [];
        }

        $analysis_result = (object)[
            'used_namespaces' => [],
            'constructor_params' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result){
            if ($node instanceof Node\Stmt\Use_) {
                $analysis_result->used_namespaces []= $node->uses[0];
            }

            if ($node instanceof Node\Stmt\ClassMethod) {
                if ($node->name == '__construct') {
                    if (!empty($node->params)) {
                        $tmp = [];
                        foreach ($node->params as $parameter) {
                            $tmp []= (object)[
                                'type' => $parameter->type,
                                'var' => $parameter->var,
                            ];
                        }
                        $analysis_result->constructor_params = $tmp;
                    }
                }
            }

        });


        $di_interfaces = [];

        foreach ($analysis_result->constructor_params as $constructor_param) {
            foreach ($analysis_result->used_namespaces as $used_namespace) {
                if (isset($constructor_param->type->parts)) {
                    if (count($constructor_param->type->parts) == 1) {  //dld exw alias
                        if (!empty($used_namespace->alias)) {
                            if ($used_namespace->alias == $constructor_param->type->parts[0]) {
                                $di_interfaces []= (object)[
                                    'name' => $used_namespace->name
                                ];
                            }
                        }
                    } else {
                        // TO DO
                    }
                }
            }

        }


        return $di_interfaces;
    }


}
