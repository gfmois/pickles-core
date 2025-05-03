<?php

namespace Pickles\Http;

use Closure;
use Pickles\Http\Request;
use Pickles\Http\Response;

/**
 * Interface Middleware
 *
 * Represents a middleware component that processes an incoming HTTP request
 * and either passes it to the next middleware or returns a response.
 *
 * @package Pickles\Http
 */
interface Middleware
{
    /**
     * Handle an incoming HTTP request.
     *
     * @param Request $request The incoming HTTP request instance.
     * @param Closure $next The next middleware to call in the pipeline.
     * @return Response The HTTP response after processing the request.
     */
    public function handle(Request $request, Closure $next): Response;
}
