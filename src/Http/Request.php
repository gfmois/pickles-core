<?php

namespace Pickles\Http;

use Pickles\Server\Server;

/**
 * Class Request
 *
 * Encapsulates the HTTP request data retrieved from the Server implementation.
 * Provides access to URI, method, POST data, and query parameters.
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
     * Constructor.
     *
     * Initializes the request data by extracting it from the Server.
     *
     * @param Server $server The server implementation providing request information.
     */
    public function __construct(Server $server)
    {
        $this->uri = $server->requestUri();
        $this->method = $server->requestMethod();
        $this->data = $server->postData();
        $this->queryParams = $server->queryParams();
    }

    /**
     * Get the request URI path.
     *
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * Get the HTTP method used in the request.
     *
     * @return HttpMethod
     */
    public function getMethod(): HttpMethod
    {
        return $this->method;
    }

    /**
     * Get POST data
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Get all query parameters
     * @return string[]
     */
    public function getQuery(): array
    {
        return $this->queryParams;
    }
}
