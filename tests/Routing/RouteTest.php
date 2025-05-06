<?php

namespace Pickles\Tests\Routing;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Pickles\Routing\Route;

class RouteTest extends TestCase
{
    public function getRoutesWithNoParameters(): array
    {
        return [
            ['/'],
            ['/test'],
            ['/test/nested'],
            ['/test/nested/another'],
            ['/test/nested/another/time']
        ];
    }

    public function getRoutesWithParameters(): array
    {
        return [
            ['/test/{test}', "/test/1", ["test" => 1]],
            ['/users/{user}', "/users/2", ["user" => 2]],
            ['/test/{test}', "/test/string", ["test" => "string"]],
            ['/users/{user}', "/users/asdf", ["user" => "asdf"]],
            ['/test/user/{user}', "/test/user/3", ["user" => 3]],
            ['/test/{test}/user/{user}', "/test/1/user/3", ["test" => 1, "user" => 3]],
            ['/test/{test}/user/{user}/name/{name}', "/test/1/user/3/name/gfmois", ["test" => 1, "user" => 3, "name" => "gfmois"]],
        ];
    }

    /** @dataProvider getRoutesWithNoParameters */
    public function test_regex_without_parameters(string $uri)
    {
        $route = new Route($uri, fn () => "test");

        $this->assertTrue($route->matches($uri));
        $this->assertFalse($route->matches("$uri/extra/path"));
        $this->assertFalse($route->matches("/some/$uri/extra/path"));
        $this->assertFalse($route->matches("/random"));
    }

    /** @dataProvider getRoutesWithParameters */
    public function test_regex_with_parameters(string $definition, string $uri)
    {
        $route = new Route($definition, fn () => "test");

        $this->assertTrue($route->matches($uri));
        $this->assertFalse($route->matches("$uri/extra/path"));
        $this->assertFalse($route->matches("/some/$uri/extra/path"));
        $this->assertFalse($route->matches("/random"));
    }

    /** @dataProvider getRoutesWithNoParameters */
    public function test_regex_on_uri_that_ends_with_slash(string $uri)
    {
        $route = new Route($uri, fn () => "test");
        $this->assertTrue($route->matches("$uri/"));
    }

    /** @dataProvider getRoutesWithParameters */
    public function test_parse_parameters(string $definition, string $uri, array $expectedParameters)
    {
        $route = new Route($definition, fn () => "asdf");

        $this->assertTrue($route->hasParameters());
        $this->assertEquals($expectedParameters, $route->parseParameters($uri));
    }
}
