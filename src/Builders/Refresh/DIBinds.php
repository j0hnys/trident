<?php

namespace j0hnys\Trident\Builders\Refresh;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Declarations;

class DIBinds
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;
    private $declarations;

    public function __construct()
    {
        $this->storage_disk = new Disk();        
        $this->storage_trident = new Trident();
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

        $workflow_logic_di_interfaces = [];
        $business_logic_di_interfaces = [];

        foreach ($Td_entities_workflows as $Td_entities_workflow) {

            $name = $Td_entities_workflow;
    
            $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php' );
            $workflow_logic_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

        foreach ($Td_entities_businesses as $Td_entities_business) {
            
            $name = $Td_entities_business;

            $code = $this->storage_disk->readFile( $this->storage_disk->getBasePath().'/app/Trident/Business/Logic/'.ucfirst($name).'.php' );
            $business_logic_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

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

        
        //
        //update TridentServiceProvider
        $workflows = [];
        if (!empty($workflow_logic_interface_class_instantiations)) {
            $workflows = array_map(function($element) use ($workflow_logic_interface_class_instantiations){
                return [
                    'Td_entity' => ucfirst($element),
                    'interface_class_instantiations' => implode(",\n", $workflow_logic_interface_class_instantiations[$element]),
                ];
            },$Td_entities_workflows);
        }

        $businesses = [];
        if (!empty($business_logic_interface_class_instantiations)) {
            $businesses = array_map(function($element) use ($business_logic_interface_class_instantiations) {
                return [
                    'Td_entity' => ucfirst($element),
                    'interface_class_instantiations' => implode(",\n", $business_logic_interface_class_instantiations[$element]),
                ];
            },$Td_entities_businesses);
        }


        $trident_event_service_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../../src/Stubs/app/Providers/TridentServiceProvider_dynamic.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
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


        return $di_interfaces;
    }


}
