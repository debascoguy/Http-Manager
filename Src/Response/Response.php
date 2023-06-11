<?php
namespace Emma\Http\Response;

use Emma\Http\HttpStatus;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Response implements ResponseInterface
{
    /**
     * @var array
     */
    protected array $headers = [];

    /**
     * @var array
     */
    protected array $body = [];

    /**
     * @var bool
     */
    protected bool $isRedirect = false;

    /**
     * @var bool
     */
    protected bool $isJson = false;

    /**
     * @var int
     */
    protected int $responseCode = HttpStatus::HTTP_OK;

    /**
     * @var string|null
     */
    protected ?string $responseText;


    /**
     * Normalize a header name
     *
     * Normalizes a header name to X-Capitalized-Names
     *
     * @param string $name
     * @return string
     */
    protected function normalizeHeader(string $name): string
    {
        $str = str_replace(' ', '', ucwords(str_replace(array('-', '_'), ' ', $name)));
        $str[0] = strtolower($str[0]);
        return $str;
    }

    /**
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param boolean $replace
     * @return Response
     * @throws \Exception
     */
    public function setHeader(string $name, string $value, bool $replace = false): static
    {
        $this->canSendHeaders(true);
        $name = $this->normalizeHeader($name);
        $value = (string)$value;

        if ($replace) {
            $this->removeHeader($name);
        }

        $this->headers[] = array(
            'name' => $name,
            'value' => $value,
            'replace' => $replace
        );

        return $this;
    }

    /**
     * @param array $headers
     * @param bool $replace
     * @return $this
     */
    public function setHeaders(array $headers, bool $replace = false): static
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value, $replace);
        }
        return $this;
    }
    
    /**
     * @param string $name
     * @return string|null
     */
    public function removeHeader(string $name): ?string
    {
        foreach ($this->headers as $key => $header) {
            if ($name == $header['name']) {
                $value = $header['value'];
                unset($this->headers[$key]);
                return $value;
            }
        }
        return null;
    }

    /**
     * @param array|string $content
     * @param string|null $name
     * @return Response
     */
    public function setBody(array|string $content, string $name = null): static
    {
        if (null === $name) {
            $this->body = array('default' => $content);
        } else {
            $this->body[$name] = $content;
        }
        return $this;
    }

    /**
     * @param array|string|null $name
     * @return array|string
     */
    public function getBody(array|string $name = null): array|string
    {
        if (null === $name) {
            return implode('', $this->body);
        }

        if (!isset($this->body[$name])) {
            return '';
        }

        return $this->body[$name];
    }

    /**
     * Set redirect URL
     *
     * Sets Location header and response code. Forces replacement of any prior
     * redirects.
     *
     * @param string $url
     * @param int $code
     * @return Response
     * @throws \Exception
     */
    public function setRedirect(string $url, int $code = HttpStatus::HTTP_FOUND): static
    {
        $this->setHeader('Location', $url, true)->setResponseCode($code);
        return $this;
    }

    /**
     * Determines if headers can be sent.
     *
     * @param boolean $throw
     * @return boolean
     *@throws \Exception
     */
    public function canSendHeaders(bool $throw = false): bool
    {
        $ok = headers_sent($file, $line);
        if ($ok && $throw) {
            throw new \Exception(sprintf('Cannot send headers; headers already sent in %s, line %d', $file, $line));
        }
        return !$ok;
    }

    /**
     * Set HTTP response code to use with headers
     *
     * @param int $code
     * @return Response
     * @throws \Exception
     */
    public function setResponseCode(int $code): static
    {
        if (100 > $code || 599 < $code) {
            throw new \Exception('Invalid HTTP response code');
        }

        if ((300 <= $code) && (307 >= $code)) {
            $this->isRedirect = true;
        } else {
            $this->isRedirect = false;
        }

        $this->responseCode = $code;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResponseText(): ?string
    {
        $this->responseText = HttpStatus::$statusTexts[$this->responseCode] ?? "";
        return $this->responseText;
    }

    /**
     * @param string|null $responseText
     * @return $this
     */
    public function setResponseText(?string $responseText = "OK"): static
    {
        if (empty($responseText)) {
            $this->responseText = $this->getResponseText();
        } else {
            $this->responseText = $responseText;
        }
        return $this;
    }

    /**
     * @param int $code
     * @param string $text
     * @throws \Exception
     */
    public function setHttpStatus(int $code, string $text): void
    {
        $this->setResponseCode($code)->setResponseText($text);
    }

    /**
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     * Displays the HTTP body.
     */
    public function outputBody(): void
    {
        $body = implode('', $this->body);
        echo $body;
    }

    /**
     * Sends the HTTP headers.
     *
     * @return Response
     * @throws \Exception
     */
    public function sendHeaders(): self
    {
        $this->canSendHeaders(true);
        if (empty($this->responseText)) {
            $this->responseText = HttpStatus::$statusTexts[$this->responseCode];
        }
        header('HTTP/1.1 ' . $this->responseCode . " " . $this->responseText);
        foreach ($this->headers as $header) {
            header("{$header['name']}: {$header['value']}", $header['replace']);
        }
        return $this;
    }

    /**
     * Outputs the entire response.
     */
    public function sendResponse(): void
    {
        $this->sendHeaders();
        if (!$this->isRedirect()) {
            $this->outputBody();
        }
    }

    /**
     * @return void
     */
    public function renderResponse(): void
    {
        $this->sendResponse();
        die();
    }

    /**
     * Gets the response as a string.
     *
     * @return string
     */
    public function __toString()
    {
        ob_start();
        $this->sendResponse();
        return ob_get_clean();
    }

    /**
     * @return bool
     */
    public function isRedirect(): bool
    {
        return $this->isRedirect;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Exception
     */
    public function setJson(array $data = []): static
    {
        $this->isJson = true;
        $this->setHeader('Content-type', 'application/json');
        $this->setBody(json_encode($data));
        return $this;
    }

    /**
     * @return bool
     */
    public function isJson(): bool
    {
        return $this->isJson;
    }

}
