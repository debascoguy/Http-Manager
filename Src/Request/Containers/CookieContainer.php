<?php
namespace Emma\Http\Request\Containers;

class CookieContainer extends HttpContainer
{
    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function add($key, $value)
    {
        $this->register($key, $value);
        return $this->save($key, $value);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function save($name, $value): static
    {
        if (isset($_COOKIE[$name]) && $_COOKIE[$name]==$value) {
            return $this;
        }
        \setcookie($name, $value);
        \setcookie($name, $value, null, '/');
        $_COOKIE[$name] = $value;
        return $this;
    }


    /**
     *
     */
    public function persistCookies()
    {
        foreach ($this->getParameters() as $name => $value) {
            $this->save($name, $value);
        }
    }

    /**
     * @param $name
     * @return bool
     */
    public function flushCookie($name): bool
    {
        \setcookie($name, '', time() - 1000);
        \setcookie($name, '', time() - 1000, '/');
        return true;
    }

    /**
     * @return bool
     */
    public function flushCookies(): bool
    {
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                $this->flushCookie($name);
            }
        }
        return true;
    }

    /**
     * @delete all cookies
     */
    public function deleteAllCookies()
    {
        // unset cookies
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $cookie) {
                $parts = explode('=', $cookie);
                $name = trim($parts[0]);
                \setcookie($name, '', time() - 1000);
                \setcookie($name, '', time() - 1000, '/');
            }
        }
    }

}