<?php

namespace Emma\Http\Request;

use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;
use Emma\Http\Request\Containers\CookieContainer;
use Emma\Http\Request\Containers\HttpContainer;
use Emma\Http\Request\Containers\ServerContainer;

class RequestFactory extends AbstractFactory implements RequestInterface
{
    use ContainerManager;

    protected RequestInterface $request;

    /**
     * @param array|string $param
     * @return $this
     * @throws \Exception
     */
    public function make(array|string $param = []): static
    {
        $this->request = $this->getContainer()->get(Request::class, (array)$param);
        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @param HttpContainer $bag
     * @param $field
     * @param mixed $default
     * @return mixed
     */
    public function fromContainer(HttpContainer $bag, $field = null, mixed $default = ""): mixed
    {
        return $this->getRequest()->fromContainer($bag, $field, $default);
    }

    public function getUri(): string
    {
        return $this->getRequest()->getUri();
    }

    /**
     * @return HttpContainer|array
     */
    public function getPost(): HttpContainer|array
    {
        return $this->getRequest()->getPost();
    }

    /**
     * @return HttpContainer|array
     */
    public function getQuery(): HttpContainer|array
    {
        return $this->getRequest()->getQuery();
    }

    /**
     * @return ServerContainer|HttpContainer|array
     */
    public function getServer(): ServerContainer|HttpContainer|array
    {
        return $this->getRequest()->getServer();
    }

    public function getCookies(): CookieContainer|array
    {
        return $this->getRequest()->getCookies();
    }

    public function getContent(): string|false
    {
        return $this->getRequest()->getContent();
    }

    public function getContentArray(): array
    {
        return $this->getRequest()->getContentArray();
    }

    public function setCookies(array|CookieContainer $cookies): static
    {
        return $this->getRequest()->setCookies($cookies);
    }

    public function setHeader(string $name, string $value, bool $replace = false): void
    {
        $this->getRequest()->setHeader($name, $value, $replace);
    }

    /**
     * @param HttpContainer|array $params
     * @return $this
     */
    public function setParams(HttpContainer|array $params): static
    {
        $this->getRequest()->setParams($params);
        return $this;
    }

    public function fromParams($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromParams($field, $default);
    }

    public function fromQuery($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromQuery($field, $default);
    }

    public function fromPost($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromPost($field, $default);
    }

    public function fromCookies($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromCookies($field, $default);
    }

    public function fromServer($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromServer($field, $default);
    }

    public function fromHeader($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromHeader($field, $default);
    }

    public function fromFiles($field = null, mixed $default = ""): array|string|\ArrayIterator|null
    {
        return $this->getRequest()->fromFiles($field, $default);
    }

}