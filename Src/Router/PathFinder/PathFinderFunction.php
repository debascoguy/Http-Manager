<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Http\Mappings\RequestMapping;
use Emma\Http\Router\Interfaces\PathFinderInterface;
use Emma\Http\Router\Store\RouterStore;

class PathFinderFunction implements PathFinderInterface
{
    protected ?RouterStore $router;

    public function __construct(RouterStore $router)
    {
        $this->router = $router;
    }

    /**
     * @param string|object $fn
     * @return void
     * @throws \ReflectionException
     */
    public function run(string|object $fn): void
    {
        $reflector = $fn instanceof \ReflectionFunction ? $fn : new \ReflectionFunction($fn);
        $functionAttributes = RoutableAttributes::filter($reflector->getAttributes());
        if (empty($functionAttributes)) {
            return;
        }
        foreach($functionAttributes as $functionAttribute) {
            /** @var RequestMapping $functionAttrInstance */
            $functionAttrInstance = $functionAttribute->newInstance();
            if (is_callable($reflector->getName())) {
                $this->router->addRoute(
                    $functionAttrInstance->getHttpRequestMethod(),
                    $functionAttrInstance->getRoutes(),
                    $reflector->getName()
                );
            }
        }
    }

}