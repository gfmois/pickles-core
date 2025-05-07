<?php

namespace Pickles\Routing;

use Closure;
use Pickles\Http\Middleware;
use Pickles\Kernel;
use RuntimeException;

/**
 * Class Route
 *
 * Represents a route in the HTTP router. Stores the URI pattern and the action (handler) to execute.
 * Supports parametrized routes using `{param}` syntax and extracts parameters from matching URIs.
 */
class Route
{
    /**
     * The URI pattern defined for the route (e.g., /users/{id}).
     *
     * @var string
     */
    protected string $uri;

    /**
     * The action (handler) to execute when the route matches.
     *
     * @var Closure
     */
    protected Closure $action;

    /**
     * Regular expression derived from the URI pattern.
     *
     * @var string
     */
    protected string $regex;

    /**
     * List of parameter names defined in the URI (e.g., ["id"] for /users/{id}).
     *
     * @var string[]
     */
    protected array $parameters;

    /**
     * HTTP middlewares associated with this route.
     * @var Middleware[]
     */
    protected array $middlewares = [];

    /**
     * Constructor.
     *
     * @param string $uri The route URI definition, possibly with parameters.
     * @param Closure $action The handler to be executed for this route.
     */
    public function __construct(string $uri, Closure $action)
    {
        $this->uri = $uri;
        $this->action = $action;
        $this->regex = preg_replace("/\{([a-zA-Z]+)\}/", "([a-zA-Z0-9]+)", $uri);

        preg_match_all("/\{([a-zA-Z]+)\}/", $uri, $parameters);
        $this->parameters = $parameters[1];
    }

    /**
     * Get the URI definition for this route.
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * Get the handler associated with this route.
     *
     * @return Closure
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Check if the given URI matches this route's pattern.
     *
     * @param string $uri The request URI.
     * @return bool True if it matches; false otherwise.
     */
    public function matches(string $uri): bool
    {
        return preg_match("#^$this->regex/?$#", $uri);
    }

    /**
     * Determine whether this route has defined parameters.
     *
     * @return bool True if parameters exist; false otherwise.
     */
    public function hasParameters(): bool
    {
        return count($this->parameters) > 0;
    }

    /**
     * Parse and extract parameter values from the given URI.
     *
     * @param string $uri The URI to extract values from.
     * @return array<string, string> Associative array of parameter names and their values.
     */
    public function parseParameters(string $uri): array
    {
        preg_match("#^$this->regex$#", $uri, $arguments);
        return array_combine($this->parameters, array_slice($arguments, 1));
    }

    /**
     * Registers a GET route with the application's router.
     *
     * @param string $uri The URI pattern for the route.
     * @param Closure $action The action to be executed when the route is matched.
     * @return self Returns the current instance of the route.
     * @throws RuntimeException If the Kernel instance is not found in the container.
     */
    public static function get(string $uri, Closure $action): self
    {
        $kernel = app();
        if (!$kernel instanceof Kernel) {
            throw new RuntimeException('Kernel instance not found in the container.');
        }

        return $kernel->getRouter()->get($uri, $action);
    }

    /**
     * Registers a POST route with the specified URI and action.
     *
     * @param string $uri The URI pattern for the route.
     * @param Closure $action The action to be executed when the route is matched.
     * @return self Returns the current instance of the route for method chaining.
     * @throws RuntimeException If the Kernel instance is not found in the container.
     */
    public static function post(string $uri, Closure $action): self
    {
        $kernel = app();
        if (!$kernel instanceof Kernel) {
            throw new RuntimeException('Kernel instance not found in the container.');
        }

        return $kernel->getRouter()->post($uri, $action);
    }

    /**
     * Registers a new route that responds to HTTP PUT requests.
     *
     * @param string $uri The URI pattern for the route.
     * @param Closure $action The action to be executed when the route is matched.
     * @return self Returns the current instance of the route for method chaining.
     * @throws RuntimeException If the Kernel instance is not found in the container.
     */
    public static function put(string $uri, Closure $action): self
    {
        $kernel = app();
        if (!$kernel instanceof Kernel) {
            throw new RuntimeException('Kernel instance not found in the container.');
        }

        return $kernel->getRouter()->put($uri, $action);
    }

    /**
     * Registers a new PATCH route with the application's router.
     *
     * @param string $uri The URI pattern for the route.
     * @param \Closure $action The action to be executed when the route is matched.
     *
     * @throws \RuntimeException If the Kernel instance is not found in the container.
     *
     * @return self Returns the current instance for method chaining.
     */
    public static function patch(string $uri, Closure $action): self
    {
        $kernel = app();
        if (!$kernel instanceof Kernel) {
            throw new RuntimeException('Kernel instance not found in the container.');
        }

        return $kernel->getRouter()->patch($uri, $action);
    }

    /**
     * Registers a DELETE route with the specified URI and action.
     *
     * @param string $uri The URI pattern for the route.
     * @param \Closure $action The action to be executed when the route is matched.
     * @return self Returns the current instance of the route.
     * @throws \RuntimeException If the Kernel instance is not found in the container.
     */
    public static function delete(string $uri, Closure $action): self
    {
        $kernel = app();
        if (!$kernel instanceof Kernel) {
            throw new RuntimeException('Kernel instance not found in the container.');
        }

        return $kernel->getRouter()->delete($uri, $action);
    }

    public static function load(string $routesDirectory)
    {
        foreach (glob("$routesDirectory/*.php") as $routes) {
            require_once $routes;
        }
    }

    /**
     * Get HTTP middlewares associated with this route.
     *
     * @return Middleware[]
     */
    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }

    /**
     * Set HTTP middlewares for this route.
     *
     * @param string[] $middlewares Array of middleware class names.
     * @throws \RuntimeException If any of the provided classes are not valid middleware.
     * @return Route
     */
    public function setMiddlewares(array $middlewares): self
    {
        $not_middlewares = array_filter($middlewares, fn ($middleware) =>  !is_subclass_of($middleware, Middleware::class));
        if (count($not_middlewares) > 0) {
            throw new RuntimeException('Not all middlewares are valid: ' . implode(', ', $not_middlewares));
        }

        $this->middlewares = array_map(fn ($middleware) => new $middleware(), $middlewares);
        return $this;
    }

    /**
     * Checks if middlewares has been added to the current Route.
     *
     * @return bool Returns true if there are middlewares associated with the route, false otherwise.
     */
    public function hasMiddlewares(): bool
    {
        return count($this->middlewares) > 0;
    }
}
