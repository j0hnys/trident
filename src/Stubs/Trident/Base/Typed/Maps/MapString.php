<?php

namespace App\Trident\Base\Typed\Maps;

use J0hnys\Typed\Map;
use J0hnys\Typed\T;

class MapString extends Map
{
    public function __construct()
    {
        parent::__construct(T::string(),T::string());
    }
}

