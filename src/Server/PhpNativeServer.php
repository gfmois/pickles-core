<?php

namespace Pickles\Server;

use Pickles\Http\HttpHeader;
use Pickles\Http\HttpMethod;
use Pickles\Http\Request;
use Pickles\Http\Response;

/**
 * Class PhpNativeServer
 *
 * Implementation of the Server interface using PHP's native global variables (`$_SERVER`, `$_POST`, `$_GET`).
 * Acts as a bridge between the application and the PHP environment, allowing access to the request data
 * and sending responses through native PHP functions.
 */
class PhpNativeServer implements Server
{
    /**
     * @inheritDoc
     */
    public function getRequest(): Request
    {
        return (new Request())
            ->setUri(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH))
            ->setMethod(HttpMethod::from($_SERVER["REQUEST_METHOD"]))
            ->setData($_POST)
            ->setQueryParams($_GET);
    }

    /**
     * @inheritDoc
     */
    public function sendResponse(Response $response): void
    {
        // PHP sends Content-Type header by default,
        // it has to be removed if the response has no content.
        $_header = HttpHeader::CONTENT_TYPE->value;

        // Content-Type header cannot be removed unless it is set to some value before
        header("$_header:None");
        header_remove(HttpHeader::CONTENT_TYPE->value);

        $response->prepare();
        http_response_code($response->getStatus());
        foreach ($response->getHeaders() as $header => $value) {
            header("$header:$value");
        }

        print($response->getContent());
    }
}
