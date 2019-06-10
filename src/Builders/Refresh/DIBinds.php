<?php

namespace j0hnys\Trident\Builders\Refresh;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

class DIBinds
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($name = 'TEST')
    {
        
        $mustache = new \Mustache_Engine;


        $Td_entities_workflows = $this->getCurrentWorkflows();
        $Td_entities_businesses = $this->getCurrentBusinesses();

        $workflow_logic_di_interfaces = [];
        $business_logic_di_interfaces = [];

        foreach ($Td_entities_workflows as $Td_entities_workflow) {

            $name = $Td_entities_workflow;
    
            $code = file_get_contents( base_path().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php' );
            $workflow_logic_di_interfaces[$name] = array_map(function($element) {
                return implode('\\', $element->name->parts);
            }, $this->getDIInterfaces($code));
        }

        foreach ($Td_entities_businesses as $Td_entities_business) {
            
            $name = $Td_entities_business;

            $code = file_get_contents( base_path().'/app/Trident/Business/Logic/'.ucfirst($name).'.php' );
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


        $trident_event_service_provider_path = base_path().'/app/Providers/TridentServiceProvider.php';
        $stub = file_get_contents(__DIR__.'/../../../src/Stubs/app/Providers/TridentServiceProvider_dynamic.stub');
        $stub = $mustache->render($stub, [
            'register_workflows' => $workflows,
            'register_business' => $businesses,
        ]);
        

        file_put_contents($trident_event_service_provider_path, $stub);


    }


    public function getDIInterfaces(string $code)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
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

    
     /**
     * Build the directory for the class if necessary.
     *
     * @param  string $path
     * @return string
     */
    protected function makeDirectory(string $path)
    {
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }
    }

     /**
     * make the appropriate file for the class if necessary.
     *
     * @param  string $path
     * @return void
     */
    protected function makeFile(string $name, string $fullpath_to_create, string $stub_fullpath)
    {
        
        if (file_exists($fullpath_to_create)) {
            // throw new \Exception($fullpath_to_create . ' already exists!');
            return;
        }

        $this->makeDirectory($fullpath_to_create);

        $stub = file_get_contents($stub_fullpath);

        $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        
        file_put_contents($fullpath_to_create, $stub);
    }
    

    /**
     * return the names of all events from trigger folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentWorkflows()
    {
        $files = scandir(base_path().'/app/Trident/Workflows/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }

    /**
     * return the names of all events from subscriber folder. (assumes that the namespace conventions are applied)
     *
     * @return array
     */
    public function getCurrentBusinesses()
    {
        $files = scandir(base_path().'/app/Trident/Business/Logic/');

        $filenames = [];
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                $filenames []= str_replace('.php','',$file);
            }
        }

        return $filenames;
    }



}
