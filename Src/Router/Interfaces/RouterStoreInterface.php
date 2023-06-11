<?php
namespace Emma\Http\Router\Interfaces;

interface RouterStoreInterface
{
    /**
     * @param array|string $httpRequestMethod
     * @param string $route
     * @param $fn
     * @return $this
     */
    public function addRoute(array|string $httpRequestMethod, string $route, $fn): static;

    /**
     * @param string $route
     * @param callable $callable
     * @return $this
     */
    public function addGroup(string $route, callable $callable): static;


    public function getRoutes(?string $httpRequestMethod = null): array;

}