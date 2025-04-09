<?php

namespace Pickles\Routing;

use Closure;
use Pickles\Http\HttpMethod;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Routing\Route;

/**
 * Class Router
 * 
 * A simple HTTP router that maps URIs and HTTP methods to handlers.
 * Supports typical RESTful HTTP methods and route resolution.
 */
class Router {

    /**
     * The registered HTTP routes, organized by HTTP method.
     *
     * @var array<string, Route[]> An associative array where each key is an HTTP method name (e.g. "GET")
     *                              and the value is an array of Route objects for that method.
     */
    protected array $routes = [];

    /**
     * Router constructor.
     *
     * Initializes the routes array for each available HTTP method.
     */
    public function __construct() {
        foreach (HttpMethod::cases() as $method) {
            $this->routes[$method->value] = [];
        }
    }

    /**
     * Registers a new route for a specific HTTP method.
     *
     * @param HttpMethod $method The HTTP method (GET, POST, etc.)
     * @param string $uri The route URI (e.g. "/users")
     * @param Closure $action The handler to be executed for this route
     * @return void
     */
    protected function registerRoute(HttpMethod $method, string $uri, Closure $action) {
        $this->routes[$method->value][] = new Route($uri, $action);
    }

    /**
     * Registers a GET route.
     *
     * @param string $uri
     * @param Closure $action
     * @return void
     */
    public function get(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::GET, $uri, $action);
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri
     * @param Closure $action
     * @return void
     */
    public function post(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::POST, $uri, $action);
    }

    /**
     * Registers a PUT route.
     *
     * @param string $uri
     * @param Closure $action
     * @return void
     */
    public function put(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::PUT, $uri, $action);
    }

    /**
     * Registers a PATCH route.
     *
     * @param string $uri
     * @param Closure $action
     * @return void
     */
    public function patch(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::PATCH, $uri, $action);
    }

    /**
     * Registers a DELETE route.
     *
     * @param string $uri
     * @param Closure $action
     * @return void
     */
    public function delete(string $uri, Closure $action) {
        $this->registerRoute(HttpMethod::DELETE, $uri, $action);
    }

    /**
     * Resolves a request to a matching route.
     *
     * @param Request $request The HTTP request instance.
     * @return Route The matched route instance.
     *
     * @throws HttpNotFoundException If no matching route is found for the request.
     */
    public function resolve(Request $request): Route {
        foreach($this->routes[$request->getMethod()->value] as $route) {
            if ($route->matches($request->getUri())) {
                return $route;
            }
        }

        throw new HttpNotFoundException();
    }
}
