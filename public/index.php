<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;

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

$app->run();