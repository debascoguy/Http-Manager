<?php
namespace Emma\Http\Router\Store;

use Emma\Http\Request\Request;
use Emma\Http\Router\Interfaces\RouterStoreInterface;

class RouterStore implements RouterStoreInterface, \Countable, \IteratorAggregate
{
    /**
     * @var ?Request
     */
    protected ?Request $request;

    /**
     * @var array
     */
    private array $routes = [];

    /**
     * @var string
     */
    private string $groupUri = "";

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param string $httpRequestMethod
     * @param string $uri
     * @param $fn
     * @return void
     */
    private function register(string $httpRequestMethod, string $uri, $fn): void
    {
        if (is_callable($fn) && is_callable($fn, true)) {
            throw new \InvalidArgumentException("Invalid Function/CallBack/Callable Passed for Route Registry!");
        }
        $httpRequestMethod = strtoupper($httpRequestMethod);
        if ($this->request->getServer()->requestIs($httpRequestMethod)) {
            $this->routes[$httpRequestMethod][strtolower($uri)] = $fn;
        }
    }

    /**
     * @param array|string $httpRequestMethod
     * @param string $route
     * @param $fn
     * @return $this
     */
    public function addRoute(array|string $httpRequestMethod, string $route, $fn): static
    {
        $route = (!empty($this->groupUri)) ? ($this->groupUri . $route) : $route;
        foreach((array)$httpRequestMethod as $httpM) {
            $this->register($httpM, $route, $fn);
        }
        return $this;
    }

    /**
     * @param string $route
     * @param callable $callable
     * @return $this
     */
    public function addGroup(string $route, callable $callable): static
    {
        $group = $this->groupUri;
        $this->groupUri .= $route;
        $callable($this);
        $this->groupUri = $group;
        return $this;
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function get(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function post(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function put(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function patch(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function delete(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function options(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string $uri
     * @param $fn
     * @return static
     */
    public function head(string $uri, $fn): static
    {
        return $this->addRoute(__FUNCTION__, $uri, $fn);
    }

    /**
     * @param string|null $httpRequestMethod
     * @return array
     */
    public function getRoutes(?string $httpRequestMethod = null): array
    {
        return empty($httpMethod) ? $this->routes : $this->routes[$httpRequestMethod];
    }

    /**
     * @param string|null $httpRequestMethod
     * @return \ArrayIterator
     */
    public function getIterator(string $httpRequestMethod = null): \ArrayIterator
    {
        return new \ArrayIterator($this->getRoutes($httpRequestMethod));
    }

    /**
     * @param string|null $httpRequestMethod
     * @return int
     */
    public function count(string $httpRequestMethod = null): int
    {
        return count($this->getRoutes($httpRequestMethod));
    }

}