<?php

require_once '../vendor/autoload.php';

use Pickles\HttpNotFoundException;
use Pickles\Router;

$router = new Router();

$router->get("/test", function() {
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

    $action = $router->resolve($uri, $method);
    print($action());
} catch (HttpNotFoundException $e) {
    print("Not found");
    http_response_code(404);
}