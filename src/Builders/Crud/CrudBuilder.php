<?php

namespace j0hnys\Trident\Builders\Crud;

use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;
use j0hnys\Trident\Base\Constants\Trident\Request;
use j0hnys\Trident\Base\Constants\Trident\FolderStructure;
use j0hnys\Trident\Base\Utilities\WordCaseConverter;

class CrudBuilder
{
    private $storage_disk;
    private $storage_trident;
    private $mustache;
    private $word_case_converter;

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
        $this->request_definition = new Request();
        $this->folder_structure = new FolderStructure();
        $this->word_case_converter = new WordCaseConverter();
    }
    
    /**
     * @param string $name
     * @return void
     */
    public function generate(string $name, string $model_db_name = '', string $schema_path = ''): void
    {
        

        //
        //controller generation
        $this->folder_structure->checkPath('app/Http/Controllers/Trident/*');
        $controller_path = $this->storage_disk->getBasePath().'/app/Http/Controllers/Trident/'.ucfirst(strtolower($name)).'Controller.php';
        
        if (!$this->storage_disk->fileExists($controller_path)) {
            $this->storage_disk->makeDirectory($controller_path);

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/Controller.stub');

            $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($controller_path, $stub);
        }

        //
        // repository generation is in workflow generation
        //

        //
        //model generation
        $this->folder_structure->checkPath('app/Models/*');
        $model_path = $this->storage_disk->getBasePath().'/app/Models/'.ucfirst($name).'.php';
        
        if (!$this->storage_disk->fileExists($model_path)) {
            $this->storage_disk->makeDirectory($model_path);

            $schema = [];
            if (!empty($schema_path)) {
                $schema = json_decode( $this->storage_disk->readFile( $schema_path ),true);
                $this->request_definition = new Request();
            }

            $fillables = [];
            if (!empty($schema)) {
                foreach ($schema['data'] as $key => $data) {
                    if (isset($data['fillable'])) {
                        if ($data['fillable']) {
                            $fillables []= '\''.$key.'\'';
                        }
                    }
                }
            }

            if (empty($model_db_name)) {
                $model_db_name = $this->word_case_converter->camelCaseToSnakeCase($name);
            }

            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/Model.stub');

            $stub = str_replace('{{db_name}}', $model_db_name, $stub);
            $stub = str_replace('{{fillables_comma_separated}}', implode(', ',$fillables), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($model_path, $stub);
        }

        //
        //update resource routes
        $Td_entities_workflows = $this->storage_trident->getCurrentControllers();
        
        $workflows = array_map(function($element){
            return [
                'Td_entity' => ucfirst($element),
                'td_entity' => $this->word_case_converter->camelCaseToSnakeCase($element),
            ];
        },$Td_entities_workflows);

        $this->folder_structure->checkPath('routes/trident.php');
        $trident_resource_routes_path = $this->storage_disk->getBasePath().'/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_resource_routes_path, $stub);

        //
        //update trident auth provider
        $this->folder_structure->checkPath('app/Providers/TridentAuthServiceProvider.php');
        $trident_auth_provider_path = $this->storage_disk->getBasePath().'/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => $workflows,
        ]);
        
        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);

        //
        //policy generation
        $this->folder_structure->checkPath('app/Policies/Trident/*');
        $trident_policy_path = $this->storage_disk->getBasePath().'/app/Policies/Trident/'.ucfirst($name).'Policy.php';
        if (!file_exists($trident_policy_path)) {
            $this->storage_disk->makeDirectory($trident_policy_path);
            
            $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/app/Policies/Trident/LogicPolicy.stub');
            
            $stub = str_replace('{{td_entity}}', $this->word_case_converter->camelCaseToSnakeCase($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);
            
            $this->storage_disk->writeFile($trident_policy_path, $stub);
        }
            
    }

    
}
