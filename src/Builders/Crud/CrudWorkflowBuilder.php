<?php

namespace j0hnys\Trident\Builders\Crud;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use j0hnys\Trident\Base\Storage\Disk;
use j0hnys\Trident\Base\Storage\Trident;

class CrudWorkflowBuilder
{

    private $mustache;
    private $storage_disk;
    private $storage_trident;

    public function __construct(Disk $storage_disk = null, Trident $storage_trident = null)
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        if (!empty($storage_disk)) {
            $this->storage_disk = $storage_disk;
        }
        $this->storage_trident = new Trident();
        if (!empty($storage_trident)) {
            $this->storage_trident = $storage_trident;
        }
    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generate(string $name = 'TEST', string $model_db_name = '', string $schema_path = '', Command $command): void
    {        
        //
        //controller generation
        $controller_path = $this->storage_disk->getBasePath() . '/app/Http/Controllers/Trident/' . ucfirst($name) . 'Controller.php';

        if (!$this->storage_disk->fileExists($controller_path)) {
            $this->storage_disk->makeDirectory($controller_path);

            $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/Crud/ControllerCrudWorkflow.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);

            $this->storage_disk->writeFile($controller_path, $stub);
        }

        //
        // repository generation is in workflow generation
        //

        //
        //model generation
        $model_path = $this->storage_disk->getBasePath() . '/app/Models/' . ucfirst($name) . '.php';
        $output_path = str_replace('C:\\','/',$this->storage_disk->getBasePath().'/app/Models/');
        if (empty($model_db_name)) {
            $model_db_name = lcfirst($name);
        }

        if (Schema::hasTable($model_db_name)) {
            // Generate model for existing table using plural table name 
            $command->call('krlove:generate:model', [
                'class-name' => ucfirst($name),
                '--output-path' => $output_path,
                '--table-name' => $model_db_name,
                '--namespace' => 'App\\Models',
                '--backup' => $this->storage_disk->fileExists($model_path),
            ]);
        } else {
            if (!$this->storage_disk->fileExists($model_path)) {
                $this->storage_disk->makeDirectory($model_path);

                $schema = [];
                if (!empty($schema_path)) {
                    $schema = json_decode( $this->storage_disk->readFile( $schema_path ),true);
                }

                $fillables = [];
                if (!empty($schema)) {
                    foreach ($schema as $key => $data) {
                        if (isset($data['fillable'])) {
                            if ($data['fillable']) {
                                $fillables []= '\''.$key.'\'';
                            }
                        }
                    }
                }

                $stub = $this->storage_disk->readFile(__DIR__.'/../../Stubs/Crud/Model.stub');

                $stub = str_replace('{{db_name}}', $model_db_name, $stub);
                $stub = str_replace('{{fillables_comma_separated}}', implode(', ',$fillables), $stub);
                $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);

                $this->storage_disk->writeFile($model_path, $stub);
            }
        }


        //
        //update resource routes
        $Td_entities_workflows = $this->storage_trident->getCurrentControllers();

        $workflows = array_map(function ($element) {
            return [
                'Td_entity' => ucfirst($element),
                'td_entity' => lcfirst($element),
            ];
        }, $Td_entities_workflows);

        $trident_resource_routes_path = $this->storage_disk->getBasePath() . '/routes/trident.php';
        $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/routes/trident.stub');
        $stub = $this->mustache->render($stub, [
            'register_resource_routes' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_resource_routes_path, $stub);

        //
        //update trident auth provider
        $trident_auth_provider_path = $this->storage_disk->getBasePath() . '/app/Providers/TridentAuthServiceProvider.php';
        $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/app/Providers/TridentAuthServiceProvider.stub');
        $stub = $this->mustache->render($stub, [
            'register_workflow_policies' => $workflows,
        ]);

        $this->storage_disk->writeFile($trident_auth_provider_path, $stub);

        //
        //policy generation
        $trident_policy_path = $this->storage_disk->getBasePath() . '/app/Policies/Trident/' . ucfirst($name) . 'Policy.php';
        if (!$this->storage_disk->fileExists($trident_policy_path)) {
            $this->storage_disk->makeDirectory($trident_policy_path);

            $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/app/Policies/Trident/LogicPolicy.stub');

            $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
            $stub = str_replace('{{Td_entity}}', ucfirst($name), $stub);

            $this->storage_disk->writeFile($trident_policy_path, $stub);
        }
    }


}
