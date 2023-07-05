<?php

namespace Emma\Http\Request;

use Emma\Http\Request\Containers\CookieContainer;
use Emma\Http\Request\Containers\HttpContainer;
use Emma\Http\Request\Containers\ServerContainer;

interface RequestInterface
{
    public function fromContainer(HttpContainer $bag, $field = null, mixed $default = ""): mixed;

    public function getUri(): string;

    public function getPost(): HttpContainer|array;

    public function getQuery(): HttpContainer|array;

    public function getCookies(): CookieContainer|array;

    public function getServer(): ServerContainer|HttpContainer|array;

    public function setCookies(array|CookieContainer $cookies): static;

    public function setHeader(string $name, string $value, bool $replace = false): void;

    public function setParams(HttpContainer|array $params): static;

    public function fromParams($field = null, string $default = ""): array|string|\ArrayIterator|null;

    public function fromQuery($field = null, string $default = ""): array|string|\ArrayIterator|null;

    public function fromPost($field = null, string $default = ""): array|string|\ArrayIterator|null;

    public function fromServer($field = null, string $default = ""): array|string|\ArrayIterator|null;

}