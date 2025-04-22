<?php

use Pickles\Http\Middleware;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;
use Pickles\Routing\Route;
use Pickles\Validation\Rule;
use Pickles\Validation\Rules\Required;
use Pickles\View\PicklesEngine;

require_once '../../vendor/autoload.php';

$app = Kernel::bootstrap();
$engine = $app->getViewEngine();
if (!$engine instanceof PicklesEngine) {
    throw new \RuntimeException("The view engine is not an instance of PicklesEngine.");
}

$engine->setViewsDir(__DIR__ . "/../views/");

Route::GET("/test/{param}", function(Request $request) {
    return json(["result" => $request->getRouteParameters()]);
});

Route::POST("/test", function(Request $request) {
    return Response::json(["result" => $request->getData()]);
});

Route::GET("/redirect", function(Request $request) {
    return Response::redirect("/test/asdf");
});

Route::PUT('/test', function(Request $request) {
    return "PUT OK";
});

Route::PATCH('/test', function(Request $request) {
    return "PATCH OK";
});

Route::DELETE('/test', function(Request $request) {
    return "DELETE OK";
});

class AuthMiddleware implements Middleware {
    public function handle(Request $request, Closure $next): Response {
        if ($request->getHeaders("authorization") != "asdf") {
            return json(
                [
                    "message" => "Not Authenticated!",
                    "status" => 401
                ]
            )->setStatus(401);
        }

        $response = $next($request);
        $response->setHeader("X-Custom-Header", "Working");

        return $response;
    }
}

class TestMiddleware implements Middleware {
    public function handle(Request $request, Closure $next): Response {
        if ($request->getHeaders("authorization") != "asdf") {
            return json(
                [
                    "message" => "Not Authenticated!",
                    "status" => 401
                ]
            )->setStatus(401);
        }

        $response = $next($request);
        $response->setHeader("X-Custom-Header-2", "Working");

        return $response;
    }
}

Route::GET(
    "/middleware", 
    fn(Request $request) => json(["result"=> "Authenticated"])
    )->setMiddlewares([AuthMiddleware::class, TestMiddleware::class]);

Route::GET("/html", fn(Request $request) => view("home", ["user" => "some user"]));

Route::POST("/validation", fn(Request $request) => json($request->validate([
    "test" => "required",
    "num" => "number",
    "email" => ["required_when:num,>,4"],
], [
    "email" => [
        Required::class => "Email is required",
    ]
    ])));

Route::GET("/session", function(Request $request) {
    // session()->flash("tests", "value");
    return json($_SESSION);
});

$app->run();