<?php

namespace App\Trident\Base\Typed\Maps;

use Spatie\Typed\Map;
use Spatie\Typed\Struct;
use Spatie\Typed\T;

class MapStruct extends Map
{

    use \Spatie\Typed\ValidatesType;

    /**
     * valueType
     */
    protected $keyType;

    /**
     * valueType
     */
    protected $valueType;

    public function __construct($keyType, $valueType)
    {
        $this->keyType = $keyType;
        $this->valueType = $valueType;

        parent::__construct($this->keyType, T::struct($this->valueType));
    }

    public function set(array $data): Map
    {
        foreach ($data as $key => $value) {
            $key = $this->validateType($this->keyType, $key);
            $tmp = new Struct($this->valueType);
            $tmp->set($value);
            
            $this[$key] = $value;
        }
        
        return $this;
    }

    public function offsetSet($offset, $value)
    {
        $offset = $this->validateType($this->keyType, $offset);
        $tmp = new Struct($this->valueType);
        $tmp->set($value);

        $this->data[$offset] = ($value);
    }

}

