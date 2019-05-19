<?php

namespace j0hnys\Trident\Builders\Refresh;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

class ClassInterface
{
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function __construct($name, $relative_input_path, $relative_output_path)
    {
        // $name = 'DemoProcess';
        // $input_path = base_path().'/'.'app/Trident/Workflows/Logic';
        // $output_path = base_path().'/'.'app/Trident/Interfaces/Workflows/Logic';

        $input_path = base_path().'/'.$relative_input_path;
        $output_path = base_path().'/'.$relative_output_path;
        

        $mustache = new \Mustache_Engine;
        

        $code = file_get_contents( $input_path.'/'.$name.'.php' );
        $result = $this->getClassFunctionSignatures($code);


        $namespace = $result->strings->class_implemented_interfaces_namespaces[0];

        $used_namespaces = array_map(function($element){
            return [
                'used_namespace' => $element,
            ];
        },$result->strings->used_namespaces);

        $class_name = $result->strings->class_name;

        $function_signatures = array_map(function($element){
            return [
                'function_signature' => $element,
            ];
        },$result->strings->function_signatures);


        $stub = file_get_contents(__DIR__.'/../../../src/Stubs/PHP/Interface.stub');
        $stub = $mustache->render($stub, [
            'namespace' => $namespace,
            'used_namespaces' => $used_namespaces,
            'class_name' => $class_name,
            'function_signatures' => $function_signatures,
        ]);
                

        file_put_contents($output_path.'/'.$name.'Interface.php', $stub);
    }


    public function getClassFunctionSignatures(string $code)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        // $dumper = new NodeDumper;
        // // dump($ast[0]->exprs);
        // echo $dumper->dump($ast) . "\n"; exit;
        

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

                $analysis_result->used_namespaces []= $node->uses[0];

            }

            if ($node instanceof Node\Stmt\Class_) {
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
                
                $analysis_result->functions_signature []= $tmp_function;
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

            array_pop($tmp_used_namespace->name->parts);
            $class_implemented_interfaces_namespaces_strings []= implode('\\', $tmp_used_namespace->name->parts );
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
                        $type_name = $parameter->type->parts[ count($parameter->type->parts)-1 ];

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
                        $function_signature_parameters []= implode('\\',$type->parts).' $'.$parameter->var->name;
                    } else if (isset($parameter->var)) {
                        $function_signature_parameters []= '$'.$parameter->var->name;
                    }
                }

                $function_signature_string .= implode(', ',$function_signature_parameters).');';
            } else {
                $function_signature_string .= ');';
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


        // dump([
        //     // '$analysis_result' => $analysis_result,
        //     // '$analysis_result->used_namespaces' => $analysis_result->used_namespaces,
        //     // '$analysis_result->functions_signature' => $analysis_result->functions_signature,
        //     // '$used_namespaces_indexes' => $used_namespaces_indexes,
        //     '$used_namespaces_strings' => $used_namespaces_strings,
        //     '$function_signature_strings' => $function_signature_strings,
        // ]);

        return (object)[
            'strings' => (object)[
                'class_implemented_interfaces_namespaces' => $class_implemented_interfaces_namespaces_strings,
                'class_namespace' => $class_namespace_string,
                'class_name' => $analysis_result->class_name->name,
                'used_namespaces' => $used_namespaces_strings,
                'function_signatures' => $function_signature_strings,
            ]
        ];
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
