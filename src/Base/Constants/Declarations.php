<?php

namespace j0hnys\Trident\Base\Constants;

class Declarations
{
    const ENTITIES = [
        'WORKFLOW' => [
            'name' => 'workflow',
            'enabled' => true
        ],
        'BUSINESS' => [
            'name' => 'business',
            'enabled' => true
        ],
    ];
    const EVENTS = [
        'SUBSCRIBER' => [
            'name' => 'subscriber',
            'enabled' => true
        ],
        'TRIGGER_LISTENER' => [
            'name' => 'trigger_listener',
            'enabled' => true
        ],
    ];
    const PROCESS_TYPES = [
        'CASCADE' => [
            'name' => 'cascade',
            'enabled' => true
        ],
        'CASCADE_STATE_MACHINE' => [
            'name' => 'cascade_state_machine',
            'enabled' => true
        ],
        'STATE_MACHINE' => [
            'name' => 'state_machine',
            'enabled' => true
        ],
    ];
    const STRICT_TYPES = [
        'STRUCT' => [
            'name' => 'struct',
            'enabled' => true
        ],
        'COLLECTION_STRUCT' => [
            'name' => 'collection_struct',
            'enabled' => true
        ],
        'MAP_STRUCT' => [
            'name' => 'map_struct',
            'enabled' => true
        ],
        'STRUCT_OPTIONAL' => [
            'name' => 'struct_optional',
            'enabled' => true
        ],
    ];

    /**
     * @return array
     */
    public function get(): array {
        $oClass = new \ReflectionClass(__CLASS__);
        $constants = $oClass->getConstants();

        return $constants;
    }

    /**
     * @param [type] $needle
     * @param [type] $haystack
     * @param boolean $strict
     * @param array $path
     * @return array|bool
     */
    private function arraySearchRecursive($needle, $haystack, $strict = false, $path = [])
    {
        if ( !is_array($haystack) ) {
            return [];
        }
    
        foreach($haystack as $key => $value ) {
            if (is_array($value) && $subPath = $this->arraySearchRecursive($needle, $value, $strict, $path) ) {
                // $path = array_merge($path, [$value], $subPath);
                $path = array_merge($path, [$value]);
                return $path;
            } else if ( (!$strict && strpos($key, strtoupper($needle)) !== false ) || ($strict && $key === $needle) ) {
                $path []= $key;
                return $path;
            }
        }

        return [];
    }


    /**
     * @param string $constant
     * @return array
     */
    public function search(string $constant, bool $strict = false): array
    {
        $all_constants = $this->get();

        $result = [];
        foreach ($all_constants as $key => $value) {
            $result[$key] = $this->arraySearchRecursive($constant, ["$key" => $value], $strict);
            if (!empty($result[$key])) {
                $result[$key] = $result[$key][0];
            }
        }        

        return $result;
    }

    /**
     * @param string $constant
     * @return boolean
     */
    public function exist(string $constant, bool $strict = true): bool
    {
        $result = $this->search($constant, $strict);

        $exist = false;
        foreach ($result as $element) {
            if (!empty($element)) {
                $exist = true;
                break;
            }
        }

        return $exist;
    }

}