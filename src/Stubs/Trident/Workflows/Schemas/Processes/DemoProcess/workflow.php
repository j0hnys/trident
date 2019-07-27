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