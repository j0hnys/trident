<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Trident\Base\Definition\Definition;

class SchemaHierarchy extends Definition
{
    const hierarchy = [
        'trident' => [
            '{{entity_name}}' => [
                'Processes',
                'Resource' => [
                    '@\j0hnys\Trident\Base\Constants\Trident\Functionality',
                    '@\j0hnys\Trident\Base\Constants\Trident\Request',
                    '@\j0hnys\Trident\Base\Constants\Trident\Response',
                ]
            ],
        ],
    ];

    const entity_name = 'T::string()';
    
}

