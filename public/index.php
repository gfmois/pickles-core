<?php

use Pickles\Http\HttpHeader;
use Pickles\Http\HttpNotFoundException;
use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Routing\Router;
use Pickles\Server\PhpNativeServer;

require_once '../vendor/autoload.php';

$router = new Router();

$router->get("/test", function(Request $request) {
    $response = new Response();
    $response->setHeader(HttpHeader::CONTENT_TYPE, "application/json");
    $response->setContent(json_encode(["message" => "GET OK"]));
    return $response;
});

$router->post("/test", function(Request $request) {
    return "OK";
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
    $request = new Request($server);
    $route = $router->resolve($request);
    $action = $route->getAction();
    $response = $action($request);
    $server->sendResponse($response);
    // $route = new Route("/test/{test}/user/{user}", fn() => "test");
    // var_dump($route->parseParameters("/test/1/user/gfmois"));
} catch (HttpNotFoundException $e) {
    $response = new Response();

    $response->setStatus(404);
    $response->setContent("Not Found");
    $response->setHeader(HttpHeader::CONTENT_TYPE, "text/plain");

    $server->sendResponse($response);
}