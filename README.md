# trident

The purpose of this package is to assist developers building laravel applications using Domain Driven Design (DDD) and Test Driven Development (TDD) principles. The way it is achieved is by creating an application structure through CLI commands that create scaffolding code. 

The main rationale behind this is that the code generated takes care of all the wiring and architecture enforcing, that way DDD and TDD principles. This leaves developers more time to focus on implementing business logic. 

# Installation instructions

## to add to a laravel project as a package

`composer require j0hnys/trident`

## to install in laravel

after executing `php artisan trident:install` add 

```
App\Providers\TridentAuthServiceProvider::class,
App\Providers\TridentEventServiceProvider::class,
App\Providers\TridentRouteServiceProvider::class,
App\Providers\TridentServiceProvider::class,
```

to config/app



## Application architecture

### Folder structure

The package on install creates among others the following folder structure

```

|* app
    |- Http
    |- Models
    |- Policies
        |- Trident
    |- Providers
        |->Trident*.php
    |- Trident
        |- Base
        |- Business
            |- Events
            |- Exceptions 
            |- Logic 
            |- Schemas
            |- Validations
        |- Intefaces
        |- Workflows
            |- Events
            |- Exceptions
            |- Logic
            |- Processes
            |- Repositories
            |- Schemas
            |- Validations
```
Where `|* app` is the app directory of a laravel application.

The goal of this structure is to isolate the Domain Logic from application/infrastructure (as seen on DDD) and at the same time, reuse as many build-in laravel functionality and follow the general philosophy of doing things. This has lead to deviations from a purely DDD approach that simplify some of the structures and flow.

The first deviation is in definitions. There are `Workflows` and `Business`. The Workflows basically implement the workflow of a functionality (e.x. user list = 1. get db list, 2. process, 3. return). The Business implements domain processes (e.x. VAT evaluation), the concept is to have this functionality isolated and reusable.

The main differences between a pure DDD and the structure above are the following
 - Building blocks
   - the "Aggregates", "Value Objects", "Factories" are implemented by `StrictTypes` (described below) and eloquent-resources
   - "Entities" are implemented by laravel models and migrations
   - "Services" are implemented by "Workflows" -> "Logic" 
   - "Repositories" are implemented as an abstraction above laravel models
   - "Domain Events" are implemented by laravel events
 - Layers
   - "Application Layer" is implemented by `app/Http`, `app/Policies` folders basically 
   - "Infrastructure Layer" is implemented by laravel
   - "Domain Model Layer" by `app/Trident` folder
  
