<?php
namespace Emma\Http\Request\Containers;

use Emma\App\Config;
use Emma\ErrorHandler\Exception\BaseException;
use Emma\Security\LocationInfo;

/**
 * @Author: Ademola Aina
 * Email: debascoguy@gmail.com
 */
class ServerContainer extends HttpContainer
{
    /**
     * @return array
     */
    public function getHeaders()
    {
        $headers = [];
        $contentHeaders = ['CONTENT_LENGTH' => true, 'CONTENT_MD5' => true, 'CONTENT_TYPE' => true];
        $parameters = $this->getParameters();
        foreach ($this->getParameters() as $key => $value) {
            if (0 === strpos($key, 'HTTP_')) {
                $headers[substr($key, 5)] = $value;
            } // CONTENT_* are not prefixed with HTTP_
            elseif (isset($contentHeaders[$key])) {
                $headers[$key] = $value;
            }
        }

        if (isset($parameters['PHP_AUTH_USER'])) {
            $headers['PHP_AUTH_USER'] = $parameters['PHP_AUTH_USER'];
            $headers['PHP_AUTH_PW'] = isset($parameters['PHP_AUTH_PW']) ? $parameters['PHP_AUTH_PW'] : '';
        } else {
            /*
             * php-cgi under Apache does not pass HTTP Basic user/pass to PHP by default
             * For this workaround to work, add these lines to your .htaccess file:
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             *
             * A sample .htaccess file:
             * RewriteEngine On
             * RewriteCond %{HTTP:Authorization} ^(.+)$
             * RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
             * RewriteCond %{REQUEST_FILENAME} !-f
             * RewriteRule ^(.*)$ app.php [QSA,L]
             */

            $authorizationHeader = null;
            if (isset($parameters['HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $parameters['HTTP_AUTHORIZATION'];
            } elseif (isset($parameters['REDIRECT_HTTP_AUTHORIZATION'])) {
                $authorizationHeader = $parameters['REDIRECT_HTTP_AUTHORIZATION'];
            }

            if (null !== $authorizationHeader) {
                if (0 === stripos($authorizationHeader, 'basic ')) {
                    // Decode AUTHORIZATION header into PHP_AUTH_USER and PHP_AUTH_PW when authorization header is basic
                    $exploded = explode(':', base64_decode(substr($authorizationHeader, 6)), 2);
                    if (count($exploded) == 2) {
                        list($headers['PHP_AUTH_USER'], $headers['PHP_AUTH_PW']) = $exploded;
                    }
                } elseif (empty($parameters['PHP_AUTH_DIGEST']) && (0 === stripos($authorizationHeader, 'digest '))) {
                    // In some circumstances PHP_AUTH_DIGEST needs to be set
                    $headers['PHP_AUTH_DIGEST'] = $authorizationHeader;
                    $parameters['PHP_AUTH_DIGEST'] = $authorizationHeader;
                }
                else if (preg_match('/Bearer\s(\S+)/', $authorizationHeader, $matches)) {
                    //Get the Bearer Token
                    $headers['TOKEN'] = trim($matches[1]);
                    $headers['BEARER_TOKEN'] = trim($matches[1]);
                }                
            }
        }

        // PHP_AUTH_USER/PHP_AUTH_PW
        if (isset($headers['PHP_AUTH_USER'])) {
            $headers['AUTHORIZATION'] = 'Basic ' . base64_encode($headers['PHP_AUTH_USER'] . ':' . $headers['PHP_AUTH_PW']);
        } elseif (isset($headers['PHP_AUTH_DIGEST'])) {
            $headers['AUTHORIZATION'] = $headers['PHP_AUTH_DIGEST'];
        }

        return $headers;
    }

    /**
     * @param array $server
     * @throws BaseException
     */
    public function __construct(array $server = array())
    {
        parent::__construct($server);
        /**  Requests from the same server don't have a HTTP_ORIGIN header */
        if (!array_key_exists('HTTP_ORIGIN', $this->getParameters())) {
            $this->register('HTTP_ORIGIN', $this->get('SERVER_NAME'));
        }

        $method = $this->get('REQUEST_METHOD');
        if ( ($method == 'POST' || $method == 'GET') && $this->getIterator()->offsetExists("HTTP_X_HTTP_METHOD")) {
            $xHttpMethod = $this->get("HTTP_X_HTTP_METHOD");
            if ($xHttpMethod == 'DELETE') {
                $method = 'DELETE';
            } else if ($xHttpMethod == 'PUT') {
                $method = 'PUT';
            } else {
                throw new \Exception("Unexpected Header");
            }
        }
        $this->setRequestMethod($method);
    }

    /**
     * @return string
     */
    public function getRequestMethod(): string
    {
        return $this->get("RequestMethod");
    }

    /**
     * @param string|null $requestMethod
     * @return $this
     */
    public function setRequestMethod(?string $requestMethod): self
    {
        $this->register("RequestMethod", $requestMethod);
        return $this;
    }

    /**
     * @param $type
     * @return bool
     */
    public function requestIs($type): bool
    {
        return strtolower($this->getRequestMethod()) === strtolower($type);
    }

    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return $this->requestIs('POST');
    }

    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return $this->requestIs('GET');
    }

