<?php

namespace Pickles;

class Request {
    protected string $uri;
    protected HttpMethod $method;
    protected array $data;
    protected array $queryParams;

    public function __construct(Server $server) {
        $this->uri = $server->requestUri();
        $this->method = $server->requestMethod();
        $this->data = $server->postData();
        $this->queryParams = $server->queryParams();
    }

    public function getUri(): string {
        return $this->uri;
    }

    public function getMethod(): HttpMethod {
        return $this->method;
    }
}
