<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Definitions\Definition;

final class Functionality extends Definition
{
    const schema = [
        "model" => [
            "db_name" => "T::string()"
        ],
    ];

    const workflow = [
        'workflow' => [
            "type" => "{{workflow_type}}",
            'schema' => [
                'initial_state' => 'T::string()',
                'states'        => 'T::array()',
                'transitions'   => [
                    '{{workflow_transition}}' => [
                        'from' => 'T::string()',
                        'to'   => 'T::string()'
                    ],
                ],
                'transition_listeners' => [
                    [
                        '{{workflow_transition}}' => '{{workflow_transition_listener}}',
                    ],
                ]
            ]
        ]
    ];

    const workflow_type = [
        'cascade', 'cascade_state_machine', 'state_machine'
    ];
    const workflow_transition = 'T::string()';
    const workflow_transition_listener = 'T::string()';
}

