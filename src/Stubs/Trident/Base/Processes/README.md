# Cascade Machine

Cascade Machine is a state machine that executes the transitions of a trident-workflow (based on Symfony's Workflow) sequentialy.

The main concept goes as follows:
 1. Every workflow function can have a state machine that describes the steps that this function executes (workflow) through two files (`workflow.php`, `cascade_process.php`) placed in `app\Trident\Workflows\Schemas\Processes\<td entity name>\` 

    e.x.
    workflow.php
    ```php
    <?php

    return [
        'index'   => [
            'type'          => 'workflow',
            'supports' => ['App\Trident\Base\Processes\Models\DefaultMarking'],
            'places'        => ['draft', 'review', 'rejected', 'published'],
            'transitions'   => [
                'to_review' => [
                    'from' => 'draft',
                    'to'   => 'review'
                ],
                'publish' => [
                    'from' => 'review',
                    'to'   => 'published'
                ],
                'reject_published' => [
                    'from' => 'published',
                    'to'   => 'rejected'
                ]
            ],
        ]
    ];
    ```
    workflow must have transition that connect to each other sequentially and supports `App\Trident\Base\Processes\Models\DefaultMarking`

    cascade_process.php
    ```php
    <?php

    return [
        'index'   => [
            'initial_place' => 'draft',
            'transitions'   => [
                'to_review' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_1',
                'publish' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_2',
                'reject_published' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_3'
            ],
        ]
    ];
    ```
    Describes the functions that are going to be executed in every transition.

 2. The function that is executed upon transition must take as parameters the return values from the function executed in the previous step. When starting the process we set the `starting_data`

    e.x.
    ```php
    class DemoProcessCascadeProcess
    {

        public function __construct(array $something_DIed = [])
        {
            // code
        }

        public function step_1(array $data): array 
        {
            // code  
            return ['one'];
        }

        public function step_2(array $data): array
        {
            // code 
            return ['two'];
        }

        public function step_3(array $data): array
        {
            // code
            return ['three'];
        }

    }

    ```

e.x. of use
```php
. . . 

$cascade_machine = CascadeMachine::getInstance();
$cascade_machine->boot();
$cascade_machine->initializeWorkflow(<td_entity_workflow>::class, '<td_entity_workflow function name>', <any data>);
$cascade_machine->runWorkflow();

. . . 
```
The `boot` function preferably should be executed in the beginning of the request once (it reads the file system to include everything inside `app\Trident\Workflows\Schemas\Processes`). 