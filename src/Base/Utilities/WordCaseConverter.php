<?php

namespace j0hnys\Trident\Base\Utilities;

class WordCaseConverter
{
    public function camelCaseToSnakeCase(string $string): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
