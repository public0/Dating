<?php
namespace App;

use Exception;

class Router {
    private array $routes;

    function __construct(protected Container $container) 
    {
        
    }

    public function register(string $method, string $route, callable|array $action): self
    {
        $this->routes[$method]['/public'.$route] = $action;

        return $this;
    }

    public function get(string $route, callable|array $action): self
    {
        return $this->register('get', $route, $action);
    }

    public function post(string $route, callable|array $action): self
    {
        return $this->register('post', $route, $action);
    }

    public function routes(): array
    {
        return $this->routes;
    }

    public function resolve(string $uri, string $method)
    {
        $route = explode('?', $uri)[0];
        $action = $this->routes[$method][$route] ?? null;
        if (!$action) {
            throw new Exception('404 not found');
        }

        if(is_callable($action))
            return call_user_func($action);

        if(is_array($action)) {
            [$class, $method] = $action;
            if(class_exists($class)) {
                $class = new $class($this->container);

                if(method_exists($class, $method)) {
                    return call_user_func_array([$class, $method], []);
                }
            }
        }

        throw new Exception('404 not found');
    }
}