<?php
namespace Emma\Http\Request\Containers;


use Emma\Http\HttpStatus;

class HeaderContainer extends HttpContainer
{
    /**
     * Normalize a header name
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
     * Set a header
     *
     * If $replace is true, replaces any headers already defined with that
     * $name.
     *
     * @param string $name
     * @param string $value
     * @param bool $replace
     * @return self
     * @throws \Exception
     */
    public function setHeader(string $name, string $value, bool $replace = false): static
    {
        $this->canSendHeaders(true);
        $name = $this->normalizeHeader($name);
        $value = (string)$value;

        if ($replace) {
            $this->remove($name);
        }

        $this->register($name, array(
            'name' => $name,
            'value' => $value,
            'replace' => $replace
        ));

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
     * Sends the HTTP headers.
     *
     * @throws \Exception
     */
    public function acceptRequestHeaders(): static
    {
        $parameters = $this->getParameters();
        foreach ($parameters as $header) {
            header("{$header['name']}: {$header['value']}", $header['replace']);
        }
        return $this;
    }

    /**
     * Sends the HTTP headers.
     *
     * @throws \Exception
     */
    public function sendResponseHeaders($responseCode, $responseText): static
    {
        $this->canSendHeaders(true);
        if (empty($responseText)) {
            $responseText = HttpStatus::$statusTexts[$responseCode];
        }
        header('HTTP/1.1 ' . $responseCode . " " . $responseText);
        $parameters = $this->getParameters();
        foreach ($parameters as $header) {
            header("{$header['name']}: {$header['value']}", $header['replace']);
        }
        return $this;
    }

}