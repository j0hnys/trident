<?php

namespace j0hnys\Trident\Base\Definition;

use J0hnys\Typed\T;
use J0hnys\Typed\Struct;

class Definition
{

    /**
     * check if data matches definition
     *
     * @param array $data
     * @param array $haystack
     * @return void
     */
    public function check(array $data, string $definition_property = 'schema',  array $haystack = null, bool $is_path = false): void
    {
        $className = get_class($this);
        $oClass = new \ReflectionClass($className);
        $constants = $oClass->getConstants();

        if ($haystack === null) {
            $haystack = $constants[ $definition_property ];
        }

        foreach ($haystack as $key => $value) {
            if (!isset($data[$key]) && strpos($key, '{{') === false) {
                throw new \Exception('data key: '.$key.' is not set', 1);
            }

            if (is_array($value)) {
                if (strpos($key, '{{') === false) {
                    $this->check( $data[$key], $definition_property, $haystack[$key], $is_path );
                    if ($is_path) {
                        return;
                    }
                } else {
                    $array_values = array_values($data);
                    foreach ($array_values as $array_value) {
                        $this->check( $array_value, $definition_property, $haystack[$key], $is_path );
                        if ($is_path) {
                            return;
                        }
                    }
                }
            } 
            
            $key_definition_type = '';
            $value_definition_type = '';

            if (strpos($key, '{{') !== false) {
                $definition_type_name = str_replace('{{','',$key);
                $definition_type_name = str_replace('}}','',$definition_type_name);

                // dump([
                //     '$constants' => $constants,
                // ]);

                $key_definition_type = $constants[$definition_type_name];
                
                $array_keys = array_keys($data);

                if (is_array($key_definition_type)) {
                    foreach ($array_keys as $array_key) {
                        if (!in_array($array_key, $key_definition_type)) {
                            throw new \Exception("unknown type", 1);
                        }
                    }
                } else if (strpos($key_definition_type, 'T::') !== false) {
                    $key_definition_type = str_replace('T::', T::class.'::', $key_definition_type);
                    $result = eval('return '.$key_definition_type.';');

                    $struct_check = new Struct([
                        'value' => $result,
                    ]);

                    foreach ($array_keys as $array_key) {
                        $struct_check->set([
                            'value' => $array_key
                        ]);
                    }
                }
            }

            if (is_string($value)) {
                if (strpos($value, '{{') !== false) {
                    $definition_type_name = str_replace('{{','',$value);
                    $definition_type_name = str_replace('}}','',$definition_type_name);
    
                    $value_definition_type = $constants[$definition_type_name];

                    if (is_array($value_definition_type)) {
                        if (!in_array($data[$key], $value_definition_type)) {
                            throw new \Exception("unknown type", 1);
                        }
                    } else if (strpos($value_definition_type, 'T::') !== false) {
                        $value_definition_type = str_replace('T::', T::class.'::', $value_definition_type);
                        $result = eval('return '.$value_definition_type.';');

                        $struct_check = new Struct([
                            'value' => $result,
                        ]);
    
                        $struct_check->set([
                            'value' => $data[$key]
                        ]);
                    }    
                } else if (strpos($value, 'T::') !== false) {
                    $value = str_replace('T::', T::class.'::', $value);
                    $result = eval('return '.$value.';');

                    $struct_check = new Struct([
                        'value' => $result,
                    ]);

                    $check_key = $key;
                    if (is_array($key_definition_type)) {
                        $data_key = array_keys($data)[0];
                        if (in_array($data_key, $key_definition_type)) {
                            $check_key = $data_key;            
                        }
                    }

                    $struct_check->set([
                        'value' => $data[$check_key]
                    ]);
                }
            }
        }
    }


    public function checkPath(string $path, string $definition_property = 'schema'): void
    {
        $parts = explode('/', $path);   

        $parts_nested = [];
        $this->nestArray($parts, $parts_nested);

        $this->check($parts_nested, $definition_property, null, true);
    }

    private function nestArray(array $data, &$haystack)
    {
        for ($i=0,$ilength=count($data); $i<$ilength; $i++) { 
            $key = array_shift($data);
            $haystack[ $key ] = $this->nestArray($data, $haystack[$key]);
            if ($ilength == 1) {    //<-- to make the last element value, not nested array
                $haystack = $key;
            }
            break;
        }

        return $haystack;
    }


    /**
     * @return array
     */
    public function get(): array {
        $className = get_class($this);
        $oClass = new \ReflectionClass($className);
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

