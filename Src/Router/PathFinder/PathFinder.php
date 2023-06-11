<?php

namespace Emma\Http\Router\PathFinder;

use Emma\Di\Container\ContainerManager;
use Emma\Http\Definition\DefinitionLoader;
use Emma\Http\Router\Interfaces\PathFinderInterface;

class PathFinder implements PathFinderInterface
{
    use ContainerManager;

    protected array $routeDefinitions = [];

    /**
     * @var PathFinderFactory|null
     */
    protected ?PathFinderFactory $scanClassFactory;

    /**
     * @var PathFinderFactory|null
     */
    protected ?PathFinderFactory $scanMethodFactory;

    /**
     * @var PathFinderFactory|null
     */
    protected ?PathFinderFactory $scanFunctionFactory;

    /**
     * @var bool
     */
    protected bool $isReady = false;

    public function __construct()
    {
        $this->routeDefinitions = $this->getContainer()->get(DefinitionLoader::class)->get();
        /** @var PathFinderFactory $factory */
        $factory = $this->getContainer()->create(PathFinderFactory::class);
        $this->scanClassFactory = $factory;
        $this->scanMethodFactory = clone $factory;
        $this->scanFunctionFactory = clone $factory;
    }

    /**
     * @return $this
     */
    public function make(): static
    {
        if ($this->isReady) {
            return $this;
        }
        $this->scanClassFactory->make(PathFinderClass::class);
        $this->scanMethodFactory->make(PathFinderMethod::class);
        $this->scanFunctionFactory->make(PathFinderFunction::class);
        $this->isReady = true;
        return $this;
    }

    /**
     * @param object|string|null $objectOrClass
     * @return void
     */
    public function run(object|string $objectOrClass = null): void
    {
        $this->make();
        foreach($this->routeDefinitions as $routable) {
            if (class_exists($routable)) {
                $this->scanClassFactory->run($routable);
            }
            else if (is_array($routable) && method_exists($routable[0], $routable[1])) {
                $this->scanMethodFactory->run(new \ReflectionMethod($routable[0], $routable[1]));
            }
            else if (is_string($routable) && function_exists($routable)) {
                $this->scanFunctionFactory->run(new \ReflectionFunction($routable));
            }
        }
    }

}