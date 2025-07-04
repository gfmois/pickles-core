<?php

namespace Pickles\Tests\Http;

use PHPUnit\Framework\TestCase;
use Pickles\Http\HttpMethod;
use Pickles\Http\Request;

class RequestTest extends TestCase
{
    public function test_request_returns_data_obtained_from_server_correctly()
    {
        $uri = "/test/route";
        $queryParams = ["a" => 1, 'b' => 2, 'test' => 'bar'];
        $postData = ['post' => 'test', 'foo' => 'bar'];

        $request = (new Request())
            ->setData($postData)
            ->setUri($uri)
            ->setQueryParams($queryParams)
            ->setMethod(HttpMethod::POST);

        $this->assertEquals($uri, $request->uri());
        $this->assertEquals($queryParams, $request->queryParams());
        $this->assertEquals($postData, $request->data());
        $this->assertEquals(HttpMethod::POST, $request->method());
    }
}
