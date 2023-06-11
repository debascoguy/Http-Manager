<?php

namespace Emma\Http\Router\Route;

use Emma\Http\Router\Interfaces\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var string|null
     */
    protected ?string $matchedRoute;

    /**
     * @var string|null
     */
    protected ?string $matchedRegex;

    /**
     * @var callable
     */
    protected $callable;

    /**
     * @var array
     */
    protected array $params = [];

    /**
     * @var bool
     */
    protected bool $found = false;

    /**
     * @param string|null $matchedRoute
     * @param string|null $matchedRegex
     * @param callable|array|string $callable
     * @param array $params
     */
    public function __construct(?string $matchedRoute, ?string $matchedRegex, callable|array|string $callable, array $params = [])
    {
        $this->matchedRoute = $matchedRoute;
        $this->matchedRegex = $matchedRegex;
        $this->callable = $callable;
        $this->params = $params;
        $this->found = true;
    }


    /**
     * @return string|null
     */
    public function getMatchedRoute(): ?string
    {
        return $this->matchedRoute;
    }

    /**
     * @param string|null $matchedRoute
     * @return Route
     */
    public function setMatchedRoute(?string $matchedRoute): static
    {
        $this->matchedRoute = $matchedRoute;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getMatchedRegex(): ?string
    {
        return $this->matchedRegex;
    }

    /**
     * @param string|null $matchedRegex
     * @return Route
     */
    public function setMatchedRegex(?string $matchedRegex): static
    {
        $this->matchedRegex = $matchedRegex;
        return $this;
    }

    /**
     * @return callable|array
     */
    public function getCallable(): callable|array
    {
        return $this->callable;
    }

    /**
     * @param callable|array|string $callable
     * @return $this
     */
    public function setCallable(callable|array|string $callable): static
    {
        $this->callable = $callable;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return Route
     */
    public function setParams(array $params): static
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFound(): bool
    {
        return $this->found;
    }

    /**
     * @param bool $found
     * @return Route
     */
    public function setFound(bool $found): static
    {
        $this->found = $found;
        return $this;
    }

}