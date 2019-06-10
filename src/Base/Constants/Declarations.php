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

    /**
     * @return array
     */
    public function get(): array {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();

        return $constants;
    }


    function arraySearchRecursive($needle, $haystack, $strict = false, $path = [])
    {
        if ( !is_array($haystack) ) {
            return false;
        }
    
        foreach( $haystack as $key => $val ) {
            if (is_array($val) && $subPath = arraySearchRecursive($needle, $val, $strict, $path) ) {
                $path = array_merge($path, [$key], $subPath);
                return $path;
            } else if ( (!$strict && $val == $needle) || ($strict && $val === $needle) ) {
                $path []= $key;
                return $path;
            }
        }

        return false;
    }


    /**
     * @param string $constant
     * @return array
     */
    public function search(string $constant): array
    {
        $all_constants = $this->get();
        
        
    }

}