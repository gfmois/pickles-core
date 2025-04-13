<?php

namespace Pickles\Tests\Routing;

use Closure;
use Exception;
use PHPUnit\Framework\TestCase;
use Pickles\Http\HttpMethod;
use Pickles\Http\Middleware;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;

class RouterTest extends TestCase
{
    private function createMockRequest(string $uri, HttpMethod $method): Request
    {
        return (new Request())
            ->setUri($uri)
            ->setMethod($method);
    }

    private function mockMiddleware(): Middleware
    {
        return new class() implements Middleware {
            private string $key;
            private string $value;

            public function handle(Request $request, Closure $next): Response
            {
                $response = $next($request);
                $response->setHeader($this->key, $this->value);
                return $response;
            }

            public function setConfiguration(string $key, string $value): void
            {
                $this->key = $key;
                $this->value = $value;
            }
        };
    }

    public function test_resolve_basic_route_with_callback_action()
    {
        $uri = "/test";
        $action = fn() => "test";
        $router = new Router();

        $router->get($uri, $action);

        $route = $router->resolveRoute($this->createMockRequest($uri, HttpMethod::GET));
        $this->assertEquals($action, $route->getAction());
    }

    public function test_resolve_multiple_basic_routes_with_callback_action()
    {
        $routes = [
            "/test" => fn() => "test",
            "/foo" => fn() => "foo",
            "/bar" => fn() => "bar",
            "/long/nested/route" => fn() => "long nested route"
        ];

        $router = new Router();

        foreach ($routes as $uri => $action) {
            $router->get($uri, $action);
        }

        foreach ($routes as $uri => $action) {
            $route = $router->resolveRoute($this->createMockRequest($uri, HttpMethod::GET));
            $this->assertEquals($action, $route->getAction());
        }
    }

    public function test_resolve_multiple_basic_routes_with_callback_for_diferent_http_methods()
    {
        $routes = [
            [HttpMethod::GET, "/test", fn() => "GET"],
            [HttpMethod::POST, "/test", fn() => "POST"],
            [HttpMethod::DELETE, "/test", fn() => "DELETE"],
            [HttpMethod::PATCH, "/test", fn() => "PATCH"],
            [HttpMethod::PUT, "/test", fn() => "PUT"],

            [HttpMethod::GET, "/random/get", fn() => "GET"],
            [HttpMethod::POST, "/random/nested/post", fn() => "POST"],
            [HttpMethod::DELETE, "/delete/random/route/nested", fn() => "DELETE"],
            [HttpMethod::PATCH, "/some/patch/route", fn() => "PATCH"],
            [HttpMethod::PUT, "/this/is/a/put/route", fn() => "PUT"]
        ];

        $router = new Router();

        foreach ($routes as [$method, $uri, $action]) {
            $router->{strtolower($method->value)}($uri, $action);
        }

        foreach ($routes as [$method, $uri, $action]) {
            $route = $router->resolveRoute($this->createMockRequest($uri, $method));
            $this->assertEquals($action, $route->getAction());
        }
    }

    public function test_run_middlewares()
    {
        $middleware1 = $this->mockMiddleware();
        $middleware2 = $this->mockMiddleware();

        $router = new Router();
        $uri = "/test";
        $expectedResponse = Response::text("test");

        $route = $router->get($uri, fn() => $expectedResponse);
        $route->setMiddlewares([$middleware1, $middleware2]);

        foreach ($route->getMiddlewares() as $key => $middleware) {
            $middleware->setConfiguration("x-testing-middlewares-{$key}", "Working-{$key}");
        }

        $response = $router->resolve($this->createMockRequest($uri, HttpMethod::GET));

        $this->assertEquals($expectedResponse, $response);
        $this->assertEquals($response->getHeaders("x-testing-middlewares-0"), "Working-0");
        $this->assertEquals($response->getHeaders("x-testing-middlewares-1"), "Working-1");
    }

    public function test_middleware_stack_can_be_stopped()
    {
        $breakMiddleware =  new class implements Middleware {
            public function handle(Request $request, Closure $next): Response
            {
                return Response::text("Stopped");
            }
        };

        $middleware = $this->mockMiddleware();

        $router = new Router();
        $uri = '/test';
        $unreachableResponse = Response::text("Unreachable");

        $route = $router->get(
            $uri,
            fn(Request $request) => $unreachableResponse
        )->setMiddlewares([$breakMiddleware, $middleware]);

        $route->getMiddlewares()[1]->setConfiguration('x-test', 'working');

        $response = $router->resolve($this->createMockRequest($uri, HttpMethod::GET));

        $this->assertEquals("Stopped", $response->getContent());
        $this->assertNull($response->getHeaders('x-test'));
    }
}
