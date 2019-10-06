<?php

namespace j0hnys\Trident\Builders\Refresh;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

use j0hnys\Trident\Base\Storage\Disk;

class ClassInterface
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
    }
    
    /**
     * @param string $name
     * @param string $relative_input_path
     * @param string $relative_output_path
     * @return void
     */
    public function run(string $name, string $relative_input_path, string $relative_output_path): void
    {
        $input_path = $this->storage_disk->getBasePath().'/'.$relative_input_path;
        $output_path = $this->storage_disk->getBasePath().'/'.$relative_output_path;

        $code = $this->storage_disk->readFile( $input_path.'/'.$name.'.php' );
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


        $stub = $this->storage_disk->readFile(__DIR__.'/../../../src/Stubs/PHP/Interface.stub');
        $stub = $this->mustache->render($stub, [
            'namespace' => $namespace,
            'used_namespaces' => $used_namespaces,
            'class_name' => $class_name,
            'function_signatures' => $function_signatures,
        ]);
        
        $this->storage_disk->makeDirectory($output_path.'/'.$name.'Interface.php'); 

        $this->storage_disk->writeFile($output_path.'/'.$name.'Interface.php', $stub);
    }

    /**
     * @param string $code
     * @return Object
     */
    public function getClassFunctionSignatures(string $code): Object
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

                if (isset($node->returnType)) {
                    if (isset($node->returnType->name)) {
                        $tmp_function->return_type = $node->returnType->name;
                    } elseif (isset($node->returnType->parts)) {
                        $tmp_function->return_type = $node->returnType->parts[ count($node->returnType->parts)-1 ];
                    }
                }
                
                $analysis_result->functions_signature []= $tmp_function;
            }

        });

        $class_namespace_string = '';
        if (isset($analysis_result->class_namespace)) {
            array_splice($analysis_result->class_namespace->parts, 2, 0, 'Interfaces'); // splice in at position 2
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
            } elseif ($function_signature->flags == 2) {
                $function_signature_string .= 'protected ';
            } elseif ($function_signature->flags == 4) {
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
                    } elseif (isset($parameter->var)) {
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
            } else {
                $function_signature_string .= ';';
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



}
