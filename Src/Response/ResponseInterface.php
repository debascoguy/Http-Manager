<?php

namespace Emma\Http\Response;

use Emma\Http\HttpStatus;

interface ResponseInterface
{
    public function setHeader(string $name, string $value, bool $replace = false): static;

    public function canSendHeaders(bool $throw = false): bool;

    public function isRedirect(): bool;

    public function setResponseCode(int $code): static;

    public function setResponseText(?string $responseText = "OK"): static;

    public function setHttpStatus(int $code, string $text): void;

    public function setRedirect(string $url, int $code = HttpStatus::HTTP_FOUND): static;

    public function setBody(array|string $content, string $name = null): static;

    public function setJson(array $data = []): static;

    public function sendHeaders(): self;

    public function sendResponse(): void;

    public function renderResponse(): void;
}