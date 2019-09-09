<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Trident\Base\Definition\Definition;

class Response extends Definition
{
    const schema = [
        "type" => '{{request_type}}',
        "data" => [
            "{{entity_property}}" => [
                'resource' => 'T::bool()',
            ]
        ],
    ];

    const request_type = [
        'json'
    ];

    const entity_property = 'T::string()';
    
}

