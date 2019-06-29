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

    public function __construct()
    {
        $this->mustache = new \Mustache_Engine;
        $this->storage_disk = new Disk();
        $this->storage_trident = new Trident();
    }

    /**
     * @param string $name
     * @param Command $command
     * @return void
     */
    public function generate(string $name = 'TEST', Command $command): void
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
        $output_path = './Models/';
        $table_name = Str::plural( Str::snake($name) );
        $table_name_singular = Str::snake($name);
        if (Schema::hasTable(lcfirst($table_name))) {
            // Generate model for existing table using plural table name 
            $command->call('krlove:generate:model', [
                'class-name' => ucfirst($name),
                '--output-path' => $output_path,
                '--table-name' => $table_name,
                '--namespace' => 'App\\Models',
                '--backup' => $this->storage_disk->fileExists($model_path),
            ]);
        } elseif (Schema::hasTable(lcfirst($table_name_singular))) {
            // Generate model for existing table using singular table name 
            $command->call('krlove:generate:model', [
                'class-name' => ucfirst($name),
                '--output-path' => $output_path,
                '--table-name' => $table_name_singular,
                '--namespace' => 'App\\Models',
                '--backup' => $this->storage_disk->fileExists($model_path),
            ]);
        } else {
            if (!$this->storage_disk->fileExists($model_path)) {
                $this->makeDirectory($model_path);

                $stub = $this->storage_disk->readFile(__DIR__ . '/../../Stubs/Crud/Model.stub');

                $stub = str_replace('{{td_entity}}', lcfirst($name), $stub);
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
