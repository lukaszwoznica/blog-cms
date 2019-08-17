<?php

namespace Core;

class Router
{
    /**
     * The routes table
     */
    private $routes = [];

    /**
     * Parameters from matched route
     */
    private $params = [];

    public  function addRoute(string $route, array $params = []): void
    {
        // Convert the route to a regular expression: escape forward slashes
        $route = preg_replace('/\//', '\\/', $route);
        // Convert variables e.g. {controller}
        $route = preg_replace('/\{([a-z]+)\}/', '(?P<\1>[a-z-]+)', $route);
        // Convert variables with custom regular expressions e.g. {id:\d+}
        $route = preg_replace('/\{([a-z]+):([^\}]+)\}/', '(?P<\1>\2)', $route);
        // Add start and end delimiters, and case insensitive flag
        $route = '/^' . $route . '$/i';

        $this->routes[$route] = $params;
    }

    public function match(string $url): bool
    {
        foreach ($this->routes as $route => $params){
            if (preg_match($route, $url, $matches)){
                foreach ($matches as $key => $match){
                    if (is_string($key)){
                        $params[$key] = $match;
                    }
                }
                $this->params = $params;
                return true;
            }
        }
        return false;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }


    public function getParams(): array
    {
        return $this->params;
    }

    protected function convertToCamelCase(string $str, bool $capitalizeFirstChar = false): string
    {
        $cc_str = str_replace('-', '', ucwords($str, '-'));

        if (!$capitalizeFirstChar) {
            $cc_str = lcfirst($cc_str);
        }

        return $cc_str;
    }

    protected function removeQueryStringVariables(string $url): string
    {
        if($url != ''){
            $parts = explode('&', $url, 2);
            if (strpos($parts[0], '=') === false)
                $url = $parts[0];
            else
                $url = '';
        }

        return $url;
    }

    protected function getNamespace(): string
    {
        $namespace = 'App\Controllers\\';
        if (array_key_exists('namespace', $this->params))
            $namespace .= $this->params['namespace'] . '\\';

        return $namespace;
    }

    public function dispatch($url): void
    {
        $url = $this->removeQueryStringVariables($url);

        if ($this->match($url)){
            $controller = $this->params['controller'];
            $controller = $this->convertToCamelCase($controller, true);
            $controller = $this->getNamespace() . $controller;

            if (class_exists($controller)){
                $controller_object = new $controller($this->params);
                $action = $this->convertToCamelCase($this->params['action']);

                if (is_callable([$controller_object, $action]))
                    $controller_object->$action();
                else
                    echo "Method $action (in controller $controller) not found";
            } else {
                echo "Controller class $controller not found";
            }
        } else {
            echo 'No route matched.';
        }
    }
}
