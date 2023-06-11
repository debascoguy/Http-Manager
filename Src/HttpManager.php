<?php

namespace Emma\Http;

use Emma\Di\Container\ContainerManager;
use Emma\Http\Request\RequestFactory;
use Emma\Http\Response\ResponseFactory;
use Emma\Http\Router\PathFinder\PathFinderFactory;
use Emma\Http\Router\Route\RouteMatcherFactory;
use Emma\Http\Router\Store\RouterStoreFactory;

class HttpManager
{
    use ContainerManager;

    /**
     * @var ResponseFactory
     */
    protected ResponseFactory $httpResponseFactory;

    /**
     * @var RequestFactory
     */
    protected RequestFactory $httpRequestFactory;

    /**
     * @var RouterStoreFactory
     */
    protected RouterStoreFactory $httpRouterFactory;

    /**
     * @var PathFinderFactory
     */
    protected PathFinderFactory $httpRouteScannerFactory;

    /**
     * @var RouteMatcherFactory
     */
    protected RouteMatcherFactory $httpRouteMatcherFactory;

    protected bool $isBooted = false;

    /**
     * @return $this
     */
    protected function init(): static
    {
        $container = $this->getContainer();
        $this->setHttpResponseFactory($container->get(ResponseFactory::class)->make());
        $this->setHttpRequestFactory($container->get(RequestFactory::class)->make());
        $this->setHttpRouteScannerFactory($container->get(PathFinderFactory::class)->make());
        $this->setHttpRouteMatcherFactory($container->get(RouteMatcherFactory::class)->make());
        $this->setHttpRouterFactory($container->get(RouterStoreFactory::class)->make());
        $this->setContainer($container);
        return $this;
    }

    /**
     * @return $this
     * @throws \Exception
     */
    public function boot(): static
    {
        if ($this->isBooted) {
            return $this;
        }
        $this->init()
            ->getHttpRouteScannerFactory()
            ->run(null);
        $this->isBooted = true;
        return $this;
    }

    /**
     * @return Router\Interfaces\RouteInterface
     * @throws \Exception
     */
    public function matchRequestRoutes(): Router\Interfaces\RouteInterface
    {
        $this->boot();
        return $this->getHttpRouteMatcherFactory()->match(
            $this->getHttpRequestFactory(),
            $this->getHttpRouterFactory()
        );
    }

    /**
     * @return ResponseFactory
     */
    public function getHttpResponseFactory(): ResponseFactory
    {
        return $this->httpResponseFactory;
    }

    /**
     * @param ResponseFactory $httpResponseFactory
     * @return static
     */
    public function setHttpResponseFactory(ResponseFactory $httpResponseFactory): static
    {
        $this->httpResponseFactory = $httpResponseFactory;
        return $this;
    }

    /**
     * @return RequestFactory
     */
    public function getHttpRequestFactory(): RequestFactory
    {
        return $this->httpRequestFactory;
    }

    /**
     * @param RequestFactory $httpRequestFactory
     * @return static
     */
    public function setHttpRequestFactory(RequestFactory $httpRequestFactory): static
    {
        $this->httpRequestFactory = $httpRequestFactory;
        return $this;
    }

    /**
     * @return RouterStoreFactory
     */
    public function getHttpRouterFactory(): RouterStoreFactory
    {
        return $this->httpRouterFactory;
    }

    /**
     * @param RouterStoreFactory $httpRouterFactory
     * @return static
     */
    public function setHttpRouterFactory(RouterStoreFactory $httpRouterFactory): static
    {
        $this->httpRouterFactory = $httpRouterFactory;
        return $this;
    }

    /**
     * @return PathFinderFactory
     */
    public function getHttpRouteScannerFactory(): PathFinderFactory
    {
        return $this->httpRouteScannerFactory;
    }

    /**
     * @param PathFinderFactory $httpRouteScannerFactory
     * @return HttpManager
     */
    public function setHttpRouteScannerFactory(PathFinderFactory $httpRouteScannerFactory): static
    {
        $this->httpRouteScannerFactory = $httpRouteScannerFactory;
        return $this;
    }

    /**
     * @return RouteMatcherFactory
     */
    public function getHttpRouteMatcherFactory(): RouteMatcherFactory
    {
        return $this->httpRouteMatcherFactory;
    }

    /**
     * @param RouteMatcherFactory $httpRouteMatcherFactory
     * @return static
     */
    public function setHttpRouteMatcherFactory(RouteMatcherFactory $httpRouteMatcherFactory): static
    {
        $this->httpRouteMatcherFactory = $httpRouteMatcherFactory;
        return $this;
    }

}