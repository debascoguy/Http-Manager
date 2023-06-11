<?php
namespace Emma\Http\Router\Interfaces;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
interface RouteInterface
{
    public function getMatchedRoute(): ?string;

    public function getMatchedRegex(): ?string;

    public function getCallable(): callable|array;

    public function getParams(): array;
}