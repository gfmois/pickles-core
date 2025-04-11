<?php

namespace Pickles\Server;

use Pickles\Http\HttpMethod;
use Pickles\Http\Request;
use Pickles\Http\Response;

/**
 * Interface Server
 *
 * Represents a wrapper around PHP's superglobal `$_SERVER`, allowing for better abstraction,
 * testability, and dependency injection.
 *
 * This interface provides methods to access HTTP request information and send a response.
 */
interface Server
{
    /**
     * Get request sent by the client.
     *
     * @return Request
     */
    public function getRequest(): Request;

    /**
     * Sends the provided HTTP response to the client.
     *
     * @param Response $response The response object to be sent.
     * @return void
     */
    public function sendResponse(Response $response): void;
}
