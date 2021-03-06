<?php

namespace j0hnys\Trident;

use Illuminate\Support\ServiceProvider;
use j0hnys\Trident\Console\Commands\GenerateCrud;
use j0hnys\Trident\Console\Commands\GenerateControllerFunction;
use j0hnys\Trident\Console\Commands\GeneratePolicyFunction;
use j0hnys\Trident\Console\Commands\GenerateBusinessLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflowLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflow;
use j0hnys\Trident\Console\Commands\GenerateWorkflowFunctionProcess;
use j0hnys\Trident\Console\Commands\GenerateWorkflowTests;
use j0hnys\Trident\Console\Commands\GenerateWorkflowTestLogicFunction;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulCrud;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulCrudTests;
use j0hnys\Trident\Console\Commands\GenerateWorkflowRestfulFunctionTest;
use j0hnys\Trident\Console\Commands\GenerateFactory;
use j0hnys\Trident\Console\Commands\GenerateFactories;
use j0hnys\Trident\Console\Commands\Install;
use j0hnys\Trident\Console\Commands\SetupTests;
use j0hnys\Trident\Console\Commands\GenerateStrictType;
use j0hnys\Trident\Console\Commands\GenerateValidation;
use j0hnys\Trident\Console\Commands\GenerateException;
use j0hnys\Trident\Console\Commands\GenerateEvents;
use j0hnys\Trident\Console\Commands\ExportModel;
use j0hnys\Trident\Console\Commands\BuildMigrations;
use j0hnys\Trident\Console\Commands\BuildModels;
use j0hnys\Trident\Console\Commands\BuildModelExports;
use j0hnys\Trident\Console\Commands\GenerateResources;
use j0hnys\Trident\Console\Commands\GenerateResource;
use j0hnys\Trident\Console\Commands\RefreshDIBinds;
use j0hnys\Trident\Console\Commands\RefreshClassInterface;
use j0hnys\Trident\Console\Commands\RefreshClassInterfaces;
use j0hnys\Trident\Console\Commands\RefreshWorkflowRestfulCrud;
use j0hnys\Trident\Console\Commands\RefreshWorkflowLogicFunction;
use j0hnys\Trident\Console\Commands\RefreshWorkflowRestfulCrudTests;
use j0hnys\Trident\Console\Commands\RefreshWorkflowRestfulFunctionTest;
use j0hnys\Trident\Console\Commands\RefreshFactories;
use j0hnys\Trident\Console\Commands\RemoveEntity;
use j0hnys\Trident\Console\Commands\RemoveEntityFunction;
use j0hnys\Trident\Console\Commands\GenerateProcess;

// . . .

class TridentServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../config/trident.php';
        $this->publishes([
            $configPath => config_path('trident.php'),
        ], 'trident');

        $this->mergeConfigFrom($configPath, 'trident');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('trident:generate:crud', function ($app) {
            return new GenerateCrud();
        });
        $this->app->singleton('trident:generate:controller_function', function ($app) {
            return new GenerateControllerFunction();
        });
        $this->app->singleton('trident:generate:policy_function', function ($app) {
            return new GeneratePolicyFunction();
        });
        $this->app->singleton('trident:generate:business_logic_function', function ($app) {
            return new GenerateBusinessLogicFunction();
        });
        $this->app->singleton('trident:generate:workflow_logic_function', function ($app) {
            return new GenerateWorkflowLogicFunction();
        });
        $this->app->singleton('trident:generate:workflow', function ($app) {
            return new GenerateWorkflow();
        });
        $this->app->singleton('trident:generate:workflow_function_process', function ($app) {
            return new GenerateWorkflowFunctionProcess();
        });
        $this->app->singleton('trident:generate:workflow_tests', function ($app) {
            return new GenerateWorkflowTests();
        });
        $this->app->singleton('trident:generate:workflow_test_logic_function', function ($app) {
            return new GenerateWorkflowTestLogicFunction();
        });
        $this->app->singleton('trident:generate:workflow_restful_crud', function ($app) {
            return new GenerateWorkflowRestfulCrud();
        });
        $this->app->singleton('trident:generate:workflow_restful_crud_tests', function ($app) {
            return new GenerateWorkflowRestfulCrudTests();
        });
        $this->app->singleton('trident:generate:workflow_restful_function_test', function ($app) {
            return new GenerateWorkflowRestfulFunctionTest();
        });
        $this->app->singleton('trident:generate:factory', function ($app) {
            return new GenerateFactory();
        });
        $this->app->singleton('trident:generate:factories', function ($app) {
            return new GenerateFactories();
        });
        $this->app->singleton('trident:install', function ($app) {
            return new Install();
        });
        $this->app->singleton('trident:setup:tests', function ($app) {
            return new SetupTests();
        });
        $this->app->singleton('trident:generate:strict_type', function ($app) {
            return new GenerateStrictType();
        });
        $this->app->singleton('trident:generate:validation', function ($app) {
            return new GenerateValidation();
        });
        $this->app->singleton('trident:generate:exception', function ($app) {
            return new GenerateException();
        });
        $this->app->singleton('trident:generate:events', function ($app) {
            return new GenerateEvents();
        });
        $this->app->singleton('trident:export:model', function ($app) {
            return new ExportModel();
        });
        $this->app->singleton('trident:build:migrations', function ($app) {
            return new BuildMigrations();
        });
        $this->app->singleton('trident:build:models', function ($app) {
            return new BuildModels();
        });
        $this->app->singleton('trident:build:model_exports', function ($app) {
            return new BuildModelExports();
        });
        $this->app->singleton('trident:generate:resources', function ($app) {
            return new GenerateResources();
        });
        $this->app->singleton('trident:generate:resource', function ($app) {
            return new GenerateResource();
        });
        $this->app->singleton('trident:refresh:di_binds', function ($app) {
            return new RefreshDIBinds();
        });
        $this->app->singleton('trident:refresh:class_interface', function ($app) {
            return new RefreshClassInterface();
        });
        $this->app->singleton('trident:refresh:class_interfaces', function ($app) {
            return new RefreshClassInterfaces();
        });
        $this->app->singleton('trident:refresh:workflow_restful_crud', function ($app) {
            return new RefreshWorkflowRestfulCrud();
        });
        $this->app->singleton('trident:refresh:workflow_logic_function', function ($app) {
            return new RefreshWorkflowLogicFunction();
        });
        $this->app->singleton('trident:refresh:workflow_restful_crud_tests', function ($app) {
            return new RefreshWorkflowRestfulCrudTests();
        });
        $this->app->singleton('trident:refresh:workflow_restful_function_test', function ($app) {
            return new RefreshWorkflowRestfulFunctionTest();
        });
        $this->app->singleton('trident:refresh:factories', function ($app) {
            return new RefreshFactories();
        });
        $this->app->singleton('trident:remove:entity', function ($app) {
            return new RemoveEntity();
        });
        $this->app->singleton('trident:remove:entity_function', function ($app) {
            return new RemoveEntityFunction();
        });
        $this->app->singleton('trident:remove:process', function ($app) {
            return new GenerateProcess();
        });
        // . . .

        $this->commands([
            'trident:generate:crud',
            'trident:generate:controller_function',
            'trident:generate:policy_function',
            'trident:generate:business_logic_function',
            'trident:generate:workflow_logic_function',
            'trident:generate:workflow',
            'trident:generate:workflow_function_process',
            'trident:generate:workflow_tests',
            'trident:generate:workflow_test_logic_function',
            'trident:generate:workflow_restful_crud',
            'trident:generate:workflow_restful_crud_tests',
            'trident:generate:workflow_restful_function_test',
            'trident:generate:factory',
            'trident:generate:factories',
            'trident:generate:validation',
            'trident:generate:strict_type',
            'trident:generate:exception',
            'trident:generate:events',
            'trident:install',
            'trident:setup:tests',
            'trident:export:model',
            'trident:build:migrations',
            'trident:build:models',
            'trident:build:model_exports',
            'trident:generate:resources',
            'trident:generate:resource',
            'trident:refresh:di_binds',
            'trident:refresh:class_interface',
            'trident:refresh:class_interfaces',
            'trident:refresh:workflow_restful_crud',
            'trident:refresh:workflow_logic_function',
            'trident:refresh:workflow_restful_crud_tests',
            'trident:refresh:workflow_restful_function_test',
            'trident:refresh:factories',
            'trident:remove:entity',
            'trident:remove:entity_function',
            'trident:remove:process',
            // . . .
        ]);
    }

}