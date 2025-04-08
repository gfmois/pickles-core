<?php

namespace Pickles\Http;

enum HttpHeader: string {
    case CONTENT_LENGTH = "Content-Length";
    case CONTENT_TYPE = "Content-Type";
    case LOCATION = "Location";
}
