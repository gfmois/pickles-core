<?php

namespace Pickles\Server;

use Pickles\Http\HttpHeader;
use Pickles\Http\HttpMethod;
use Pickles\Http\Response;

class PhpNativeServer implements Server {
    public function requestUri(): string {
        return parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
    }

    public function requestMethod(): HttpMethod {
        return HttpMethod::from($_SERVER["REQUEST_METHOD"]);
    }

    public function postData(): array {
        return $_POST;
    }

    public function queryParams(): array {
        return $_GET;
    }

    public function sendResponse(Response $response): void {
        // PHP sends Content-Type header by default,
        // it has to be removed if the response has no content.
        $_header = HttpHeader::CONTENT_TYPE->value;
        
        // Content-Type header cannot be removed unless it is set to some value before 
        header("$_header:None");
        header_remove(HttpHeader::CONTENT_TYPE->value);

        $response->prepare();
        http_response_code($response->getStatus());
        foreach ($response->getHedaers() as $header => $value) {
            header("$header:$value");
        }

        print($response->getContent());
    }
}
