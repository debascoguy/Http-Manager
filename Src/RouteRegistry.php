<?php

namespace Emma\Http;

use Emma\Common\Singleton\Singleton;

class RouteRegistry
{
    use Singleton;

    private array $routables = [];

    public function register(array|object|callable $classOrFunctionOrObject)
    {
        $this->registryContainer[] = $classOrFunctionOrObject;
    }

    /**
     * @return array
     */
    public function getRoutables(): array
    {
        return $this->registryContainer;
    }

    /**
     * @param array $routables
     * @return RouteRegistry
     */
    public function setRoutables(array $routables): RouteRegistry
    {
        $this->registryContainer = $routables;
        return $this;
    }
}