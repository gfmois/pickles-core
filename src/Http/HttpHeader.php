<?php

namespace Pickles\Http;

/**
 * Enum HttpHeader
 *
 * Represents a subset of standard HTTP headers used in HTTP requests and responses.
 * Each case maps to the actual header name as a string.
 */
enum HttpHeader: string
{
    /**
     * The size of the request or response body, in bytes.
     */
    case CONTENT_LENGTH = "Content-Length";

    /**
     * The media type of the request or response body (e.g., application/json).
     */
    case CONTENT_TYPE = "Content-Type";

    /**
     * Used to redirect the client to a different URL.
     */
    case LOCATION = "Location";
}
