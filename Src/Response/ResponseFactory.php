<?php

namespace Emma\Http\Response;

use Emma\Di\Container\ContainerManager;
use Emma\Common\Factory\AbstractFactory;
use Emma\Http\HttpStatus;

class ResponseFactory extends AbstractFactory implements ResponseInterface
{
    use ContainerManager;

    protected ResponseInterface $response;

    /**
     * @param array|string $param
     * @return mixed
     */
    public function make(array|string $param = []): static
    {
        $this->response = $this->getContainer()->get(Response::class, (array)$param);
        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @return $this
     */
    public function setHeader(string $name, string $value, bool $replace = false): static
    {
        $this->getResponse()->setHeader($name, $value, $replace);
        return $this;
    }

    /**
     * @param bool $throw
     * @return bool
     */
    public function canSendHeaders(bool $throw = false): bool
    {
        return $this->getResponse()->canSendHeaders($throw);
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->getResponse()->isRedirect();
    }

    /**
     * @param int $code
     * @return $this
     */
    public function setResponseCode(int $code): static
    {
        $this->getResponse()->setResponseCode($code);
        return $this;
    }

    public function setResponseText(?string $responseText = "OK"): static
    {
        $this->getResponse()->setResponseText($responseText);
        return $this;
    }

    public function setHttpStatus(int $code, string $text): void
    {
        $this->getResponse()->setHttpStatus($code, $text);
    }

    public function setRedirect(string $url, int $code = HttpStatus::HTTP_FOUND): static
    {
        $this->getResponse()->setRedirect($url, $code);
        return $this;
    }

    public function setBody(array|string $content, string $name = null): static
    {
        $this->getResponse()->setBody($content, $name);
        return $this;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setJson(array $data = []): static
    {
        $this->getResponse()->setJson($data);
        return $this;
    }

    public function getResponseCode(): int
    {
        return $this->getResponse()->getResponseCode();
    }

    /**
     * @return ResponseInterface
     */
    public function sendHeaders(): ResponseInterface
    {
        return $this->getResponse()->sendHeaders();
    }

    public function sendResponse(): void
    {
        $this->getResponse()->sendResponse();
    }

    public function renderResponse(): void
    {
        $this->getResponse()->renderResponse();
    }

}