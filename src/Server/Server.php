<?php

namespace Pickles\Server;

use Pickles\Http\HttpMethod;
use Pickles\Http\Response;

interface Server {
    public function requestUri(): string;
    public function requestMethod(): HttpMethod;
    public function postData(): array;
    public function queryParams(): array;
    public function sendResponse(Response $response): void;
}
