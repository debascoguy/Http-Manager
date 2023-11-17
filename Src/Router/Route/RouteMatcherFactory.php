<?php

namespace Emma\Http\Router\Route;

use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;
use Emma\Http\Request\RequestInterface;
use Emma\Http\Router\Interfaces\RouteMatcherInterface;
use Emma\Http\Router\Interfaces\RouterStoreInterface;
use Emma\Http\Router\Interfaces\RouteInterface;

class RouteMatcherFactory extends AbstractFactory implements RouteMatcherInterface
{
    use ContainerManager;

    protected RouteMatcherInterface $routeMatcher;

    /**
     * @param array|string $param
     * @return mixed
     */
    public function make(array|string $param = []): mixed
    {
        $this->routeMatcher = $this->getContainer()->get(RouteMatcher::class, (array)$param);
        return $this;
    }

    /**
     * @param RequestInterface $httpRequest
     * @param RouterStoreInterface $httpRouter
     * @return RouteInterface
     */
    public function match(RequestInterface $httpRequest, RouterStoreInterface $httpRouter): ?RouteInterface
    {
        return $this->routeMatcher->match($httpRequest, $httpRouter);
    }

}