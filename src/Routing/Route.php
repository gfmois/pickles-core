<?php

namespace Pickles\Routing;

use Closure;

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
}
