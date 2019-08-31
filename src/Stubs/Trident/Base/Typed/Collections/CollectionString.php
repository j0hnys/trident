<?php

namespace App\Trident\Base\Typed\Collections;

use J0hnys\Typed\Collection;
use J0hnys\Typed\T;

class CollectionString extends Collection
{
    public function __construct()
    {
        parent::__construct(T::String());
    }
}

