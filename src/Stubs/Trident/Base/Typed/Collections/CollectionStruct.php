<?php

namespace App\Trident\Base\Typed\Collections;

use Spatie\Typed\Collection;
use Spatie\Typed\Struct;
use Spatie\Typed\T;

class CollectionStruct extends Collection
{

    /**
     * Type
     */
    protected $type;

    public function __construct($type)
    {
        $this->type = $type;

        parent::__construct(T::struct());
    }

    public function set(array $data): Collection
    {
        $tmp = new Struct($this->type);
        foreach ($data as $element) {
            $tmp->set($element);
        }
        $this[] = $data;
        
        return $this;
    }

    public function offsetSet($offset, $value)
    {
        $tmp = new Struct($this->type);
        foreach ($value as $key => $element) {
            $tmp->set($element);
        }

        if (is_null($offset)) {
            $this->data[] = ($value);
        } else {
            $this->data[$offset] = ($value);
        }
    }

}

