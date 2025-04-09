<?php

namespace Pickles\Server;

use Pickles\Http\HttpMethod;
use Pickles\Http\Response;

/**
 * Interface Server
 *
 * Represents a wrapper around PHP's superglobal `$_SERVER`, allowing for better abstraction,
 * testability, and dependency injection.
 *
 * This interface provides methods to access HTTP request information and send a response.
 */
interface Server {
    /**
     * Retrieves the request URI (e.g., "/api/users?active=true").
     *
     * @return string The full request URI as a string.
     */
    public function requestUri(): string;

    /**
     * Retrieves the HTTP method used in the request (e.g., GET, POST).
     *
     * @return HttpMethod The HTTP method as an instance of HttpMethod enum/class.
     */
    public function requestMethod(): HttpMethod;

    /**
     * Retrieves the POST data sent with the request.
     *
     * @return array An associative array of POST parameters.
     */
    public function postData(): array;

    /**
     * Retrieves the query parameters from the request URI.
     *
     * @return array An associative array of query parameters.
     */
    public function queryParams(): array;

    /**
     * Sends the provided HTTP response to the client.
     *
     * @param Response $response The response object to be sent.
     * @return void
     */
    public function sendResponse(Response $response): void;
}
