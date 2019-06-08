<?php

namespace j0hnys\Trident\Base\Constants;

class Declarations
{
    const TRIDENT = [
        'ENTITIES' => [
            'WORKFLOW',
            'BUSINESS'
        ]
    ];

    function showConstant() {
        dump(self::TRIDENT);
    }
}