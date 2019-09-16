<?php

namespace j0hnys\Trident\Base\Constants\Trident;

use j0hnys\Definitions\Definition;

final class SchemaHierarchy extends Definition
{
    const hierarchy = [
        'trident' => [
            '{{entity_name}}' => [
                'FolderStructure' => '@\j0hnys\Trident\Base\Constants\Trident\FolderStructure',
                'Processes' => [
                    '@\j0hnys\Trident\Base\Constants\Trident\Process',
                ],
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

