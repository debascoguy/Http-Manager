<?php

namespace Emma\Http\Request\Containers;

interface HttpContainerInterface
{
    public function getParameters(): array;

    public function getIterator(): \ArrayIterator;

    public function count(): int;

    public function get($name, $default = null);

    public function has(string $name): bool;

    public function isEmpty();
}