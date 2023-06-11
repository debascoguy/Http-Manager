<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Http\Mappings\RequestMapping;
use Emma\Http\Router\Interfaces\PathFinderInterface;
use Emma\Http\Router\Store\RouterStore;
use ReflectionClass;

class PathFinderClass implements PathFinderInterface
{
    protected ?RouterStore $router;

    public function __construct(RouterStore $router)
    {
        $this->router = $router;
    }

    /**
     * @throws \ReflectionException
     */
    public function run(object|string $routable): void
    {
        if ($routable instanceof ReflectionClass) {
            $reflector = $routable;
            $className = $reflector->getName();
            $routable = $className;
        } else {
            $reflector = new \ReflectionClass($routable);
        }
        $methods = $reflector->getMethods(\ReflectionMethod::IS_PUBLIC);
        $attributes = RoutableAttributes::filter($reflector->getAttributes());
        if (!empty($attributes)) {
            foreach ($attributes as $attribute) {
                /** @var RequestMapping $attrInstance */
                $attrInstance = $attribute->newInstance();
                $route = $attrInstance->getRoutes();
                $httpRequestMethod = $attrInstance->getHttpRequestMethod();
                if (is_callable($routable)) {
                    $this->router->addRoute($httpRequestMethod, $route, $routable);
                }

                foreach ($methods as $method) {
                    (new PathFinderMethod($this->router))->run($method, $route, $httpRequestMethod);
                }
            }
        }
    }

}