<?php

namespace j0hnys\Trident\Base\Constants;

class Declarations
{
    const ENTITIES = [
        'WORKFLOW' => true,
        'BUSINESS' => true
    ];
    const EVENTS = [
        'SUBSCRIBER' => true,
        'TRIGGER_LISTENER' => true
    ];
    const STRICT_TYPES = [
        'STRUCT' => true,
        'COLLECTION_STRUCT' => true,
        'MAP_STRUCT' => true,
        'STRUCT_OPTIONAL' => true,
    ];

    public function get() {
        $oClass = new \ReflectionClass(__CLASS__);
        
        return $oClass->getConstants();
    }
}