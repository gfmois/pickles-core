<?php

namespace Pickles\Http;

/**
 * Enum HttpMethod
 *
 * Represents the supported HTTP methods used in routing and requests.
 * Matches standard HTTP verbs used in RESTful APIs.
 *
 * @package Pickles\Http
 */
enum HttpMethod: string
{
    case GET = "GET";
    case POST = "POST";
    case DELETE = "DELETE";
    case PUT = "PUT";
    case PATCH = "PATCH";
}
