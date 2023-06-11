<?php

namespace Emma\Http\Router\Interfaces;

interface PathFinderInterface
{
    public function run(object|string $objectOrClass): void;
}