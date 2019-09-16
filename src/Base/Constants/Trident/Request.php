<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Definitions\Definition;

final class Request extends Definition
{
    const schema = [
        "type" => '{{request_type}}',
        "data" => [
            "{{entity_property}}" => [
                'type' => 'T::string()',
                'validation' => [
                    'rule' => '{{laravel_validation_rule_string}}',
                    'message' => 'T::string()',
                ],
                'fillable' => 'T::bool()',
            ]
        ],
    ];

    const request_type = [
        'json'
    ];
    const entity_property = 'T::string()';
    const laravel_validation_rule_string = 'T::string()';
}

