<?php

use Pickles\Http\Middleware;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;
use Pickles\Routing\Route;

require_once '../vendor/autoload.php';

$app = Kernel::bootstrap();

$app->getRouter()->get("/test/{param}", function(Request $request) {
    return Response::json(["result" => $request->getRouteParameters()]);
});

$app->getRouter()->post("/test", function(Request $request) {
    return Response::json(["result" => $request->getData()]);
});

$app->getRouter()->get("/redirect", function(Request $request) {
    return Response::redirect("/test/asdf");
});

$app->getRouter()->put('/test', function(Request $request) {
    return "PUT OK";
});

$app->getRouter()->patch('/test', function(Request $request) {
    return "PATCH OK";
});

$app->getRouter()->delete('/test', function(Request $request) {
    return "DELETE OK";
});

class AuthMiddleware implements Middleware {
    public function handle(Request $request, Closure $next): Response {
        if ($request->getHeaders("authorization") != "asdf") {
            return Response::json(
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
            return Response::json(
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
    fn(Request $request) => Response::json(["result"=> "Authenticated"])
    )->setMiddlewares([AuthMiddleware::class, TestMiddleware::class]);

Route::GET("/html", fn(Request $request) => Response::view("home"));

$app->run();