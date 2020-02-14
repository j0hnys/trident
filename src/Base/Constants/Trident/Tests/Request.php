<?php

namespace j0hnys\Trident\Base\Constants\Trident\Tests;

use j0hnys\Definitions\Definition;

final class Request extends Definition
{
    const schema = [
        "type" => '{{request_type}}',
        "data" => [
            "{{entity_property}}" => [
                'property_type' => '{{property_type}}',
                'value' => null,
            ]
        ],
    ];

    const request_type = [
        'json'
    ];
    const entity_property = 'T::string()';
    const property_type = [
        'default', 'auto_id', 'relation_id'
    ];
}

