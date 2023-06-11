<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;
use Emma\Http\Router\Interfaces\PathFinderInterface;

class PathFinderFactory extends AbstractFactory implements PathFinderInterface
{
    use ContainerManager;

    protected PathFinderInterface $scanner;

    /**
     * @param array|string|null $param
     * @return PathFinderInterface
     */
    public function make(array|string $param = null): PathFinderInterface
    {
        if (is_null($param)) {
            $param = PathFinder::class;
        }
        $this->scanner = $this->getContainer()->get($param);
        return $this;
    }

    /**
     * @param object|string|null $objectOrClassOrMethodOrFunction
     * @return void
     */
    public function run(object|string|null $objectOrClassOrMethodOrFunction = null): void
    {
        $this->scanner->run($objectOrClassOrMethodOrFunction);
    }
}