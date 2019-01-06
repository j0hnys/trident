<?php

namespace App\Trident\Base\Typed\Collections;

use Spatie\Typed\Collection;
use Spatie\Typed\T;

class CollectionNumber extends Collection
{
    public function __construct()
    {
        parent::__construct(T::union(T::integer(), T::float()));
    }
}

