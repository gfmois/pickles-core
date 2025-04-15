<?php

use Pickles\Http\Request;
use Pickles\Http\Response;
use Pickles\Kernel;

function json(array $data): Response
{
    return Response::json($data);
}

function redirect(string $uri): Response
{
    return Response::redirect($uri);
}

function view(string $view, array $data = [], ?string $layout = null): Response
{
    return Response::view($view, $data, $layout);
}

function request(): Request
{
    $appInstance = app();
    if (!$appInstance instanceof Kernel) {
        throw new \Exception('Kernel instance not found.');
    }

    return $appInstance->getRequest();
}
