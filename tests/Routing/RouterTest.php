<?php

namespace Pickles\Tests\Routing;

use PHPUnit\Framework\TestCase;
use Pickles\Http\HttpMethod;
use Pickles\Http\Request;
use Pickles\Routing\Router;
use Pickles\Server\Server;

class RouterTest extends TestCase
{
    private function createMockRequest(string $uri, HttpMethod $method): Request {
        return (new Request())
            ->setUri($uri)
            ->setMethod($method);
    }

    public function test_resolve_basic_route_with_callback_action()
    {
        $uri = "/test";
        $action = fn () => "test";
        $router = new Router();

        $router->get($uri, $action);

        $route = $router->resolve($this->createMockRequest($uri, HttpMethod::GET));
        $this->assertEquals($action, $route->getAction());
    }

    public function test_resolve_multiple_basic_routes_with_callback_action()
    {
        $routes = [
            "/test" => fn () => "test",
            "/foo" => fn () => "foo",
            "/bar" => fn () => "bar",
            "/long/nested/route" => fn () => "long nested route"
        ];

        $router = new Router();

        foreach ($routes as $uri => $action) {
            $router->get($uri, $action);
        }

        foreach ($routes as $uri => $action) {
            $route = $router->resolve($this->createMockRequest($uri, HttpMethod::GET));
            $this->assertEquals($action, $route->getAction());
        }
    }

    public function test_resolve_multiple_basic_routes_with_callback_for_diferent_http_methods()
    {
        $routes = [
            [HttpMethod::GET, "/test", fn () => "GET"],
            [HttpMethod::POST, "/test", fn () => "POST"],
            [HttpMethod::DELETE, "/test", fn () => "DELETE"],
            [HttpMethod::PATCH, "/test", fn () => "PATCH"],
            [HttpMethod::PUT, "/test", fn () => "PUT"],

            [HttpMethod::GET, "/random/get", fn () => "GET"],
            [HttpMethod::POST, "/random/nested/post", fn () => "POST"],
            [HttpMethod::DELETE, "/delete/random/route/nested", fn () => "DELETE"],
            [HttpMethod::PATCH, "/some/patch/route", fn () => "PATCH"],
            [HttpMethod::PUT, "/this/is/a/put/route", fn () => "PUT"]
        ];

        $router = new Router();

        foreach ($routes as [$method, $uri, $action]) {
            $router->{strtolower($method->value)}($uri, $action);
        }

        foreach ($routes as [$method, $uri, $action]) {
            $route = $router->resolve($this->createMockRequest($uri, $method));
            $this->assertEquals($action, $route->getAction());
        }
    }
}
