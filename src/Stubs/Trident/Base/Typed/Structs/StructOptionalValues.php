<?php

namespace App\Trident\Base\Typed\Structs;

use J0hnys\Typed\Struct;

class StructOptionalValues extends Struct
{
    /** @var array */
    protected $types = [];

    public function set(array $data): Struct
    {
        $values = [];

        foreach (array_keys($this->types) as $key) {
            $value = isset($data[$key]) ? $data[$key] : null;
            $values[$key] = $value;
        }

        return parent::set($values);
    }

    public function getFilledValues(): array
    {
        $values = [];
        $all = $this->toArray();

        foreach ($all as $key => $value) {
            if ($value != null) {
                $values[$key] = $value;
            }
        }

        return $values;
    }
}