    /**
     * @return bool
     */
    public function isPut(): bool
    {
        return $this->requestIs('PUT');
    }

    /**
     * @return bool
     */
    public function isDelete(): bool
    {
        return $this->requestIs('DELETE');
    }

    /**
     * @return bool
     */
    public function isHead(): bool
    {
        return $this->requestIs('HEAD');
    }

    /**
     * @return bool
     */
    public function isPatch(): bool
    {
        return $this->requestIs('PATCH');
    }

    /**
     * @return bool
     */
    public function isOptions(): bool
    {
        return $this->requestIs('OPTIONS');
    }

    /**
     * @return bool
     */
    public function isXmlHttpRequest(): bool
    {
        return (strtolower($this->get('HTTP_X_REQUESTED_WITH', "")) == 'xmlhttprequest');
    }
    
    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return $this->isXmlHttpRequest();
    }

    /**
     * @return string
     */
    public function getServerIpAddress(): string
    {
        $ip = $this->get("REMOTE_ADDR");
        return $ip == "::1" ? "127.0.0.1" : $ip;
    }

    /**
     * Returns the IP address of the client.
     *
     * @param boolean $trust_proxy_headers Whether or not to trust the
     *                                       proxy headers HTTP_CLIENT_IP
     *                                       and HTTP_X_FORWARDED_FOR. ONLY
     *                                       use if your server is behind a
     *                                       proxy that sets these values
     * @return  string
     */
    public function getClientIpAddress(bool $trust_proxy_headers = false)
    {
        if (!$trust_proxy_headers) {
            return $this->getServerIpAddress();
        }

        if (!empty($this->get("HTTP_CLIENT_IP"))) {
            return $this->get('HTTP_CLIENT_IP');
        } else if (!empty($this->get('HTTP_X_FORWARDED_FOR'))) {
            return $this->get('HTTP_X_FORWARDED_FOR');
        } else {
            return $this->getServerIpAddress();
        }
    }

    /**
     * @param string $ip
     * @return LocationInfo|null
     */
    public function getLocationInfoByIp($ip = "")
    {
        // return LocationInfo::getInstance($ip);
        return null;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        $parameters = $this->getParameters();
        if (!isset($parameters["R_URI"])) {
            $base = basename(dirname(Config::getFrameworkBaseRoute()));
            $length = strlen($base);
            $uri = strtolower($parameters["REQUEST_URI"]);
            if (false !== $pos = strpos($uri, '?')) {
                $uri = substr($uri, 0, $pos);
            }
            $uri = rawurldecode($uri);
            if (substr($uri, 0, $length) === $base) {
                $parameters["R_URI"] = substr_replace($uri, "", 0, $length);
            }
            else if (substr($uri, 0, $length+1) === "/".$base) {
                $parameters["R_URI"] = substr_replace($uri, "", 0, $length+1);
            }
            else{
                $parameters["R_URI"] = $uri;
            }
        }
        return $parameters["R_URI"];
    }

    /**
     * GET the Current Page URL
     * @param bool $show_www
     * @return string
     */
    public function getCurrentURL(bool $show_www = true): string
    {
        $parameters = $this->getParameters();
        $pageURL = ($parameters["HTTPS"] == "on") ? 'https://' : 'http://';
        $pageURL .= ($parameters["SERVER_PORT"] != "80") ?
            $parameters["SERVER_NAME"] . ":" . $parameters["SERVER_PORT"] . $parameters["REQUEST_URI"]
            : $parameters["SERVER_NAME"] . $parameters["REQUEST_URI"];

        return ($show_www) ? $pageURL : str_replace("www.", "", $pageURL);
    }

    /**
     * @param string $append
     * @return string
     */
    public function getHomeURL(string $append = ""): string
    {
        $parameters = $this->getParameters();
        $pageURL = ($parameters["HTTPS"] == "on") ? 'https://' : 'http://';
        $pageURL .= ($parameters["SERVER_PORT"] != "80") ?
            $parameters["SERVER_NAME"] . ":" . $parameters["SERVER_PORT"] : $parameters["SERVER_NAME"];
        return $pageURL . $append;
    }
    
    /**
     * 
     * @return string
     */
    public function getServerName()
    {
        return $this->get("SERVER_NAME");
    }
    
    /**
     * @return string
     */
    public function getServerPort()
    {
        return $this->get("SERVER_PORT");
    }
}