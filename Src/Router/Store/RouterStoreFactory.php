<?php

namespace Emma\Http\Router\Store;

use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;
use Emma\Http\Router\Interfaces\RouterStoreInterface;

class RouterStoreFactory extends AbstractFactory implements RouterStoreInterface
{
    use ContainerManager;

    protected RouterStoreInterface $httpRouter;

    /**
     * @param array|string $param
     * @return $this
     */
    public function make(array|string $param = []): static
    {
        $this->httpRouter = $this->getContainer()->get(RouterStore::class, (array)$param);
        return $this;
    }

    /**
     * @return RouterStoreInterface
     */
    public function getHttpRouter(): RouterStoreInterface
    {
        return $this->httpRouter;
    }

    /**
     * @param array|string $httpRequestMethod
     * @param string $uri
     * @param $fn
     * @return $this
     */
    public function addRoute(array|string $httpRequestMethod, string $uri, $fn): static
    {
        $this->getHttpRouter()->addRoute($httpRequestMethod, $uri, $fn);
        return $this;
    }

    /**
     * @param string $route
     * @param $fn
     * @return $this
     */
    public function addGroup(string $route, $fn): static
    {
        $this->getHttpRouter()->addGroup($route, $fn);
        return $this;
    }

    /**
     * @return array
     */
    public function getRoutes(?string $httpRequestMethod = null): array
    {
        return $this->getHttpRouter()->getRoutes($httpRequestMethod);
    }
}