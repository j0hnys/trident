<?php

namespace j0hnys\Trident\Builders\Crud;

use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;

class ControllerFunction
{
    private $storage_disk;

    public function __construct(Disk $storage_disk = null)
    {
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->folder_structure = new FolderStructure();
    }
    
    /**
     * @param string $td_entity_name
     * @param string $function_name
     * @return void
     */
    public function generate(string $td_entity_name, string $function_name): void
    {
        
        $name = ucfirst($td_entity_name).ucfirst($function_name);


        //
        //controller function generation
        $this->folder_structure->checkPath('app/Http/Controllers/Trident/*');
        $controller_path = $this->storage_disk->getBasePath().'/app/Http/Controllers/Trident/'.ucfirst($td_entity_name).'Controller.php';
        
        $lines = $this->storage_disk->readFileArray($controller_path); 
        $last = sizeof($lines) - 1 ; 
        unset($lines[$last]); 

        $this->storage_disk->writeFileArray($controller_path, $lines);
        
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/ControllerFunction.stub');

        $stub = str_replace('{{td_entity}}', lcfirst($td_entity_name), $stub);
        $stub = str_replace('{{Td_entity}}', ucfirst($td_entity_name), $stub);
        $stub = str_replace('{{function_name}}', ($function_name), $stub);
        $stub = str_replace('{{Function_name}}', ucfirst($function_name), $stub);
        
        $this->storage_disk->writeFile($controller_path, $stub, [
            'append_file' => true
        ]);

        //
        //update dependencies in use
        $this->addDependenciesInUses($controller_path, $td_entity_name, $function_name);
    }

    public function addDependenciesInUses(string $controller_path, string $td_entity_name, string $function_name): void
    {
        $code = $this->storage_disk->readFile($controller_path); 
        $lines = $this->storage_disk->readFileArray($controller_path); 

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
        $use_validation_string = 'use App\Trident\Workflows\Validations\\'.$td_entity_name.$function_name.'Request;'."\r\n";
        $use_struct_string = 'use App\Trident\Workflows\Schemas\Logic\\'.$td_entity_name.'\Typed\Struct'.ucfirst($function_name).$td_entity_name.';'."\r\n";

        $start_line = ($analysis_result->used_namespaces[count($analysis_result->used_namespaces)-1])->getStartLine();
        $end_line = ($analysis_result->used_namespaces[count($analysis_result->used_namespaces)-1])->getEndLine();

        array_splice($lines, $start_line, 0, $use_validation_string);
        array_splice($lines, ($start_line+1), 0, $use_struct_string);

        //update file
        $this->storage_disk->writeFileArray($controller_path, $lines); 
    }
    

}