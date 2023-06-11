<?php

namespace Emma\Http\Router\Route;

use Emma\Http\Request\RequestInterface;
use Emma\Http\Router\Interfaces\RouteMatcherInterface;
use Emma\Http\Router\Interfaces\RouterStoreInterface;
use Emma\Http\Router\Interfaces\RouteInterface;

class RouteMatcher implements RouteMatcherInterface
{
    /**
     * @return Route|null
     * @throws \Exception
     */
    public function match(RequestInterface $httpRequest, RouterStoreInterface $httpRouter): ?RouteInterface
    {
        $routes = $httpRouter->getRoutes();
        $uri = $httpRequest->getUri();
        $params = (stripos($uri, "/") !== 0) ? "/" . $uri : $uri;
        $requestMethod = $httpRequest->getServer()->getRequestMethod();

        if (empty($routes[$requestMethod])) {
            return null;
        }
        if (array_key_exists($uri, $routes[$requestMethod])) {
            return new Route($uri, $uri, $routes[$requestMethod][$uri]);
        }
        elseif ($params != $uri && array_key_exists($params, $routes[$requestMethod])) {
            return new Route($params, $params, $routes[$requestMethod][$params]);
        }
        else {
            ksort($routes[$requestMethod]);
            foreach($routes[$requestMethod] as $regex => $fn) {
                $matchedRegex = $regex;
                $callBackParams = [];
                if(preg_match_all('/{+(.*?)}/',  $regex, $bracesMatches)) {
                    foreach($bracesMatches[1] as $m) {
                        if (strpos($m, ":") !== false) {
                            $temp = explode(":", $m);
                            $callBackParams[$temp[0]] = null;
                            $regex = str_replace(
                                [
                                    "[/{".$m."}]", //optional param
                                    "[{".$m."}]", //optional param
                                    "{".$m."}",
                                ],
                                [
                                    "(/".$temp[1].")?",
                                    "(".$temp[1].")?",
                                    "(".$temp[1].")",
                                ],
                                $regex
                            );
                        }
                        else {
                            $callBackParams[$m] = null;
                            $regex = str_replace(
                                [
                                    "[/{".$m."}]", //optional param
                                    "[{".$m."}]", //optional param
                                    "{".$m."}",
                                ],
                                [
                                    "(/(.+))?",
                                    "(.+)?",
                                    "(.+)",
                                ],
                                $regex
                            );
                        }
                    }
                }

                //Nested Optional Param[/{...}[/{...}]]
                if(preg_match_all('/\[\/+(.*?)\]/',  $regex, $optionalParamMatches)) {
                    foreach($optionalParamMatches[1] as $m) {
                        $regex = str_replace("[".$m."]", "(".$m.")?", $regex);
                    }
                }

                $regex = str_replace('/', '\/', $regex); //Escape /
                $is_match = preg_match('/^' . ($regex) . '$/', $params, $matches);
                if ($is_match) {
                    $matchedRoute = array_shift($matches);
                    $callBackParams = array_combine(array_keys($callBackParams), $matches);
                    return new Route($matchedRoute, $matchedRegex, $fn, $callBackParams);
                }
            }
        }
        return null;
    }

}