<?php

namespace j0hnys\Trident\Base\Constants;

use j0hnys\Definitions\Definition;

final class Declarations extends Definition
{
    const ENTITIES = [
        'WORKFLOW' => [
            'name' => 'workflow',
            'enabled' => true
        ],
        'BUSINESS' => [
            'name' => 'business',
            'enabled' => true
        ],
    ];
    const EVENTS = [
        'SUBSCRIBER' => [
            'name' => 'subscriber',
            'enabled' => true
        ],
        'TRIGGER_LISTENER' => [
            'name' => 'trigger_listener',
            'enabled' => true
        ],
    ];
    const PROCESS_TYPES = [
        'CASCADE' => [
            'name' => 'cascade',
            'enabled' => true
        ],
        'CASCADE_STATE_MACHINE' => [
            'name' => 'cascade_state_machine',
            'enabled' => true
        ],
        'STATE_MACHINE' => [
            'name' => 'state_machine',
            'enabled' => true
        ],
    ];
    const STRICT_TYPES = [
        'STRUCT' => [
            'name' => 'struct',
            'enabled' => true
        ],
        'COLLECTION_STRUCT' => [
            'name' => 'collection_struct',
            'enabled' => true
        ],
        'MAP_STRUCT' => [
            'name' => 'map_struct',
            'enabled' => true
        ],
        'STRUCT_OPTIONAL' => [
            'name' => 'struct_optional',
            'enabled' => true
        ],
        'STRUCT_OPTIONAL_SHOW' => [
            'name' => 'struct_optional_show',
            'enabled' => true
        ],
        'STRUCT_OPTIONAL_WORKFLOW_FUNCTION' => [
            'name' => 'struct_optional_workflow_function',
            'enabled' => true
        ],
    ];
    
}