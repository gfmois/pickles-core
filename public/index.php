<?php

require_once '../vendor/autoload.php';

use Pickles\HttpNotFoundException;
use Pickles\PhpNativeServer;
use Pickles\Request;
use Pickles\Route;
use Pickles\Router;
use Pickles\Server;

$router = new Router();

$router->get("/test", function() {
    return "OK";
});

$router->post("/test", function() {
    return "OK";
});

$router->put('/test', function() {
    return "PUT OK";
});

$router->patch('/test', function() {
    return "PATCH OK";
});

$router->delete('/test', function() {
    return "DELETE OK";
});


try {
    $method = $_SERVER["REQUEST_METHOD"];
    $uri = $_SERVER["REQUEST_URI"];

    $route = $router->resolve(new Request(new PhpNativeServer()));
    $action = $route->getAction();
    print($action());

    // $route = new Route("/test/{test}/user/{user}", fn() => "test");
    // var_dump($route->parseParameters("/test/1/user/gfmois"));
} catch (HttpNotFoundException $e) {
    print("Not found");
    http_response_code(404);
}