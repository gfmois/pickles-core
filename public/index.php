<?php

use Pickles\Http\HttpHeader;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;
use Pickles\Server\PhpNativeServer;

require_once '../vendor/autoload.php';

$router = new Router();

$router->get("/test/{param}", function(Request $request) {
    return Response::json(["result" => $request->getRouteParameters()]);
});

$router->post("/test", function(Request $request) {
    return Response::json(["result" => $request->getData()]);
});

$router->get("/redirect", function(Request $request) {
    return Response::redirect("/test");
});

$router->put('/test', function(Request $request) {
    return "PUT OK";
});

$router->patch('/test', function(Request $request) {
    return "PATCH OK";
});

$router->delete('/test', function(Request $request) {
    return "DELETE OK";
});


$server = new PhpNativeServer();
try {
    $request = $server->getRequest();
    $route = $router->resolve($request);
    $request->setRoute($route);
    $action = $route->getAction();
    $response = $action($request);
    $server->sendResponse($response);
} catch (HttpNotFoundException $e) {
    $server->sendResponse(Response::text("Not Found")->setStatus(404));
}