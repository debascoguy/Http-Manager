<?php
namespace Emma\Http\Request;

use Emma\Common\Singleton\Interfaces\SingletonInterface;
use Emma\Http\Request\Containers\CookieContainer;
use Emma\Http\Request\Containers\HeaderContainer;
use Emma\Http\Request\Containers\HttpContainer;
use Emma\Http\Request\Containers\ServerContainer;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class Request implements SingletonInterface, RequestInterface
{
    /**
     * @var array|HttpContainer
     */
    protected $query,
        $post,
        $files,
        $params;

    /**
     * @var array|HttpContainer
     */
    protected $cookies,
        $server,
        $headers;

    /**
     * @var Request|null
     */
    public static $instance = null;

    /**
     * @param array $query
     * @param array $post
     * @param array $files
     * @param array $cookies
     * @param array $server
     * @param array $params
     * @throws \Exception
     */
    public function __construct(
        array $query,
        array $post,
        array $files,
        array $cookies,
        array $server,
        array $params = []
    ) {
        $this->query    = new HttpContainer(Escaper\Escaper::escapeAll($query));
        $this->post     = new HttpContainer(Escaper\Escaper::escapeAll($post));
        $this->files    = new HttpContainer(Escaper\Escaper::escapeAll($files));
        $this->params   = new HttpContainer(Escaper\Escaper::escapeAll($params));
        $this->cookies  = new CookieContainer(Escaper\Escaper::escapeAll($cookies));
        $this->server   = new ServerContainer(Escaper\Escaper::escapeAll($server));
        $this->headers  = new HeaderContainer($this->server->getHeaders());
    }

    /**
     * @param string $name
     * @param string $value
     * @param bool $replace
     */
    public function setHeader(string $name, string $value, bool $replace = false): void
    {
        header("{$name}: {$value}", $replace);
        $this->headers->register($name, $value);
    }

    /**
     * @param array $params
     * @return Request
     * @throws \Exception
     */
    public static function getInstance(array $params = []): Request
    {
        if (self::$instance == null) {
            self::$instance = new self($_GET, $_POST, $_FILES, $_COOKIE, $_SERVER, $params);
        }
        if (!empty($params)) {
            self::$instance->setParams(array_merge(self::$instance->getParams(), $params));
        }
        return self::$instance;
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromGlobals($field = null, $default = "")
    {
        return $this->fromContainer($this->getGlobals(), $field, $default);
    }

    /**
     * @param HttpContainer $bag
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromContainer(HttpContainer $bag, $field = null, mixed $default = ""): mixed
    {
        $iterator = $bag->getIterator();
        if (empty($field)) {
            return $iterator;
        }
        return ($iterator->offsetExists($field)) ? $iterator->offsetGet($field) : $default;
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromQuery($field = null, string $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getQuery(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromPost($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getPost(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromFiles($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getFiles(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromCookies($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getCookies(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromParams($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getParams(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromServer($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getServer(), $field, $default);
    }

    /**
     * @param null $field
     * @param string $default
     * @return \ArrayIterator|string|array|null
     */
    public function fromHeader($field = null, $default = ""): array|string|\ArrayIterator|null
    {
        return $this->fromContainer($this->getHeaders(), $field, $default);
    }

    /**
     * @return array|HttpContainer
     */
    public function getQuery(): HttpContainer|array
    {
        return $this->query;
    }

    /**
     * @param array|HttpContainer $query
     * @return Request
     */
    public function setQuery(HttpContainer|array $query): static
    {
        if (is_array($query)) {
            $query = new HttpContainer(Escaper\Escaper::escapeAll($query));
        }
        $this->query = $query;
        return $this;
    }

    /**
     * @return array|HttpContainer
     */
    public function getPost(): HttpContainer|array
    {
        return $this->post;
    }

    /**
     * @param array|HttpContainer $post
     * @return Request
     */
    public function setPost(HttpContainer|array $post): static
    {
        if (is_array($post)) {
            $post = new HttpContainer(Escaper\Escaper::escapeAll($post));
        }
        $this->post = $post;
        return $this;
    }

    /**
     * @return array|HttpContainer
     */
    public function getFiles(): HttpContainer|array
    {
        return $this->files;
    }

    /**
     * @param array|HttpContainer $files
     * @return Request
     */
    public function setFiles(HttpContainer|array $files): static
    {
        if (is_array($files)) {
            $files = new HttpContainer(Escaper\Escaper::escapeAll($files));
        }
        $this->files = $files;
        return $this;
    }

    /**
     * @return HttpContainer|array|CookieContainer
     */
    public function getCookies(): CookieContainer|array
    {
        return $this->cookies;
    }

    /**
     * @param array|CookieContainer $cookies
     * @return Request
     */
    public function setCookies(array|CookieContainer $cookies): static
    {
        if (is_array($cookies)) {
            $cookies = new CookieContainer(Escaper\Escaper::escapeAll($cookies));
        }
        $this->cookies = $cookies;
        return $this;
    }

    /**
     * @return array|HttpContainer
     */
    public function getParams(): HttpContainer|array
    {
        return $this->params;
    }

    /**
     * @param array|HttpContainer $params
     * @return Request
     */
    public function setParams(HttpContainer|array $params): static
    {
        if (is_array($params)) {
            $params = new HttpContainer(Escaper\Escaper::escapeAll($params));
        }
        $this->params = $params;
        return $this;
    }

    /**
     * @return array|ServerContainer
     */
    public function getServer(): ServerContainer|HttpContainer|array
    {
        return $this->server;
    }

    /**
     * @param array|ServerContainer $server
     * @return Request
     */
    public function setServer(ServerContainer|array $server): static
    {
        if (is_array($server)) {
            $server = new ServerContainer(Escaper\Escaper::escapeAll($server));
        }
        $this->server = $server;
        return $this;
    }

    /**
     * @return HeaderContainer|HttpContainer|array
     */
    public function getHeaders(): HeaderContainer|HttpContainer|array
    {
        return $this->headers;
    }

    /**
     * @param array|HeaderContainer $headers
     * @return Request
     */
    public function setHeaders(HeaderContainer|array $headers)
    {
        if (is_array($headers)) {
            $headers = new HeaderContainer(Escaper\Escaper::escapeAll($headers));
        }
        $this->headers = $headers;
        return $this;
    }

    /**
     * @return HttpContainer
     */
    public function getGlobals()
    {
        return new HttpContainer($GLOBALS);
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return file_get_contents("php://input");
    }

    /**
     * @return array
     */
    public function getContentArray(): array
    {
        return json_decode(file_get_contents("php://input"), true);
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->getServer()->getUri();
    }

    /**
     * @param bool|bool $removeRealScriptName
     * @return string
     */
    public function getBasePath(bool $removeRealScriptName = false): string
    {
        $basePath = rtrim($this->getServer()->get("SCRIPT_NAME"), "/");
        if ($removeRealScriptName && substr($basePath, -4) === '.php') {
            $basePath = dirname($basePath);
        }
        if ($basePath == "/") {
            $basePath = "";
        }
        return $basePath;
    }

}