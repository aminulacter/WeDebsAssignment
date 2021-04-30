<?php

namespace Core;

class Router
{
    protected $routes = [
        'GET' => [],
        'POST' => [],
        'OPTIONS' => []
    ];

    public static function load($file)
    {
        $router = new static;
        require $file;
        return $router;
    }


    public function get($uri, $controller)
    {
        $this->routes['GET'][$uri] = $controller;
    }
    public function post($uri, $controller)
    {
        $this->routes['POST'][$uri] = $controller;
        $this->routes['OPTIONS'][$uri] = $controller;
    }


    public function direct($uri, $requestType)
    {
        if (array_key_exists($uri, $this->routes[$requestType])) {
            // return $this->routes[$requestType][$uri];

            return $this->callAction(...explode('@', $this->routes[$requestType][$uri]));
        }

        throw new \Exception('No route defiend for this URI');
    }

    protected function callAction($controller, $action)
    {
        $controller = "App\\controller\\{$controller}";
        $controller = new $controller;
        if (!method_exists($controller, $action)) {
            throw new \Exception(
                "{$controller} does not respont to the {$action} action"
            );
        }

        return $controller->$action();
    }
}
