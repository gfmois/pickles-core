<?php

namespace Pickles\Http;

enum HttpMethod: string {
    case GET = "GET";
    case POST = "POST";
    case DELETE = "DELETE";
    case PUT = "PUT";
    case PATCH = "PATCH";
}