<?php

namespace j0hnys\Trident\Builders\Remove;

use PhpParser\Error;
use PhpParser\NodeDumper;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;

class EntityFunction
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;

    public function __construct()
    {
        $this->storage_disk = new Disk();        
        $this->storage_trident = new Trident();
        $this->mustache = new \Mustache_Engine;
    }
    
    /**
     * Crud constructor.
     * @param string $name
     * @throws \Exception
     */
    public function run($name, $function_name)
    {        
        //
        //
        //workflow
        $workflow_input_path = $this->storage_disk->getBasePath().'/'.'app/Trident/Workflows/Logic';
        $code = $this->storage_disk->readFile( $workflow_input_path.'/'.$name.'.php' );
        $workflow_file = $this->storage_disk->readFileArray( $workflow_input_path.'/'.$name.'.php' );
        $workflow_result = $this->getClassFunctionSignature($code, $function_name);

        //remove connected files
        $used_namespace_paths = [];
        if (isset($workflow_result->function_signature_->used_namespaces_indexes)) {
            foreach ($workflow_result->function_signature_->used_namespaces_indexes as $used_namespaces_index) {
                $used_namespace = $workflow_result->used_namespaces[$used_namespaces_index]->name->parts;
                array_shift($used_namespace);
    
                $used_namespace_paths []= $this->storage_disk->getBasePath().'/'.'app/'.implode('/',$used_namespace).'.php'; 
            }
            foreach ($used_namespace_paths as $used_namespace_path) {
                $this->storage_disk->isFile($used_namespace_path) ? $this->storage_disk->deleteFile($used_namespace_path) : '';
            }
        }

        //remove workflow function
        if (isset($workflow_result->function_signature_)) {
            $starting_line = $workflow_result->function_signature_->lines->start-1;
            $ending_line = $workflow_result->function_signature_->lines->end;
            $lines_to_remove = range($starting_line,$ending_line);
    
            // Filter lines based on line number (+1 because the array is zero-indexed)
            $lines = array_filter($workflow_file, function($line_number) use ($lines_to_remove) {
                return !in_array($line_number, $lines_to_remove);
            }, ARRAY_FILTER_USE_KEY);
    
            $output = implode('', $lines);
            $output_file_path = $workflow_input_path.'/'.$name.'.php';
    
            // Write back to file
            $this->storage_disk->writeFile($output_file_path, $output);
        }
        //
        //
        //



        //
        //
        //controller
        $controller_input_path = $this->storage_disk->getBasePath().'/'.'app/Http/Controllers/Trident';
        $code = $this->storage_disk->readFile( $controller_input_path.'/'.$name.'Controller.php' );
        $controller_file = $this->storage_disk->readFileArray( $controller_input_path.'/'.$name.'Controller.php' );
        $controller_result = $this->getClassFunctionSignature($code, $function_name);

        //remove connected files
        $used_namespace_paths = [];       
        if (isset($controller_result->function_signature_->used_namespaces_indexes)) {
            foreach ($controller_result->function_signature_->used_namespaces_indexes as $used_namespaces_index) {
                $used_namespace = $controller_result->used_namespaces[$used_namespaces_index]->name->parts;
                array_shift($used_namespace);
    
                $used_namespace_paths []= $this->storage_disk->getBasePath().'/'.'app/'.implode('/',$used_namespace).'.php'; 
            }
            foreach ($used_namespace_paths as $used_namespace_path) {
                $this->storage_disk->isFile($used_namespace_path) ? $this->storage_disk->deleteFile($used_namespace_path) : '';
            }
        } 

        //remove workflow function
        if (isset($controller_result->function_signature_)) {
            $starting_line = $controller_result->function_signature_->lines->start-1;
            $ending_line = $controller_result->function_signature_->lines->end;
            $lines_to_remove = range($starting_line,$ending_line);
    
            // Filter lines based on line number (+1 because the array is zero-indexed)
            $lines = array_filter($controller_file, function($line_number) use ($lines_to_remove) {
                return !in_array($line_number, $lines_to_remove);
            }, ARRAY_FILTER_USE_KEY);
    
            $output = implode('', $lines);
            $output_file_path = $controller_input_path.'/'.$name.'Controller.php';
    
            // Write back to file
            $this->storage_disk->writeFile($output_file_path, $output);
        }
        //
        //
        //   

    }


    public function getClassFunctionSignature(string $code, string $function_name)
    {
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        $analysis_result = (object)[
            'class_namespace' => null,
            'class_name' => '',
            'used_namespaces' => [],
            'implemented_interfaces' => [],
            'function_signature_' => null,
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

                if ($node->name == $function_name) {
                    $tmp_function = (object)[
                        'flags' => $node->flags,  //dld public (1), protected (2), private (4), e.t.c.
                        'name' => $node->name,
                        'parameters' => [],
                        'return_type' => null,
                        'lines' => (object)[
                            'start' => -1,
                            'end' => $node->getAttributes()['endLine'],
                        ],
                        'used_namespaces_indexes' => [],
                    ];
                    
                    $function_comments = $node->getAttributes()['comments'];
                    foreach ($function_comments as $function_comment) {
                        if ($function_comment instanceof \PhpParser\Comment\Doc) {
                            $tmp_function->lines->start = $function_comment->getLine();
                        }
                    }

                    
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

                    if (!empty($node->returnType)) {
                        $tmp_function->return_type = $node->returnType;
                    }
                    
                    $analysis_result->function_signature_ = $tmp_function;
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

            array_pop($tmp_used_namespace->name->parts);
            $class_implemented_interfaces_namespaces_strings []= implode('\\', $tmp_used_namespace->name->parts );
        }


        //gia t interface
        $used_namespaces_indexes = [];
        //gia tis parametroys ths function
        if (isset($analysis_result->function_signature_->parameters)) {
            foreach ($analysis_result->function_signature_->parameters[0] as $parameter) {
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
                
            }
        }           

        //gia tn epistrefomenh time ths function
        if (isset($analysis_result->function_signature_->return_type)) {
            $type_name = $analysis_result->function_signature_->return_type->parts[ count($parameter->type->parts)-1 ];
            
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

        //sort $used_namespaces_indexes ascending order
        sort($used_namespaces_indexes);

        if (isset($analysis_result->function_signature_->used_namespaces_indexes)) {
            $analysis_result->function_signature_->used_namespaces_indexes = $used_namespaces_indexes;        
        }


        return $analysis_result;
    }



}