<?php

namespace Emma\Http\Mappings;

use Attribute;
use Emma\Http\Request\Method;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class PostMapping extends RequestMapping
{
    public function __construct(string $routes)
    {
        parent::__construct($routes, Method::POST);
    }

}