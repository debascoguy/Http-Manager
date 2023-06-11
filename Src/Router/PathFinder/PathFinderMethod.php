<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Http\Mappings\RequestMapping;
use Emma\Http\Router\Interfaces\PathFinderInterface;
use Emma\Http\Router\Store\RouterStore;
use InvalidArgumentException;

class PathFinderMethod implements PathFinderInterface
{
    protected ?RouterStore $router;

    public function __construct(RouterStore $router)
    {
        $this->router = $router;
    }

    /**
     * @param object|string $method
     * @param string|null $parentRoute
     * @param array|null $parentHttpRequestMethod
     * @return void
     * @throws \ReflectionException
     */
    public function run(object|string $method, ?string $parentRoute = null, ?array $parentHttpRequestMethod = []): void
    {
        $reflector = $method instanceof \ReflectionMethod ? $method : new \ReflectionMethod($method);
        $methodsAttributes = RoutableAttributes::filter($reflector->getAttributes());
        if (empty($methodsAttributes)) {
            return;
        }

        $object_or_class = $reflector->getDeclaringClass()->getName();
        $hasParentHttpMethod = !empty($parentHttpRequestMethod);
        foreach($methodsAttributes as $methodsAttribute) {
            /** @var RequestMapping $methodAttrInstance */
            $methodAttrInstance = $methodsAttribute->newInstance();
            $httpRequestMethod = $methodAttrInstance->getHttpRequestMethod();
            if ($hasParentHttpMethod && array_intersect($httpRequestMethod, $parentHttpRequestMethod) !== $httpRequestMethod) {
                throw new InvalidArgumentException(
                    "Class Method cannot implements Http Method that isn't allowed by the class route! "
                    . " [" . $object_or_class . "]"
                );
            }
            $this->router->addRoute(
                $methodAttrInstance->getHttpRequestMethod(),
                $parentRoute.$methodAttrInstance->getRoutes(),
                [$object_or_class, $reflector->getName()]
            );
        }
    }

}