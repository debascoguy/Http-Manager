<?php

namespace Emma\Http\Router\Interfaces;

use Emma\Http\Request\RequestInterface;

interface RouteMatcherInterface
{
    public function match(RequestInterface $httpRequest, RouterStoreInterface $httpRouter): ?RouteInterface;

}