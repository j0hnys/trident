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