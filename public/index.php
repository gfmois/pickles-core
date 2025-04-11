<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;

require_once '../vendor/autoload.php';

$app = Kernel::bootstrap();

$app->router->get("/test/{param}", function(Request $request) {
    return Response::json(["result" => $request->getRouteParameters()]);
});

$app->router->post("/test", function(Request $request) {
    return Response::json(["result" => $request->getData()]);
});

$app->router->get("/redirect", function(Request $request) {
    return Response::redirect("/test/asdf");
});

$app->router->put('/test', function(Request $request) {
    return "PUT OK";
});

$app->router->patch('/test', function(Request $request) {
    return "PATCH OK";
});

$app->router->delete('/test', function(Request $request) {
    return "DELETE OK";
});

$app->run();