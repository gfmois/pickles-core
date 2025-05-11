<?php

namespace Pickles\Http;

use InvalidArgumentException;
use Pickles\Routing\Route;
use Pickles\Storage\File;
use Pickles\Validation\Validator;

/**
 * Class Request
 *
 * Encapsulates the HTTP request data retrieved from the Server implementation.
 * Provides access to URI, method, POST data, and query parameters.
 *
 * @package Pickles\Http
 */
class Request
{
    /**
     * The request URI path.
     *
     * @var string
     */
    protected string $uri;

    /**
     * Route match by URI
     *
     * @var Route
     */
    protected Route $route;

    /**
     * The HTTP method of the request.
     *
     * @var HttpMethod
     */
    protected HttpMethod $method;

    /**
     * POST data from the request.
     *
     * @var array<string, mixed>
     */
    protected array $data;

    /**
     * Query parameters from the request URI.
     *
     * @var array<string, string>
     */
    protected array $queryParams;

    /**
     * Request headers.
     * @var array<string, string>
     */
    protected array $headers = [];

    /**
     * Array that contains the Uploaded files.
     * @var array<string, File>
     */
    protected array $files = [];

    //MARK: Getters
    /**
     * Validates the request data against the provided validation rules.
     *
     * @param array<string, mixed> $validationRules An associative array of validation rules where the key is the field name
     *                                and the value is the validation rule(s) to apply.
     * @param array<string, string> $messages Optional. An associative array of custom error messages where the key is the rule
     *                        or field name and the value is the custom message.
     * @return array<string, mixed> An array containing the validation results, including any errors encountered.
     */
    public function validate(array $validationRules, array $messages = []): array
    {
        $validator = new Validator($this->data);
        return $validator->validate($validationRules, $messages);
    }

    /**
     * Get the request URI path.
     *
     * @return string
     */
    public function uri(): string
    {
        return $this->uri;
    }

    /**
     * Get the HTTP method used in the request.
     *
     * @return HttpMethod
     */
    public function method(): HttpMethod
    {
        return $this->method;
    }

    /**
     * Retrieves a specific item from the POST data or returns all POST data.
     *
     * If no key is provided, the entire POST data array will be returned.
     * If a key is provided but does not exist in the data, `null` will be returned.
     *
     * @param string|null $key The data key to retrieve, or null to get all data.
     * @return array|string|null The entire data array, a specific value, or null if the key is not set.
     *
     * @throws InvalidArgumentException If the provided key is not a string.
     */
    public function data(?string $key = null)
    {
        if (is_null($key)) {
            return $this->data;
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('POST data key must be a string or null.');
        }

        return $this->data[$key] ?? null;
    }

    /**
     * Get all query parameters
     *
     * @var string|null $key
     * @return string|string[]|null
     * @throws InvalidArgumentException when `$key` is not null or string
     */
    public function queryParams(?string $key = null): string|array|null
    {
        if (is_null($key)) {
            return $this->queryParams;
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('Query parameter key must be a string or null.');
        }

        return $this->queryParams[$key] ?? null;
    }

    /**
     * Get route match by URI of this request.
     *
     * @return  Route
     */
    public function route(): Route
    {
        return $this->route;
    }

    /**
     * Get all route params
     *
     * @param string|null $key
     * @throws InvalidArgumentException when `$key` is not a string
     * @return string|string[]|null
     */
    public function routeParams(?string $key = null)
    {
        $routeParams = $this->route->parseParameters($this->uri);
        if (is_null($key)) {
            return $routeParams;
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('Route parameter key must be a string or null.');
        }

        return $routeParams[$key] ?? null;
    }

    /**
     * Get all request headers or a specific header.
     *
     * @param string|null $key
     * @return  array<string,
     */
    public function headers(?string $key = null): string|array|null
    {
        if (is_null($key)) {
            return $this->headers;
        }

        if (!is_string($key)) {
            throw new InvalidArgumentException('Route headers key must be a string or null.');
        }

        return $this->headers[strtolower($key)] ?? null;
    }

    /**
     * Retrieves a file from the uploaded files array.
     *
     * @param string|null $key The key of the file to retrieve. If null, no specific file is targeted.
     * @return File|null The file object associated with the given key, or null if the key does not exist.
     */
    public function files(?string $key = null): ?File
    {
        return $this->files[$key] ?? null;
    }

    //MARK: Setters
    /**
     * Set request URI.
     *
     * @param  string  $uri  The request URI path.
     * @return self
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * Set the HTTP method of the request.
     *
     * @param  HttpMethod  $method  The HTTP method of the request.
     * @return  self
     */
    public function setMethod(HttpMethod $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * Set POST data
     *
     * @param  array<string, mixed>
     * @return  self
     */
    public function setData(array $data): self
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Set query parameters.
     *
     * @param  array<string, string> $queryParams
     * @return  self
     */
    public function setQueryParams(array $queryParams): self
    {
        $this->queryParams = $queryParams;
        return $this;
    }

    /**
     * Set route for this request.
     *
     * @param  Route  $route
     * @return  self
     */
    public function setRoute(Route $route): self
    {
        $this->route = $route;
        return $this;
    }

    /**
     * Set Request headers
     *
     * @param array<string, string> $headers
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $key => $value) {
            $this->headers[strtolower($key)] = $value;
        }

        return $this;
    }

    /**
     * Set uploaded files.
     *
     * @param array<string, File> $files
     * @return self
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;
        return $this;
    }
}