For more information about DDD you can reference [this](https://en.wikipedia.org/wiki/Domain-driven_design), [this](https://docs.microsoft.com/en-us/dotnet/architecture/microservices/microservice-ddd-cqrs-patterns/ddd-oriented-microservice) and [google](google.com)

Everything inside the `app/Trident` folder is Dependency Injected with Interfaces, the "bind" to laravel happens through dedicated providers in the `app/Providers` folder

The `app/Trident/Business` folder is for implementing domain processes as pure php classes. It is encouraged generally to write "thin" repositories, that only do reads/writes to persistant storage, and everything else put in the Business Layer  

The `app/Trident/Workflow/Processes` are for implementing the steps of one workflow process

All this complexity is handled by the package. Using the CLI commands all the necessary objects are created and binded together and to the framework. The developer implementes the functionality needed to the designated places, `Policies` is for authorization, `Events` are for events, `Repositories` are for repositories e.t.c.

## Basic usage

1. `php artisan make:migration create_demo_process`
2. fill in the appropriate data in the migration file
3. `php artisan migrate`
4. `php artisan trident:generate:workflow_restful_crud DemoProcess`

In the end of this process the following will be created:
- a new controller with restful CRUD functions will and placed in `app/Http/Controllers/Trident`
- a new resource in `routes/trident.php` behind the native authentication middleware
- a new model in `app/Models`, a new policy for this process and placed in `app/Policies/Trident` 
- a new exception class in `app/Trident/Workflows/Exceptions`
- a new set of validation (FormRequests) and placed in `app/Trident/Workflows/Validations`
- a new repository in `app/Trident/Workflows/Repositories`
- a new logic for this process in `app/Trident/Workflows/Logic` 
- finally a new business logic in `app/Trident/Business/Logic`

If we want to add a new function in the process, let's say a new feature we execute:

1. `php artisan trident:generate:workflow_logic_function DemoProcess [function_name]`

which will create automatically the appropriate functions and wiring in the controller, router, workflow logic, business logic, policy, validation. as seen below

Workflow:
```php
<?php

namespace App\Trident\Workflows\Logic;

use Illuminate\Http\Request;
use App\Trident\Workflows\Exceptions\TestEntityException;
use App\Trident\Interfaces\Workflows\Repositories\TestEntityRepositoryInterface as TestEntityRepository;
use App\Trident\Interfaces\Workflows\Logic\TestEntityInterface;
use App\Trident\Interfaces\Business\Logic\TestEntityInterface as TestEntityBusiness;

class TestEntity implements TestEntityInterface
{

    /**
     * @var TestEntityRepository
     */
    protected $test_entity_repository;

    /**
     * constructor.
     *
     * @var string
     * @return void
     */
    public function __construct(TestEntityBusiness $test_entity_business, TestEntityRepository $test_entity_repository)
    {
        $this->test_entity_repository = $test_entity_repository;
        $this->test_entity_business = $test_entity_business;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $data['user_id'] = auth()->id();
        
        $username = $data['username'];

        $username = $this->test_entity_business->add_suffix($username,'_edit');

        $data['username'] = $username;

        return $this->test_entity_repository->create($data);
    }


}

```

Business:
```php
<?php

namespace App\Trident\Business\Logic;

use App\Trident\Business\Exceptions\TestEntityException;
use App\Trident\Interfaces\Business\Logic\TestEntityInterface;


class TestEntity implements TestEntityInterface
{

    /**
     * constructor.
     *
     * @var string
     * @return void
     */
    public function __construct()
    {
       //
    }

    /**
     * put a suffix to a string.
     *
     * @param  string  $string
     * @return string
     */
    public function destroy(string $string, string $suffix): string
    {
        return $string.$suffix;
    }

}

```

Controller:
```php
<?php

namespace App\Http\Controllers\Trident;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Container\Container as App;
use App\Trident\Workflows\Validations\StoreRequest;
use App\Trident\Interfaces\Workflows\Logic\TestEntityInterface as TestEntityWorkflow;
use App\Trident\Interfaces\Workflows\Repositories\TestEntityRepositoryInterface as TestEntityRepository;
use App\Trident\Workflows\Exceptions\TestEntityException;

class TestEntityController extends Controller
{
    /**
     * @var TestEntity
     */
    protected $TestEntity;

    public function __construct(TestEntityWorkflow $TestEntityWorkflow, TestEntityRepository $test_entity_repository)
    {
        $this->test_entity_workflow = $TestEntityWorkflow;
        $this->test_entity_repository = $test_entity_repository;
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request)
    {
        $this->authorize('store',$this->test_entity_restful_crud_repository);
        return response()->json( $this->test_entity_workflow->store($request) );
    }

    
}

```

So in this example the `TestEntityController` calls `TestEntity` from workflow which calls `TestEntity` from logic. Authentication, authorization and validation are being done in the controller before we reach the workflow, the workflow interacts with the database (our only external source in this example) and the business only does logic. By this paradigm all concerns are seperated and isolated using native laravel functionallity (IoC DI, Exceptions, Policies, Validations, authentication) and we have a good structure for pure unit, integration, functional tests.


# Available artisan commands

| Command | Description | Parameters |
|---|---|---|
trident:build:migrations                      | Create all migrations from current database connection | {--output-path=}
trident:build:model_exports                   | Create all model exports from current models | {--output-path=}
trident:build:models                          | Create all models from current database connection | {--output-path=}
trident:export:model                          | export a models schema | {entity_name} {--output-path=}
trident:generate:business_logic_function      | Create a business logic function | {entity_name} {function_name}
trident:generate:controller_function          | Create a controller function | {entity_name} {function_name}
trident:generate:events                       | Create an event | {td_entity_type} {event_type} {td_entity_name}
trident:generate:exception                    | Create an exception | {td_entity_type} {td_entity_name}
trident:generate:factory                      | Create a factory for a model | {model} {--force}
trident:generate:policy_function              | Create a policy function | {entity_name} {function_name}
trident:generate:process                      | Create a process | {td_entity_name} {process_name} {schema_path}
trident:generate:resource                     | Create a resource | {entity_name} {--collection} {--workflow} {--schema_path=} {--force}
trident:generate:restful_crud                 | Create a RESTFUL CRUD | {name} {--model_db_name=} {--schema_path=}
trident:generate:strict_type                  | Create a strict type | {strict_type_name} {function_name} {entity_name} {--workflow} {--schema_path=} {--force}
trident:generate:validation                   | Create a validation | {entity_name} {function_name} {--schema_path=} {--force}
trident:generate:workflow                     | Create a workflow | {name}
trident:generate:workflow_function_process    | Create a workflow function process from existing workflow function | {entity_name} {type} {function_name} {schema_path}
trident:generate:workflow_logic_function      | Create a workflow logic function | {entity_name} {function_name}
trident:generate:workflow_restful_crud        | Create a workflow with the accompanied restful crud | {name} {--functionality_schema_path=} {--resource_schema_path=} {--validation_schema_path=} {--strict_type_schema_path=}
trident:generate:workflow_test_logic_function | Create workflow test logic function | {entity_name} {function_name}
trident:generate:workflow_tests               | Create workflow tests | {name}
trident:install                               | Trident installer | -
trident:refresh:class_interface               | Refreshes the interface that a class implements according to class functions | {name} {relative_input_path} {relative_output_path}
trident:refresh:class_interfaces              | Refreshes all the interfaces from the classes of a specific type/folder | {td_entity_type}
trident:refresh:di_binds                      | Refreshes DI containers binds | -
trident:refresh:workflow_restful_crud         | Refresh a workflow with the accompanied restful crud | {name} {--functionality_schema_path=} {--resource_schema_path=} {--validation_schema_path=} {--strict_type_schema_path=}
trident:remove:entity                         | Removes trident entity completely or a part. | {td_entity_name}
trident:remove:entity_function                | Removes trident entity's function with the structures connected to it. | {entity_name} {function_name}
trident:setup:tests                           | Trident test setup | -

