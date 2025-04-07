<?php

namespace Pickles;

use Closure;

class Router {
    protected array $routes = [];

    public function __construct() {
        foreach (HttpMethod::cases() as $method) {
            $this->routes[$method->value] = [];
        }
    }

    protected function registerRoute(HttpMethod $method, string $uri, Closure $action) {
        $this->routes[$method->value][] = new Route($uri, $action);
    }

    public function get(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::GET, $uri, $action);
    }

    public function post(string $uri, callable $action) {
        $this->registerRoute(HttpMethod::POST, $uri, $action);
    }

    public function put(string $uri, callable $action) {
        $this->registerRoute(HttpMethod::PUT, $uri, $action);
    }

    public function patch(string $uri, callable $action) {
        $this->registerRoute(HttpMethod::PATCH, $uri, $action);
    }

    public function delete(string $uri, callable $action) {
        $this->registerRoute(HttpMethod::DELETE, $uri, $action);
    }

    public function resolve(string $uri, string $method) {
        foreach($this->routes[$method] as $route) {
            if ($route->matches($uri)) {
                return $route;
            }
        }

        throw new HttpNotFoundException();
    }
}