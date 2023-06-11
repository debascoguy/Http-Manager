<?php

namespace Emma\Http\Mappings;

use Attribute;
use Emma\Http\Request\Method;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD | Attribute::TARGET_FUNCTION)]
class RequestMapping
{
    /**
     * @var string|null
     */
    private ?string $routes;

    /**
     * @var array|string
     */
    private array|string $httpRequestMethod;

    /**
     * @param string $routes
     * @param array|string|null $httpRequestMethod
     */
    public function __construct(string $routes, array|string|null $httpRequestMethod = null)
    {
        $all = Method::all();
        $httpRequestMethod = empty($httpRequestMethod) ? $all : (array) $httpRequestMethod;
        $isSubset = array_diff($httpRequestMethod, $all);
        if (!!$isSubset) {
            throw new \InvalidArgumentException("Invalid HTTP Request Method in " . __CLASS__ . " Mapping -> " . json_encode($isSubset));
        }
        $errorMessage = "";
        if (!$this->validateRoute($routes, $errorMessage)) {
            throw new \InvalidArgumentException($errorMessage);
        }
        $this->routes = $routes;
        $this->httpRequestMethod = $httpRequestMethod;
    }

    /**
     * @param string $routes
     * @param String $errorMessage
     * @return bool
     */
    private function validateRoute(string $routes, String &$errorMessage): bool
    {
        $regex = str_replace('/', '\/', $routes); //Escape /
        if(@preg_match('/^' . ($regex) . '$/', '') === false) {
            $errorMessage = str_replace("preg_match(): ", "", error_get_last()["message"]);
            return false;
        }
        return true;
    }

    /**
     * @return string|null
     */
    public function getRoutes(): ?string
    {
        return $this->routes;
    }

    /**
     * @return array|string
     */
    public function getHttpRequestMethod(): array|string
    {
        return $this->httpRequestMethod;
    }
}