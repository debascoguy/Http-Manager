<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Http\Mappings\RequestMapping;

class RoutableAttributes
{
    /**
     * @param array|\Attribute[] $attributes
     * @return array
     */
    public static function filter(array $attributes): array
    {
        return array_filter($attributes, function ($attr) {
            return $attr->newInstance() instanceof RequestMapping;
        });
    }

}