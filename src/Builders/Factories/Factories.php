<?php

namespace j0hnys\Trident\Builders\Factories;

use Illuminate\Console\Command;
use PhpParser\Error;
use PhpParser\ParserFactory;
use PhpParser\{Node, NodeFinder};
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;


class Factories
{
    
    /**
     * laravel instance from command
     */
    protected $laravel;

    private $storage_disk;
    private $models_path;

    public function __construct(Disk $storage_disk = null, string $models_path = '')
    {
        $this->storage_disk = new Disk();
        $this->models_path = app_path('Models');
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        if (!empty($models_path)) {
            $this->models_path = $models_path;
        }
        $this->mustache = new \Mustache_Engine;
        $this->folder_structure = new FolderStructure();
    }

    /**
     * @param mixed $laravel
     * @param string $model
     * @return void
     */
    public function generate($laravel, $command, bool $force = false): void
    {
        $this->laravel = $laravel;

        $model_names = $this->getModels($this->models_path);
        $model_classpaths = [];

        foreach ($model_names as $model_name) {
            $model_classpath = $this->getClassNamespace( $this->models_path.'/'.$model_name.'.php' ).'\\'.$model_name;

            $this->generateOther($model_classpath, $command);

            $model_classpaths []= $model_classpath;
        }
    }


    /**
     * @param string $path
     * @return array
     */
    private function getModels(string $path): array
    {
        $files = $this->storage_disk->getFolderFiles($path);

        return $files;
    }


    /**
     * @param string $path
     * @return string
     */
    private function getClassNamespace(string $path): string
    {
        $code = $this->storage_disk->readFile($path);
        
        $parser = (new ParserFactory)->create(ParserFactory::PREFER_PHP7);
        try {
            $ast = $parser->parse($code);
        } catch (Error $error) {
            echo "Parse error: {$error->getMessage()}\n";
            return (object)[];
        }

        $analysis_result = (object)[
            'class_namespace' => null,
        ];

        $nodeFinder = new NodeFinder;
        $nodeFinder->find($ast, function(Node $node) use (&$analysis_result){
            if ($node instanceof Node\Stmt\Namespace_) {
                $analysis_result->class_namespace = $node->name;
            }
        });

        return $analysis_result->class_namespace;
    }


    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generateOther(string $model_classpath, Command $command): void
    {
        //new model factory
        $command->call('trident:generate:factory', [
            'model' => $model_classpath,
        ]);
    }


}