<?php

namespace Pickles\Routing;

use Closure;
use Pickles\Http\HttpMethod;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Routing\Route;

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

    public function post(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::POST, $uri, $action);
    }

    public function put(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::PUT, $uri, $action);
    }

    public function patch(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::PATCH, $uri, $action);
    }

    public function delete(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::DELETE, $uri, $action);
    }

    public function resolve(Request $request): Route | HttpNotFoundException {
        foreach($this->routes[$request->getMethod()->value] as $route) {
            if ($route->matches($request->getUri())) {
                return $route;
            }
        }

        throw new HttpNotFoundException();
    }
}
