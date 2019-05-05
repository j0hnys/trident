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

        dump('faseis');

        $name = 'DemoProcess';

        $code = file_get_contents( base_path().'/app/Trident/Workflows/Logic/'.ucfirst($name).'.php' );

        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return;
        }

        // $dumper = new NodeDumper;
        // dump($ast[0]->exprs);
        // echo $dumper->dump($ast) . "\n";


        $analysis_result = (object)[
            'used_namespaces' => [],
            'constructor_params' => [],
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result){
            if ($node instanceof Node\Stmt\Use_) {
                // dump([
                //     // '$node->getAttributes()' => $node->getAttributes(),
                //     // '$node->getSubNodeNames()' => $node->getSubNodeNames(),
                //     // '$node->getLine()' => $node->getLine(),
                //     // '$node->getType()' => $node->getType(),
                //     // '$node->uses[0]->name->parts' => $node->uses[0]->name->parts,
                //     '$node' => $node,
                //     // '$node->uses[0]->name->parts' => $node->uses[0]->name->parts,
                //     // '$node->uses[0]->alias' => $node->uses[0]->alias,
                // ]);

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

        dump([
            '$analysis_result' => $analysis_result,
        ]);

        $di_interfaces = [];

        foreach ($analysis_result->constructor_params as $constructor_param) {
            foreach ($analysis_result->used_namespaces as $used_namespace) {
                if (count($constructor_param->type->parts) == 1) {  //dld exw alias
                    if (!empty($used_namespace->alias)) {
                        if ($used_namespace->alias == $constructor_param->type->parts[0]) {
                            // dump('USED INTERFACE FOUND!! '.$used_namespace->alias.' | '.implode('\\',$used_namespace->name->parts));
                            $di_interfaces []= [
                                'name' => $used_namespace->name
                            ];
                        }
                    }
                } else {
                    // TO DO
                }
            }

        }

        dump([
            '$di_interfaces' => $di_interfaces,
        ]);



        // dump($code);

        // $traverser = new NodeTraverser;
        // $traverser->addVisitor(new class extends NodeVisitorAbstract {
        //     public function leaveNode(Node $node) {

        //         dump([
        //             '$node' => $node,
        //         ]);

        //         // if ($node instanceof Node\Scalar\LNumber) {
        //         //     return new Node\Scalar\String_((string) $node->value);
        //         // }
        //     }
        // });
        // $modifiedStmts = $traverser->traverse($ast);








        // //
        // //workflow logic test generation
        // $workflow_logic_test_path = base_path().'/tests/Trident/Workflows/Logic/'.ucfirst($name).'Test.php';
        
        // $class_name = '\\App\\Trident\\Workflows\\Logic\\'.ucfirst($name);
        // $class_methods = \get_class_methods( $class_name );
        // $class_methods = array_map(function($element){
        //     return [
        //         'method' => ($element),
        //     ];
        // },array_values(array_filter($class_methods,function($element){
        //     return $element != '__construct' ? true : false;
        // })));

        // if (!file_exists($workflow_logic_test_path)) {
        //     $this->makeDirectory($workflow_logic_test_path);

        //     $stub = file_get_contents(__DIR__.'/../../Stubs/tests/Trident/Workflows/Logic/Logic.stub');

        //     $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        //     $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        //     $stub = $mustache->render($stub, [
        //         'methods' => $class_methods,
        //     ]);
            
        //     file_put_contents($workflow_logic_test_path, $stub);
        // }

        // //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=
        // //now we build the business logic part of this workflow
        // //-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

        // //
        // //workflow logic test generation
        // $business_logic_test_path = base_path().'/tests/Trident/Business/Logic/'.ucfirst($name).'Test.php';
        
        // $class_name = '\\App\\Trident\\Business\\Logic\\'.ucfirst($name);
        // $class_methods = \get_class_methods( $class_name );
        // $class_methods = array_map(function($element){
        //     return [
        //         'method' => ($element),
        //     ];
        // },array_values(array_filter($class_methods,function($element){
        //     return $element != '__construct' ? true : false;
        // })));

        // if (!file_exists($business_logic_test_path)) {
        //     $this->makeDirectory($business_logic_test_path);

        //     $stub = file_get_contents(__DIR__.'/../../Stubs/tests/Trident/Business/Logic/Logic.stub');

        //     $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
        //     $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
        //     $stub = $mustache->render($stub, [
        //         'methods' => $class_methods,
        //     ]);
            
        //     file_put_contents($business_logic_test_path, $stub);
        // }


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
