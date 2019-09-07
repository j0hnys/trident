<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Trident\Base\Definition\Definition;

class Functionality extends Definition
{
    const schema = [
        "model" => [
            "db_name" => "T::string()"
        ],
    ];

    const workflow = [
        "type" => "{{workflow_type}}",
        'schema' => [
            'initial_state' => 'T::string()',   //'draft'
            'states'        => 'T::array()',    //['draft', 'review', 'rejected', 'published']
            'transitions'   => [
                '{{workflow_transition}}' => [  //to_review
                    'from' => 'T::string()',    //'from' => 'draft',
                    'to'   => 'T::string()'     //'to'   => 'review'
                ],
                // 'publish' => [
                //     'from' => 'review',
                //     'to'   => 'published'
                // ],
                // 'reject_published' => [
                //     'from' => 'published',
                //     'to'   => 'rejected'
                // ]
            ],
            'transition_listeners' => [
                '{{workflow_transition}}' => '{{workflow_transition_listener}}',  //App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_1
                // 'publish' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_2',
                // 'reject_published' => 'App\Trident\Workflows\Processes\DemoProcessCascadeProcess@step_3'
            ],
        ]

    ];

    const workflow_type = [
        'cascade', 'cascade_state_machine', 'state_machine'
    ];

    const workflow_transition = 'T::string()';

    const workflow_transition_listener = 'T::string()';
}

