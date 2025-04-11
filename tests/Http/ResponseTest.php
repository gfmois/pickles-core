<?php

namespace Pickles\Tests\Http;

use PHPUnit\Framework\TestCase;
use Pickles\Http\HttpHeader;
use Pickles\Http\Response;

class ResponseTest extends TestCase
{
    public function test_json_response_is_constructed_correctly()
    {
        $response = Response::json(["key" => "value"]);
        ;

        $this->assertEquals(200, $response->getStatus());
        print_r($response->getHeaders());
        $this->assertEquals("application/json", $response->getHeader(HttpHeader::CONTENT_TYPE->value));
        $this->assertJsonStringEqualsJsonString('{"key":"value"}', $response->getContent());
    }

    public function test_text_response_is_constructed_correctly()
    {
        $response = Response::text("Hello world!");
        $this->assertEquals(200, $response->getStatus());
        $this->assertEquals('text/plain', $response->getHeader(HttpHeader::CONTENT_TYPE->value));
        $this->assertEquals('Hello world!', $response->getContent());
    }

    public function test_redirect_response_is_constructed_correctly()
    {
        $response = Response::redirect("https://example.com");
        $this->assertEquals(302, $response->getStatus());
        $this->assertEquals('https://example.com', $response->getHeader(HttpHeader::LOCATION->value));
    }

    public function test_prepare_method_removes_content_headers_if_there_is_no_content()
    {
        $response = new Response();
        $response->prepare();

        $this->assertNull($response->getHeader(HttpHeader::CONTENT_LENGTH->value));
        $this->assertNull($response->getHeader(HttpHeader::CONTENT_TYPE->value));
    }

    public function test_prepare_method_adds_content_length_header_if_there_is_content()
    {
        $response = Response::text("Hello, world!");
        $response->prepare();

        $this->assertEquals(13, $response->getHeader(HttpHeader::CONTENT_LENGTH->value));
    }
}
