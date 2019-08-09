<?php

namespace App\Trident\Base\Typed\Collections;

use J0hnys\Typed\Collection;
use J0hnys\Typed\T;

class CollectionNumber extends Collection
{
    public function __construct()
    {
        parent::__construct(T::union(T::integer(), T::float()));
    }
}

